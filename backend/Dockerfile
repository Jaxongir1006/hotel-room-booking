# --- Stage 1: PHP deps + wayfinder type generation ---
FROM php:8.4-fpm-alpine AS php-builder

WORKDIR /var/www/html

RUN apk add --no-cache git curl libzip-dev zip unzip oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
# --no-scripts skips package:discover which needs the full app present
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY . .

# Generate wayfinder TypeScript types (reads routes only, no DB needed)
RUN cp .env.example .env \
    && php artisan key:generate --no-interaction \
    && php artisan package:discover --ansi \
    && php artisan wayfinder:generate --with-form

# --- Stage 2: Build frontend assets ---
FROM node:22-alpine AS node-builder

WORKDIR /app

# Install PHP so the wayfinder Vite plugin can call `php artisan wayfinder:generate` during build
RUN apk add --no-cache php84 php84-pdo php84-pdo_mysql php84-mbstring php84-tokenizer \
    php84-xml php84-simplexml php84-xmlwriter php84-dom php84-openssl php84-session \
    php84-fileinfo php84-ctype php84-zip php84-iconv php84-pcntl php84-bcmath php84-curl \
    && ln -sf /usr/bin/php84 /usr/bin/php

COPY package.json pnpm-workspace.yaml ./
RUN npm install -g pnpm && pnpm install --no-frozen-lockfile

COPY . .

# Copy vendor and .env from php-builder so artisan can boot
COPY --from=php-builder /var/www/html/vendor ./vendor
COPY --from=php-builder /var/www/html/.env ./.env

RUN pnpm run build


# --- Stage 3: PHP runtime ---
FROM php:8.4-fpm-alpine AS app

WORKDIR /var/www/html

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY --from=php-builder /var/www/html/vendor ./vendor
COPY . .

# Copy built frontend assets
COPY --from=node-builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
