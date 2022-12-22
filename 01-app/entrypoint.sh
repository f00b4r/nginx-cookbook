#!/usr/bin/env bash
set -e

/usr/sbin/php-fpm8.1 -F -R -y /etc/php/8.1/php-fpm.conf &
/usr/sbin/nginx -g "daemon off;" &
wait -n
