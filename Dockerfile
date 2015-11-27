FROM php:5.6-cli

RUN docker-php-ext-install mbstring && \
    curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD . /opt/graze/silex-trailing-slash-handler

WORKDIR /opt/graze/silex-trailing-slash-handler

CMD /bin/bash
