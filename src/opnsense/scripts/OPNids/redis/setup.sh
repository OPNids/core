#!/bin/sh

mkdir -p /var/log/redis
chown -R redis:redis /var/log/redis
chmod -R 0755 /var/log/redis