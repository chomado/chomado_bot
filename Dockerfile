FROM centos:centos7
MAINTAINER chomado <chomado@gmail.com>

RUN yum update -y && \
    yum install -y \
        scl-utils \
        http://ftp.tsukuba.wide.ad.jp/Linux/fedora/epel/7/x86_64/e/epel-release-7-5.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/rh-php56/epel-7-x86_64/download/rhscl-rh-php56-epel-7-x86_64.noarch.rpm \
            && \
    yum install -y \
        cronie \
        curl \
        rh-php56-php-cli \
        rh-php56-php-intl \
        rh-php56-php-mbstring \
            && \
    yum clean all

RUN useradd chomadocker
ADD . /home/chomadocker/bot
RUN mkdir /home/chomadocker/bot/runtime; \
    chown -R chomadocker:chomadocker /home/chomadocker
 
USER chomadocker
WORKDIR /home/chomadocker/bot
RUN scl enable rh-php56 'curl -sS https://getcomposer.org/installer | php && php composer.phar install --no-dev --prefer-dist'

USER root
WORKDIR /
ADD docker/cron/ /etc/cron.d/

CMD /usr/sbin/crond -n
