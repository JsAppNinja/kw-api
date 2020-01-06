#!/bin/sh
# Are we RedHat Based?
if [ ! -e "/etc/debian_version" ]
then
    #Nginx and git should be part of the base image..
    sudo yum -y install git nginx
    sudo yum -y install nodejs npm --enablerepo=epel
fi
# Are we Debian Based?
if [ -e "/etc/debian_version" ]
then
    #Nginx and git should be part of the base image..
    sudo apt-get update
    sudo apt-get install -y nginx
    sudo apt-get install python-software-properties
    sudo LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
    sudo apt-get update && apt-get purge php5-fpm
    sudo apt-get install -y php7.0-cli php7.0-common libapache2-mod-php7.0 php7.0 php7.0-mysql php7.0-fpm php7.0-curl php7.0-gd php7.0-mysql php7.0-bz2 php7.0-mbstring php7.0-bcmatch php7.0-dom
    sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi
