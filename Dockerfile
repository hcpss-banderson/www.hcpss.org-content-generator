FROM banderson/symfony:base

COPY symfony/bin           /var/www/symfony/bin
COPY symfony/config        /var/www/symfony/config
COPY symfony/public        /var/www/symfony/public
COPY symfony/src           /var/www/symfony/src
COPY symfony/templates     /var/www/symfony/templates
COPY symfony/.env          /var/www/symfony/.env
COPY symfony/composer.json /var/www/symfony/composer.json
COPY symfony/composer.lock /var/www/symfony/composer.lock
COPY symfony/symfony.lock  /var/www/symfony/symfony.lock

RUN composer install

COPY entrypoint.sh /entrypoint.sh
ENTRYPOINT [ "/entrypoint.sh" ]

CMD [ "/var/www/symfony/bin/console", "list", "content" ]
