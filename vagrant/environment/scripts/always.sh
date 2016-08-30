#!/bin/bash

# Stop swap
sudo swapoff -a

# Stop Firewall
sudo /etc/rc.d/init.d/iptables stop

# Start MySql
sudo /etc/rc.d/init.d/mysqld start

# Start Apache
sudo /etc/rc.d/init.d/httpd start


cd /vagrant/work/cloud-voice

# Start webpack dev server
echo "Start webpack dev server"
npm start

#Update FuelPHP
echo "Update bower"
bower --allow-root update

#Update FuelPHP
echo "Update FuelPHP"
php composer.phar self-update
php composer.phar update

FUEL_ENV=development
echo $FUEL_ENV
php oil refine migrate --packages=auth
php oil refine migrate:down --version=0
php oil refine migrate
