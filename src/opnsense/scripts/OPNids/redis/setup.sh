#!/bin/sh
REDIS_DIRS="/var/log/redis"

for REDIS_DIR in ${REDIS_DIRS}; do
        mkdir -p ${REDIS_DIR}
        chown -R redis:redis ${REDIS_DIR}
        chmod -R 0755 ${REDIS_DIR}
done

sleep 2
/usr/local/etc/rc.d/redis start