FROM        mysql:5.7
MAINTAINER  Arnaud Ponel <arnaud@xpressive.io>

RUN         usermod -u 1000 mysql
RUN         groupmod -g 1000 mysql

RUN         chown -R mysql:mysql /var/lib/mysql
