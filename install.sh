#!/bin/bash

python -c 'import MySQLdb' > /dev/null 2>&1
if [[ $? -ne 0 ]];then
	echo "module MySQLdb not install"
	if grep -iq debian /etc/issue;then
		echo "apt-get install python-mysqldb"	  	
	fi
fi
