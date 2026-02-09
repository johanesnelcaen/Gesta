#!/bin/sh

set -e

# Créer le fichier SQLite s'il n'existe pas
if [ ! -f /app/database/database.sqlite ]; then
  touch /app/database/database.sqlite
fi

# Donner les permissions
chmod -R 775 /app/database

# Lancer les migrations
php artisan migrate --force
