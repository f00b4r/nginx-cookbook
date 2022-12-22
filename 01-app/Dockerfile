################################################################################
# Base #########################################################################
################################################################################

FROM debian:bullseye-slim as builder

ARG TARGETARCH

# ENV
ENV DEBIAN_DISTRO=bullseye
ENV PHP_DIR=/etc/php/8.1
ENV PHP_MODS_DIR=${PHP_DIR}/mods-available
ENV PHP_CLI_DIR=${PHP_DIR}/cli
ENV PHP_CLI_CONF_DIR=${PHP_CLI_DIR}/conf.d
ENV PHP_FPM_DIR=${PHP_DIR}/fpm
ENV PHP_FPM_CONF_DIR=${PHP_FPM_DIR}/conf.d
ENV PHP_FPM_POOL_DIR=${PHP_FPM_DIR}/pool.d
ENV NGINX_VERSION=1.22.0
ENV TZ=Europe/Prague
ENV DEBIAN_FRONTEND=noninteractive
ENV TINI_VERSION=v0.19.0
ENV LC_ALL=en_US.UTF-8
ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US.UTF-8

# INSTALLATION
RUN apt-get update && apt-get dist-upgrade -y && \
    # DEPENDENCIES #############################################################
    apt-get install -y wget curl apt-transport-https lsb-release ca-certificates gnupg2 git unzip make locales && \
    # LOCALE ###################################################################
    echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen && \
    dpkg-reconfigure --frontend=noninteractive locales && \
    update-locale LANG=en_US.UTF-8 && \
    # TIMEZONE #################################################################
    echo ${TZ} > /etc/timezone && \
    dpkg-reconfigure --frontend=noninteractive tzdata && \
    # APT ######################################################################
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb https://packages.sury.org/php/ ${DEBIAN_DISTRO} main" > /etc/apt/sources.list.d/php.list && \
    wget -O- http://nginx.org/keys/nginx_signing.key | apt-key add - && \
    echo "deb http://nginx.org/packages/debian/ ${DEBIAN_DISTRO} nginx" > /etc/apt/sources.list.d/nginx.list && \
    echo "deb-src http://nginx.org/packages/debian/ ${DEBIAN_DISTRO} nginx" >> /etc/apt/sources.list.d/nginx.list && \
    apt-get update && \
    # NGINX ####################################################################
    apt-get install -y --no-install-recommends nginx=${NGINX_VERSION}* && \
    ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stderr /var/log/nginx/error.log && \
    # PHP ######################################################################
    apt-get update && \
    apt-get install -y --no-install-recommends \
    php8.1-cli \
    php8.1-curl \
    php8.1-mbstring \
    php8.1-fpm && \
    # COMPOSER #################################################################
    curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    # TINY #####################################################################
    curl -sSL https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini-${TARGETARCH} --output /tini && \
    chmod +x /tini && \
    # CLEAN UP #################################################################
    apt-get clean -y && \
    apt-get autoclean -y && \
    apt-get remove -y wget gnupg2 && \
    apt-get autoremove -y

# APP SETUP
WORKDIR /srv

# TINI
ENTRYPOINT ["/tini", "--"]

################################################################################
# App ##########################################################################
################################################################################

FROM builder

# APPLICATION
ADD app /srv

# SETUP
RUN rm -rf /etc/nginx/conf.d/* && \
    rm -rf /var/lib/log/* && \
    rm -rf /tmp/* && \
    rm -rf /var/tmp/* && \
    mkdir -p /tmp/nginx && \
    mkdir -p /etc/nginx/site.d

# NGINX
ADD nginx/nginx.conf /etc/nginx/
ADD nginx/mime.types /etc/nginx/
ADD nginx/site.conf /etc/nginx/site.d/site.conf

# PHP
ADD php/php-fpm.conf ${PHP_DIR}
ADD php/app.ini ${PHP_CLI_CONF_DIR}

COPY --chmod=777 entrypoint.sh /entrypoint.sh
CMD [ "/entrypoint.sh" ]