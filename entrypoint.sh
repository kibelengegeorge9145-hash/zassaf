#!/bin/sh

# Endesha migrations pindi container inapoanza
php artisan migrate --force

# Anzisha Apache server
exec apache2-foreground
