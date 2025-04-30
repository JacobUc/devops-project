
composer install

echo "Esperando a que MySQL esté disponible..."
until mysql -h $DB_HOST -u$DB_USERNAME -p$DB_PASSWORD -e "SELECT 1;" &> /dev/null
do
  sleep 2
done

php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=8000
