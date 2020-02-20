FROM        nginx:mainline-alpine
MAINTAINER  Arnaud Ponel <arnaud@xpressive.io>

RUN         apk add --no-cache \
            --repository http://dl-cdn.alpinelinux.org/alpine/edge/main \
            --repository  http://dl-cdn.alpinelinux.org/alpine/edge/community \
            openssl shadow

RUN         usermod -u 1000 nginx && \
            groupmod -g 1000 nginx

COPY        ./docker/services/web/conf.d /etc/nginx/conf.d
VOLUME      /etc/nginx/conf.d

RUN         mkdir -p /etc/nginx/certs
RUN         openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
                -out /etc/nginx/certs/server.crt \
                -keyout /etc/nginx/certs/server.key \
                -subj "/C=FR/ST=PACA/L=TOULON/O=XPRESSIVE/OU=DEV DEPARTMENT/CN=*"
RUN         openssl dhparam -out /etc/nginx/certs/server.pem 2048

RUN         mkdir -p /app

EXPOSE      80 443
VOLUME      /app
WORKDIR     /app
