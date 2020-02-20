FROM        node:lts-alpine
MAINTAINER  Arnaud Ponel <arnaud@xpressive.io>

RUN         mkdir -p /build
COPY        ./docker/services/node /build

RUN         mkdir -p /app

VOLUME      /app
WORKDIR     /app

ENV         NODE_ENV=development

ENTRYPOINT  ["/build/entrypoint.sh"]
CMD         ["node"]
