<?php

namespace Tidumper\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Process\Process;

class FetchCddbCommand extends Command
{
    /**
     * configure name, arguments and options
     */
    protected function configure() {
        $this
            ->setName('fetch-cddb')
            ->setDescription('Download tango related freedb.org database files')
            ->setDefinition(array(
                new InputOption('complete', null, InputOption::VALUE_NONE, 'Fetch the complete database, not just the update.'),
                new InputOption('year', 'y', InputOption::VALUE_OPTIONAL, 'Date to fetch: year', date('Y')),
                new InputOption('month', 'm', InputOption::VALUE_OPTIONAL, 'Date to fetch: month', date('m'))
            ))

            ->setHelp(<<<EOT
<info>./tidumper fetch-cddb</info>
    Downloads CDDB files from freedb.org with "Tango" as genre.
EOT
            )
        ;
    }

    /**
     * Initializes the command just after the input has been validated:
     * - convert year and month to freedb.org URL format
     * - prepare data dir
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        /* convert year and month */
        $year = (int) $input->getOption('year');
        if ($year < 100) {
            $year += 2000; // allow passing the year using two digits only
        }
        $input->setOption('year', sprintf('%4d', $year));
        $input->setOption('month', sprintf('%02d', $input->getOption('month')));

        /* prepare data dir */
        $this->get('filesystem')->mkdir($this->get('data_dir'), 0700);
    }



    /**
     * run command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $query = '';
        if ($input->getOption('complete')) {
            $query = sprintf('freedb-complete-%s%s01.tar.bz2',
                $input->getOption('year'),
                $input->getOption('month')
            );
        }
        else {
            $query = sprintf('freedb-update-%4d%02d01-%s%s01.tar.bz2',
                ('01' === $input->getOption('month') ? (int) $input->getOption('year') - 1 : $input->getOption('year')),
                ('01' === $input->getOption('month') ? 12 : (int) $input->getOption('month') - 1),
                $input->getOption('year'),
                $input->getOption('month')
            );
        }

        /* fetch remote file */
        $file = sys_get_temp_dir() . '/' . $query;
        if (!file_exists($file)) { // TODO use (better) caching
            $client = $this->get('client');
            $client->setBaseUrl($this->get('cddb_download_server'));
            $response = $client->get($query)->send();
            $stream = $response->getBody();
            file_put_contents($file, (string) $stream);
        }

        /* extract archive */
        $sourceDir = sys_get_temp_dir() . '/' . str_replace('.tar.bz2', '', $query);
        $this->get('filesystem')->mkdir($sourceDir, 0700);
        chdir($sourceDir);
        $process = new Process("tar xjf $file");
        $process->setTimeout(3600);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        /* find files with tango genre, copy from /tmp to data dir */
        $targetDir = $this->get('data_dir') . '/' . str_replace('.tar.bz2', '', $query);
        $this->get('filesystem')->mkdir($targetDir, 0777);

        $search = 'DGENRE=Tango';
        $finder = $this->get('finder');
        $finder
            ->files()
            ->in($sourceDir)
            ->contains($search)
            ;

        foreach ($finder as $file) {
            $this->get('filesystem')->mkdir($targetDir . '/' . $file->getRelativePath(), 0777);
            $this->get('filesystem')->copy($file, $targetDir . '/' . $file->getRelativePathname(), true);
        }
    }

}

