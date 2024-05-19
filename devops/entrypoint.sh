#!/bin/sh

echo "=> Create /var/log/nginx"
mkdir -p /var/log/nginx


echo "** Running supervisord **"
supervisord --nodaemon --configuration /etc/supervisord.conf
