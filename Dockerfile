FROM php:8.2-apache

# Set main parameters
ARG BUILD_ARGUMENT_ENV=dev
ENV ENV=${BUILD_ARGUMENT_ENV}
ENV APP_HOME=/var/www/html
ARG HOST_UID=1000
ARG HOST_GID=1000
ENV USERNAME=www-data
ARG INSIDE_DOCKER_CONTAINER=1
ENV INSIDE_DOCKER_CONTAINER=${INSIDE_DOCKER_CONTAINER}
ARG XDEBUG_CONFIG=main
ENV XDEBUG_CONFIG=${XDEBUG_CONFIG}
ARG XDEBUG_VERSION=3.3.2
ENV XDEBUG_VERSION=${XDEBUG_VERSION}

# Check environment
RUN if [ "$BUILD_ARGUMENT_ENV" = "default" ]; then \
        echo "Set BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev" && exit 2; \
    elif [ "$BUILD_ARGUMENT_ENV" = "dev" ]; then \
        echo "Building development environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "test" ]; then \
        echo "Building test environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "staging" ]; then \
        echo "Building staging environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then \
        echo "Building production environment."; \
    else \
        echo "Set correct BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev. Available choices are dev, test, staging, prod." && exit 2; \
    fi

# Install dependencies and PHP extensions
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
      bash-completion \
      fish \
      procps \
      nano \
      git \
      unzip \
      libicu-dev \
      zlib1g-dev \
      libxml2 \
      libxml2-dev \
      libreadline-dev \
      supervisor \
      cron \
      sudo \
      libzip-dev \
      wget \
      librabbitmq-dev \
      openssl \                
      ca-certificates \        
    && pecl install amqp \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
      pdo_mysql \
      sockets \
      intl \
      opcache \
      zip \
    && docker-php-ext-enable amqp \
    && a2enmod rewrite \
    # Install Composer using the official installer
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    # Clean up to reduce image size
    && rm -rf /tmp/* /var/lib/apt/lists/* \
    && apt-get clean

# Copy Apache configuration
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# Set permissions for entrypoint script and make it executable
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set working directory and copy source files with appropriate ownership
WORKDIR ${APP_HOME}
COPY --chown=${USERNAME}:${USERNAME} . ${APP_HOME}/

# Switch to www-data user for runtime
USER ${USERNAME}

# Set entrypoint and default command
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]