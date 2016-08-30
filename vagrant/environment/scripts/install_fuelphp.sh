#!/bin/bash

echo "Install FuelPHP"

# install oil command
curl get.fuelphp.com/oil | sh

# composer download
cd /vagrant/work/cloud-voice
php composer.phar config --global github-oauth.github.com 289ac7bbf77dc3ddc4c435d2ae0bfae1db64a0e5

# Pull in the defined composer dependencies.
php composer.phar self-update
php composer.phar update

# Makes the necessary directories writable
php oil refine install
