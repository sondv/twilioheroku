#!/bin/bash

echo "install mysql"

# install mysql server
sudo yum -y install mysql mysql-devel mysql-server mysql-utilities

sudo /etc/rc.d/init.d/mysqld start

echo "create user & database"
mysql -h 127.0.0.1 -P 3306 -u root <<< "CREATE DATABASE cloud_voice_v2 CHARACTER SET utf8;"
mysql -h 127.0.0.1 -P 3306 -u root <<< "CREATE USER 'user1'@'%' IDENTIFIED BY 'oihuu2Ahcoov2iek';"
mysql -h 127.0.0.1 -P 3306 -u root <<< "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION; FLUSH PRIVILEGES;"
mysql -h 127.0.0.1 -P 3306 -u root <<< "GRANT ALL PRIVILEGES ON *.* TO 'user1'@'%' WITH GRANT OPTION; FLUSH PRIVILEGES;"
mysql -h 127.0.0.1 -P 3306 -u root <<< "GRANT ALL PRIVILEGES ON *.* TO user1@'127.0.0.1' IDENTIFIED BY 'oihuu2Ahcoov2iek' WITH GRANT OPTION;FLUSH PRIVILEGES;"
mysql -h 127.0.0.1 -P 3306 -u root <<< "GRANT ALL PRIVILEGES ON *.* TO user1@'localhost' IDENTIFIED BY 'oihuu2Ahcoov2iek' WITH GRANT OPTION;FLUSH PRIVILEGES;"
