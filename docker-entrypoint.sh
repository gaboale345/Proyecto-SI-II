#!/bin/bash
set -e

# Setup .env if not present or configure DB settings for Docker
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

# Ensure database settings match Docker Compose service
sed -i 's/^DB_HOST=.*/DB_HOST=db/' .env
sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=hospital_plan3000/' .env
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=root/' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=root/' .env

# Install composer dependencies if not already installed
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Generate app key if not set
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "Generating Application Key..."
    php artisan key:generate --force
fi

# Install npm dependencies and build assets if build folder doesn't exist
if [ ! -d "public/build" ]; then
    echo "Building frontend assets with Vite..."
    npm install
    npm run build
fi

# Wait for MySQL to be ready
echo "Waiting for MySQL database connection..."
until php -r "try { new PDO('mysql:host=db;port=3306;dbname=hospital_plan3000', 'root', 'root'); exit(0); } catch (Exception \$e) { exit(1); }"; do
    sleep 2
done
echo "MySQL is up and ready!"

# Run migrations automatically
echo "Running database migrations..."
php artisan migrate --force

echo "Setup complete. Starting Laravel..."
exec "$@"
