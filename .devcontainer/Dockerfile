FROM mcr.microsoft.com/devcontainers/base:ubuntu-24.04

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
        composer \
        gettext \
        ruby \
        php8.3-cli \
        php8.3-curl \
        php8.3-gd \
        php8.3-gettext \
        php8.3-intl \
        php8.3-mbstring \
        php8.3-mysql \
        php8.3-pcov \
        php8.3-pgsql \
        php8.3-xml \
        php8.3-zip \
        unzip \
    && rm -rf /var/lib/apt/lists/*
