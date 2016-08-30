#!/bin/bash

## Install nvm & npm
cd
git clone git://github.com/creationix/nvm.git ~/.nvm
source ~/.nvm/nvm.sh
nvm install v6.2.0
npm update -g npm
sh -c "echo 'source ~/.nvm/nvm.sh' >> ~/.bash_profile"
sh -c "echo 'nvm use v6.2.0' >> ~/.bash_profile"


## Install webpack & bower
npm install -g webpack webpack-dev-server
npm install -g bower


## Initial install
cd /vagrant/work/cloud-voice
bower --allow-root install
npm install
