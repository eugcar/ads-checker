FROM php:7.4-cli

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y git zip unzip