FROM php:8.2-fpm

# Cài thêm extension cần thiết
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libonig5 \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set thư mục làm việc
WORKDIR /var/www

# Copy project vào container
COPY . .

# Cấp quyền cho storage và bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]

