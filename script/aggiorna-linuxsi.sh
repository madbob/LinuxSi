#!/bin/bash

# questo è lo script che si occupa dell'aggiornamento di http://linuxsi.com/
# pigliando il relativo branch da GitHub.
# Bada che questo script non viene eseguito in automatico, sicché eventuali
# modifiche vanno segnalate a Fabio Invernizzi <fabulus@linux.it>

PATH_SITO='/var/www/linuxsi'

fallito_aggiornamento() {
	# segnalo via mail problemi sull'aggiornamento, se possibile
	[ -e /usr/bin/mail ] && echo "Problema aggiornamento git-pull linuxsi.com" | /usr/bin/mail -s "LinuxSi: errore git-pull" bob@linux.it
	# sputo qualcosa anche in output, contando che venga intercettato da cron.
	echo "LinuxSi: errore git-pull"
	exit
}

cd $PATH_SITO

su -c "/usr/bin/git pull -q git://github.com/madbob/LinuxSi.git master" www-data || fallito_aggiornamento
su -c "/bin/date -d @$(git log -n 1 --pretty='%at')  > .ultimo_commit" www-data

/usr/bin/php map-generator.sh

