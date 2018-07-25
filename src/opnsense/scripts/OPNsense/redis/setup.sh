#!/bin/sh

REDIS_DIRS="/var/log/redis"

for REDIS_DIR in ${REDIS_DIRS}; do
	mkdir -p ${REDIS_DIR}
	chown -R root:wheel ${REDIS_DIR}
	chmod -R 0700 ${REDIS_DIR}
done

touch ${REDIS_DIRS}/redis.log