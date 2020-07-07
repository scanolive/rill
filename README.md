#linux运维监控

* 基于python编写,CS架构,client端定期检测系统状态发送至server端,server端将数据存入数据库并根据监控阀值判断状态,超过阀值报警
* 页面展示系统基于php,支持web页面对client端状态进行查看,并可对client端进行操作管理
* 数据安全方面,client和server端数据传输基于socket,数据经过加密,加密方式可选密钥或自定义
* 系统安全方面,client可定义运行级别,可选为监控,控制,自己定义,页面展示可选开启或关闭控制中心和webshell,另用户权限分级为管理员,普通用户,监控查看

#文件和目录列表

| 文件名目录名			| 用途					|
|	---					| ---					|
| olive_server.py 		| 服务器端文件		|
| olive_client.py  		| 客户端文件		|
| olive_oct.py			| 命令行控制客户端文件 |
| client_file.py		| 升级客户端文件时的新客户端文件 |
| install.sh			| 服务器端安装检测文件	|
| Dockerfile			| docker镜像构建文件	|
| start.sh				| 用于构建docker镜像|
| create_db.sql 		| 默认建库sql文件		|
| olive_db.sql			| 数据库文件		|
| shell_file 			| 存放客户端shell脚本目录,启动后客户端自动下载 |
| web					| 存放web文件目录		|

#server端安装方式
1. 普通安装
	* 系统环境需求
		- python2或python3
			- python-pymysql或python-MysqlDB
			- 若选密钥加密则另外安装python-rsa和python-cryptography模块
		- php mysql apache
		- 关闭selinux
		- 若系统开启防火墙,则需开放对应端口
	* 安装步骤
		1. 下载代码进入代码目录
		2. 添加可执行权限
			
			```
			chmod +x *.py
			chmod +x *.sh
			```
		3. 执行install.sh检测,并根据提示安装模块,修改客户端文件配置
			
			
			```
			./install.sh 	
			```
		4. 使用默认数据库设置建库或自定义数据库配置建库赋权和导入数据库
			
			```
			mysql < create_db.sql
			mysql olive < olive_db.sql
			```
		5. 若有需要可按照注释修改配置,若是自定义数据库配置则修改数据库相关的配置,修改配置需要同时修改下面三个文件
			- olive_server.py
			- olive_client.py
			- olive_oct.py
		6. 复制代码目录下的web目录下的所有文件到apache的默认目录
		7. 启动server端
		
			```
			./olive_server.py start
			```
		8. 访问server端页面http://server_ip/,自动进入web初始化,配置数据库,添加用户,配置报警邮件

2.  Dockerfile构建镜像
 * 数据卷 /var/lib/mysql /myrill
 * 暴露端口 80 33331
	* 构建镜像
		
		```
		docker build -t olive/rill .
		```
	* 启动容器若要挂载数据卷要保证/var/lib/mysql对应的主机目录为空,/myrill对应的主机目录是代码目录,如以下命令中主机目录/data/mysql为空,/data/myrill为代码目录
		
		```
	   docker run -it --name rill -p 8380:80 -p 33331:33331 -v /data/mysql:/var/lib/mysql -v /data/myrill:/myrill olive/rill
		```	

		进入容器后Ctrl + p + q退出
	* 访问server端页面http://server_ip:8380/,自动进入web初始化,配置数据库,添加用户,配置报警邮件
	* 若系统开启防火墙,则需开放对应端口
		
#client端安装
* 将修改配置后的客户端文件复制到客户端任意目录启动即可,若是使用密钥加密且相关模块未安装,按照提示安装即可
	
	```
	./olive_client.py start
	```
* 若系统开启防火墙,则需开放对应端口



