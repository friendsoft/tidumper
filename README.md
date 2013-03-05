# Tidumper

Dump audio meta data from and to tango.info.

## Installation

1. Download and install Composer:

    curl -s http://getcomposer.org/installer | php

2. Install dependencies:

    php composer.phar install

*Note: `tar` must be installed on the system running PHP

## Commands

Tidumper is console-based. To list all commands, run from within the project root:

    ./tidumper

For help on a specific command, add `--help` to it:

    ./tidumper fetch-cddb --help

## Fetch CDDB

Examples on fetching tango related CDDB entries.

Fetch latest complete database (aware, files are large):

    ./tidumper fetch-cddb --complete

Fetch latest monthly update:

    ./tidumper fetch-cddb

Fetch monthly update released in December 2012:

    ./tidumper fetch-cddb --year=2012 --month=12

