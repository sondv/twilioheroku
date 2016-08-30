#!/bin/bash

echo "Install Apache"

## Install Apache
sudo yum -y install httpd

## Repalce Apache User & Group
sudo sed -i -e "s/User apache/User vagrant/g" /etc/httpd/conf/httpd.conf
sudo sed -i -e "s/Group apache/Group vagrant/g" /etc/httpd/conf/httpd.conf

# change document root
cat >> /etc/httpd/conf/httpd.conf << "EOF"
NameVirtualHost *:80
<VirtualHost *:80>
     DocumentRoot /vagrant/work/cloud-voice/public
     ErrorLog logs/fuelphp_error.log
     CustomLog logs/fuelphp_access.log combined
     <Directory /vagrant/work/cloud-voice/public>
         DirectoryIndex index.php index.html
         AllowOverride All
         Order allow,deny
         Allow from all
     </Directory>
</VirtualHost>

EnableSendfile off
EOF
