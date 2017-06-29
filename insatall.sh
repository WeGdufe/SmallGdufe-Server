#!/bin/bash 
echo "======= 开始安装redis ======="
yum -y install epel-release
yum -y install redis
redis-server /usr/local/redis/etc/redis.conf
echo "======= redis安装运行完毕， ps aux | grep redis 应能看到进程 ======="

echo "======= 开始安装composer ======="
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer config -g repo.packagist composer https://packagist.phpcomposer.com
yum -y install php-mbstring php-xml
composer install
echo "======= composer和依赖库安装完毕 ======="
