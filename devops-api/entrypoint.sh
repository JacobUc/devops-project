composer install

echo "Esperando a que MySQL esté disponible..."
until mysql -h $DB_HOST -u$DB_USERNAME -p$DB_PASSWORD -e "SELECT 1;" &> /dev/null
do
  sleep 2
done

# Crear la base de datos si no existe
mysql -h $DB_HOST -u$DB_USERNAME -p$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS ${DB_DATABASE};"

# Migraciones y ejecución de Laravel
php artisan migrate --force
php artisan storage:link
php artisan serve --host=0.0.0.0 --port=8000
