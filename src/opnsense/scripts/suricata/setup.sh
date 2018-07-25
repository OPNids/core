#!/bin/sh

SURICATA_DIRS="/var/log/suricata"

for SURICATA_DIR in ${SURICATA_DIRS}; do
	mkdir -p ${SURICATA_DIR}
	chown -R root:wheel ${SURICATA_DIR}
	chmod -R 0700 ${SURICATA_DIR}
done

# make sure we can load our yaml file if we don't have rules installed yet
touch /usr/local/etc/suricata/installed_rules.yaml

REDIS_DIRS="/var/log/redis"

for REDIS_DIR in ${REDIS_DIRS}; do
	mkdir -p ${REDIS_DIR}
	chown -R root:wheel ${REDIS_DIR}
	chmod -R 0700 ${REDIS_DIR}
done

touch ${REDIS_DIRS}/redis.log
