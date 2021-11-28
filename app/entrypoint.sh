#!/bin/sh

#OAUTH_DOMAIN="${OAUTH_DOMAIN:-null}"
#OAUTH_CLIENT="${OAUTH_CLIENT:-null}"
#OAUTH_SERVER="${OAUTH_SERVER:-null}"

#PASSWORD="${PASSWORD:-null}"

if [ ! -f /opt/store/databases/Demo.kdbx ]; then
    echo "Demo is not exists. Copying"
    mkdir -p /opt/store/databases /opt/store/backup
    cp /opt/app/Demo.kdbx /opt/store/databases/
fi

chmod 0777 /opt/store/databases /opt/store/backup
chmod 0666 /opt/store/databases/*

exec "$@"


