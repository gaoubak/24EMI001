#!/usr/bin/env bash
 
composer install -n
php bin/console doctrine:schema:update --force
php bin/console  app:sync-vehicleCatalogue-csv
exec "$@"






