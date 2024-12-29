FROM nginx:stable-alpine

USER root

RUN sed -i "s/user  nginx/user laravel/g" /etc/nginx/nginx.conf

ADD nginx/default.conf /etc/nginx/conf.d/

RUN mkdir -p /var/www/html