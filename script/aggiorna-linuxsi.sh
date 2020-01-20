#!/bin/bash

# questo è lo script che si occupa dell'aggiornamento di http://linuxsi.com/
# pigliando il relativo branch da GitHub.

PATH_SITO='/var/www/LinuxSi'

fallito_aggiornamento() {
	# segnalo via mail problemi sull'aggiornamento, se possibile
	[ -e /usr/bin/mail ] && echo "Problema aggiornamento git-pull linuxsi.com" | /usr/bin/mail -s "LinuxSi: errore git-pull" bob@linux.it
	# sputo qualcosa anche in output, contando che venga intercettato da cron.
	echo "LinuxSi: errore git-pull"
	exit
}

cd $PATH_SITO

git pull || fallito_aggiornamento
date -d @$(git log -n 1 --pretty='%at')  > .ultimo_commit

php map-generator.sh

