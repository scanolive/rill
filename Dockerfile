FROM debian

MAINTAINER scan <scani@163.com>

RUN sed -i 's/security.debian.org/mirrors.aliyun.com/g' /etc/apt/sources.list && \
	sed -i 's#http://deb.debian.org#http://mirrors.aliyun.com#g' /etc/apt/sources.list && \
	echo 'nameserver 223.5.5.5' > /etc/resolv.conf &&  \
	apt-get update  -o Acquire-by-hash=yes -o Acquire::https::No-Cache=True -o Acquire::http::No-Cache=True && \
	apt-get install -o Acquire-by-hash=yes -o Acquire::https::No-Cache=True -o Acquire::http::No-Cache=True  -y --no-install-recommends \
	python python-pymysql python-rsa python-cryptography \
	default-mysql-server php-mysql apache2 libapache2-mod-php && \
	rm -rf /var/lib/apt/lists/*

RUN mv /var/lib/mysql /var/lib/mysql_bak && \
	mkdir /var/lib/mysql && \
	mkdir /myrill && \ 
	mkdir /myolive && \ 
	echo "ServerName localhost:80" >> /etc/apache2/apache2.conf 

VOLUME /var/lib/mysql /myrill
WORKDIR /myrill/
COPY ./ /myolive/
COPY start.sh /start.sh
EXPOSE 80
ENTRYPOINT ["/bin/bash", "/start.sh"]

