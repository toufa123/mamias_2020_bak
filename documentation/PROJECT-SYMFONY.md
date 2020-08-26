[<-- Back to main section](../README.md)

# Running SYMFONY

## Create SYMFONY project

```bash
make create symfony
```

And change `DOCUMENT_ROOT` and `DOCUMENT_ROOT` in `etc/environment*.yml`:

    DOCUMENT_ROOT=/app/public/
    DOCUMENT_INDEX=index.php

## SYMFONY cli runner

You can run one-shot command inside the `main` service container:

```bash
docker-compose run --rm app php /bin/console
docker-compose run --rm app bash
```

Webserver is available at Port 8000
