![License MIT](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)

This is an easy customizable docker boilerplate for any PHP-based projects like _Symfony Framework_, _laravel_,  and many other frameworks or applications.It was used to develop and deploy MAMIAS web Application.

Supports:

- Nginx or Apache HTTPd
- PHP-FPM (with Xdebug)
- MySQL, MariaDB or PerconaDB
- PostgreSQL with postGIS
- Solr (disabled, without configuration)
- Elasticsearch (disabled, without configuration)
- Redis (disabled)
- Memcached (disabled)
- Maildev
- FTP server (vsftpd)
- pgAdmin
- maybe more later...

This Docker boilerplate is based on the [Docker best practices](https://docs.docker.com/articles/dockerfile_best-practices/) and doesn't use too much magic. Configuration of each docker container is available in the `docker/` directory - feel free to customize.

This boilerplate can also be used for any other web project. Just customize the makefile for your needs.

## Table of contents

- [First steps / Installation and requirements](/documentation/INSTALL.md)
- [Updating docker boilerplate](/documentation/UPDATE.md)
- [Customizing](/documentation/CUSTOMIZE.md)
- [Services (Webserver, MySQL... Ports, Users, Passwords)](/documentation/SERVICES.md)
- [Docker Quickstart](/documentation/DOCKER-QUICKSTART.md)
- [Run your project](/documentation/DOCKER-STARTUP.md)
- [Container detail info](/documentation/DOCKER-INFO.md)
- [Troubleshooting](/documentation/TROUBLESHOOTING.md)
- [Changelog](/CHANGELOG.md)

