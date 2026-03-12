# Stage 1: Runtime App (PHP-FPM)
FROM php:8.3-fpm AS app

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www/html

# Assign permissions of the working directory to the www-data user
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

# Stage 2: Node Build
FROM node:20 AS node
WORKDIR /var/www/html
COPY package.json package-lock.json ./
RUN npm install
COPY . .
# Keep it alive for local development (npm run dev inside compose)
CMD ["npm", "run", "dev"]

# Stage 3: Nginx Web Server
FROM nginx:alpine AS nginx
WORKDIR /var/www/html
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY . /var/www/html
# If building for prod, we would copy from the node stage:
# COPY --from=node /var/www/html/public/build /var/www/html/public/build
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
