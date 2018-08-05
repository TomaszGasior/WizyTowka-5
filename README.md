WizyTówka 5
===

Simple PHP-written content management system for small websites. Currently in development. Licensed on 3-clause BSD license.

The repository is divided into four separate directories:

* `code` — main code directory; only files from here are needed to build releases and run this application;
* `tests` — unit tests, based on PHP Unit framework;
* `docs` — documentation files, written in Polish using Markdown files; there is no inline code documentation;
* `others` — additional files: command line utility scripts, some configuration files, artworks.

The user interface supports Polish only as this project is indented to be used by Polish websites.

The application requires PHP 7.1 or newer. SQLite, MySQL and PostgreSQL databases are supported. Tested on Linux and FreeBSD, with Apache and nginx. Other parts of this repository can require the newest PHP version. Unit tests require xdebug extension.