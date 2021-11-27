FROM alpine:latest

ENV KEEWEB_VERSION=1.18.7
ENV KEEWEB_LOCAL_SERVER_VERSION=0.0.1


RUN apk update
RUN apk add --no-cache php7-cli php7-json php7-phar bash wget unzip php7-iconv php7-mbstring php7-openssl php7-session

RUN mkdir -p /opt/app/www \
  && cd /opt/app/www \
  && wget https://github.com/keeweb/keeweb/releases/download/v${KEEWEB_VERSION}/KeeWeb-${KEEWEB_VERSION}.html.zip \
  && unzip KeeWeb-${KEEWEB_VERSION}.html.zip \
  && unlink KeeWeb-${KEEWEB_VERSION}.html.zip \
  && sed -i 's/content="(no-config)"/content="config.json"/' index.html 

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY app/composer.json /opt/app/
RUN cd /opt/app && composer install

ADD app /opt/app

RUN mkdir -p /opt/app/www && mkdir -p /opt/store/databases && mkdir -p /opt/store/backup && chmod 0777 /opt/store/databases /opt/store/backup 
RUN chmod +x /opt/app/entrypoint.sh


EXPOSE 8080

WORKDIR /opt/app/www

ENTRYPOINT ["/opt/app/entrypoint.sh"]

CMD ["php", "-S", "0.0.0.0:8080"]
