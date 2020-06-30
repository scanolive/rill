create database if not exists olive default charset utf8 collate utf8_general_ci;
grant all privileges on olive.* to olive@localhost identified by 'olive' with grant option;
flush privileges;
