#!/bin/bash

# Installer les dépendances Composer
composer install

# Créer le fichier .env s'il n'existe pas
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Générer la clé d'application Laravel
php artisan key:generate

# Assurer que le répertoire de stockage a les permissions appropriées
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Effacer la configuration du cache
php artisan config:cache

# Effacer la vue cache
php artisan view:clear

# Effacer la cache de l'application
php artisan cache:clear

echo "L'application Laravel est configurée avec succès !"
echo "/!\ il faut run les migrations"