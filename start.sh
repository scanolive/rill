#!/bin/bash
cp -r /var/lib/mysql_bak/* /var/lib/mysql/
chown -R mysql:mysql /var/lib/mysql

/etc/init.d/mysql start
sleep 3
/etc/init.d/apache2 start

if [[ `ls /myrill |wc -l` -eq 0 ]];then
	cp -r /myolive/* /myrill/
fi

if [[ -f /myrill/create_db.sql ]];then
	mysql < /myrill/create_db.sql
fi
if [[ -f /myrill/olive_db.sql ]];then
	mysql olive < /myrill/olive_db.sql
fi

if [[ -f /myrill/olive_server.py ]];then
	/myrill/olive_server.py start
fi

if [[ -d /myrill/web ]];then
	rm -rf /var/www/html
	cp -r /myrill/web /var/www/html
	chown -R www-data:www-data /var/www/html
fi

/bin/bash
