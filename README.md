# Tidumper

Dump audio meta data from and to tango.info.

## Installation

1. Download and install Composer:

    curl -s http://getcomposer.org/installer | php

2. Install dependencies:

    php composer.phar install

*Note: [PHP with bz2] is needed to get the freedb.org data fetched.*

## Commands

Tidumper is console-based. To list all commands, run from within the project root:

    ./tidumper

For help on a specific command, add `--help` to it:

    ./tidumper fetch-cddb --help


[PHP with bz2]: <http://www.php.net/manual/en/bzip2.installation.php>

