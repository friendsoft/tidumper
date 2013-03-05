<?php

namespace Tidumper\Console;

use ArrayAccess as ServicesInterface;
use Symfony\Component\Console\Application as BaseConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Tidumper\Command;

/**
 * The console application that handles the commands
 */
class Console extends BaseConsole
{
    /**
     * service container, e. g. Silex application / Pimple
     * renamed to "services" to avoid confusion with Symfony\Component\Console\Application
     *
     * @var ServicesInterface
     */
    protected $services;

    /**
     * pre-define the console application name
     */
    public function __construct() {
        parent::__construct('Tidumper', 'dev');
    }

    /**
     * set services (e. g. silex application / pimple service container)
     *
     * @param ServicesInterface $services
     * @return Console
     */
    public function setServices(ServicesInterface $services) {
        $this->services = $services;

        return $this;
    }

    /**
     * get services
     *
     * @return ServicesInterface
     */
    public function getServices() {
        return $this->services;
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $output) {
            $styles['highlight'] = new OutputFormatterStyle('red');
            $styles['warning'] = new OutputFormatterStyle('black', 'yellow');
            $styles['success'] = new OutputFormatterStyle('green');
            $formatter = new OutputFormatter(null, $styles);
            $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
        }

        return parent::run($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->registerCommands();

        return parent::doRun($input, $output);
    }

    /**
     * registers all commands
     * injects application into command to make services available
     */
    protected function registerCommands()
    {
        $this->add(new Command\FetchCddbCommand('fetch-cddb', $this->getServices()));
    }

}
