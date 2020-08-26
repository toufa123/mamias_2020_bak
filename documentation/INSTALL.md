[<-- Back to main section](../README.md)

# Installation

## Requirements

- GNU/Linux with Docker (recommendation: [Vagrant](https://www.vagrantup.com/downloads.html) VM with Docker or [native Linux with Docker](http://docs.docker.com/linux/step_one/)
- [make](https://en.wikipedia.org/wiki/Make_(software)), [GNU manual for make](https://www.gnu.org/software/make/manual/make.html)
- [composer](https://getcomposer.org/)
- [docker-compose](https://github.com/docker/compose)


For more convenience use [CliTools.phar](https://github.com/webdevops/clitools) (will also run on native Linux, not only inside a Vagrant box)

## First startup

```bash
git clone https://github.com/toufa123/mamias.git mamias

cd mamias

# copy favorite docker-compose.*.yml to docker-compose.yml
cp docker-compose.development.yml docker-compose.yml

docker-compose up -d --build
```
