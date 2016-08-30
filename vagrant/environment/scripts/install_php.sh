#!/bin/bash

echo "Install PHP"

## Install PHP 5.6.x ##
sudo yum -y --enablerepo=remi,remi-php56 install php php-common

## Install PHP 5.6.x modules ##
sudo yum -y --enablerepo=remi,remi-php56 install php-cli php-pear php-pdo php-mysqlnd php-mbstring php-mcrypt php-xml

##  Set up php.ini
echo "Configure php.ini file"

grep '^date.timezone = Asia/Tokyo' /etc/php.ini
if [ $? -eq 1 ]; then
cat >> /etc/php.ini << "EOF"
date.timezone = Asia/Tokyo
mbstring.language = Japanese
mbstring.internal_encoding = UTF-8
mbstring.http_input = auto
mbstring.http_output = UTF-8
mbstring.detect_order = UTF-8,EUC-JP,SJIS,JIS,ASCII
EOF
fi
