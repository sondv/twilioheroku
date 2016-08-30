#!/bin/bash

# clear default settings
chkconfig iptables off
sudo /etc/rc.d/init.d/iptables stop

chkconfig ip6tables off
sudo /etc/rc.d/init.d/ip6tables stop

## install git
sudo yum -y install git

# set timezone
sudo cp /usr/share/zoneinfo/Japan /etc/localtime

# set hosts
sudo sh -c "echo '52.196.76.85    healhty-voice.com' >> /etc/hosts"