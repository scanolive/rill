#!/bin/bash

function check_os()
{
	if [[ -f '/etc/debian_version'  ]];then
		echo "debian"
	elif [[ -f '/etc/redhat-release'  ]];then
		echo "centos"
	else
		echo "other"
	fi
}

function check_decrypt_mode()
{
	decrypt_mode=`grep '^ENCRYPT_MODE' olive_server.py |tr '"' "'" |awk -F "'" '{print $2}'`
	echo $decrypt_mode		
}


function test_python_module()
{
	module_name=$1
	python -c "import $module_name" > /dev/null 2>&1
	if [[ $? -ne 0 ]];then
		echo "No"
	else
		echo "Yes"
	fi

}

function create_database()
{
	echo "#建库添加用户sql"
	echo ""
	create_database_sql="create database if not exists olive default charset utf8 collate utf8_general_ci;"
	add_user_sql="grant all privileges on olive.* to olive@localhost identified by 'olive'  with grant option;"
	flush_sql="flush privileges;"
	echo "#"$create_database_sql
	echo "#"$add_user_sql
	echo "#"$flush_sql
	echo ""
}

function get_python_version()
{
	if [[  `which python` == "" ]];then
		python_cmd="python3"
	else
		python_cmd=`which python`
	fi
	py_version=`$python_cmd -c "import sys; print(sys.version_info[0])"`
	echo $py_version
}


function install_py_module()
{
	if [[ $os == "debian" ]];then
		for i in `echo $module`
		do
			if [[ `test_python_module $i` == "No" ]];then
				echo "#检测python模块$i未安装,运行下面的命令安装"
				echo ""
				if [[ $py_version -eq 2 ]];then
					echo "apt-get install python-$i"
				else
					echo "apt-get install python"$py_version"-$i"
				fi
				echo ""
			else
				echo "#检测到python模块$i已安装"
			fi
		done
	fi

	if [[ $os == "centos"  ]];then
		check_epel_repo
		for i in `echo $module`
		do 
			if [[ `test_python_module $i` == "No"  ]];then
				if [[ $py_version -eq 2  ]];then
					if [[ `yum search python"$py_version"-$i 2>/dev/null |grep python"$py_version"-$i |wc -l` -eq 0  ]];then
						install_cmd="No"
					else
						install_cmd="yum install python"$py_version"-$i"
					fi
				else
					if [[ `yum search $i 2>/dev/null |grep python3|wc -l` -eq 0  ]];then
						install_cmd="No"
					else
						install_cmd="yum install python"$py_version*"-$i"
					fi
				fi
				if [[ $install_cmd != "No" ]];then
					echo "#检测到未安装python模块$i,运行下面的命令安装"
					echo ""
					echo "$install_cmd"
					echo ""
				else
					echo "#检测到未安装python模块$i,且未搜索到可用安装,请手动安装$i模块"
					echo ""
				fi
			else
				echo "#检测到python模块$i已安装"
			fi
		done
	fi	
}

function install_python-pymysql()
{
	if [[ `test_python_module pymysql` == "No" ]]  &&  [[ `test_python_module MySQLdb` == "No" ]];then
		#echo "#检测到未安装python-pymysql模块,搜索可用安装"
		if [[ $os == "debian" ]];then
			if [[ $py_version -eq 2 ]];then
				if [[ `apt-cache search python-pymysql|wc -l` -eq 0 ]];then
					install_cmd="No"
				else
					install_cmd="apt-get install python-pymysql"
				fi
			else
				if [[ `apt-cache search python3-pymysql|wc -l` -eq 0 ]];then
					install_cmd="No"
				else
					install_cmd="apt-get install python3-pymysql"
				fi
			fi

		elif [[ $os == "centos"  ]];then
			check_epel_repo
			if [[ $py_version -eq 2  ]];then
				if [[ `yum search python2-pymysql 2>/dev/null |grep python2-pymysql |wc -l` -eq 0 ]];then
					install_cmd="No"
				else
					install_cmd="yum install python2-pymysql"
				fi
			else
				if [[ `yum search pymysql 2>/dev/null |grep python3|wc -l` -eq 0 ]];then
					install_cmd="No"
				else
					install_cmd="yum install python3*-PyMySQL"
				fi
			fi
		fi
		if [[ $install_cmd != "No" ]];then
			echo "#检测到未安装python模块pymysql或MySQLdb,运行下面的命令安装"
			echo "$install_cmd"
			echo ""
			return 0
		else
			echo "#检测到未安装python模块pymysql,且未搜索到可用安装,请安装MySQLdb模块"
			echo ""
			return 1
		fi
	else
		echo "#检测到python模块pymysql已安装"
	fi
}

function install_python-mysqldb()
{
	if [[  `test_python_module MySQLdb` == "No"  ]];then
		#echo "#检测到未安装python模块MySQLdb,行下面的命令安装"
		if [[ $os == "debian" ]];then
			if [[ $py_version -eq 2 ]];then
				if [[ `apt-cache search python-mysqldb|wc -l` -eq 0 ]];then
					install_cmd="No"
				else
					install_cmd="apt-get install python-mysqldb"
				fi
			else
				if [[ `apt-cache search python3-mysqldb|wc -l` -eq 0 ]];then
					install_cmd="No"
				else
					install_cmd="apt-get install python3-mysqldb"
				fi
			fi

		elif [[ $os == "centos"  ]];then
			check_epel_repo
			if [[ $py_version -eq 2  ]];then
				if [[ `yum search MySQL-python 2>/dev/null |wc -l` -eq 0 ]];then
					install_cmd="No"
				else
					install_cmd="yum install MySQL-python"
				fi
			else
				if [[ `yum search mysqldb 2>/dev/null |grep python3|wc -l` -eq 0 ]];then
					install_cmd="No"
				else
					install_cmd="yum install python3*-mysql"
				fi
			fi
		fi
		if [[ $install_cmd != "No" ]];then
			echo "#检测到未安装python模块MySQLdb,运行下面的命令安装"
			echo "$install_cmd"
			echo ""
		else
			echo "#检测到未安装python模块MySQLdb,且未搜索到可用安装,请用pip命令或其他方式安装"
			echo ""
		fi
	else
		echo "#检测到python模块MySQLdb已安装"
	fi				
}




function check_epel_repo()
{
	if [[ $os == "centos"  ]] && [[ ! -f /etc/yum.repos.d/epel.repo ]];then
		echo "#检测到未安装epel仓库,运行下面的命令安装后重新执行本脚本"
		echo ""
		echo "yum install epel-release"
		echo ""
		exit
	fi
}

function disable_selinux()
{
	if [[ $os == "centos" ]];then
		selinux_status=`getenforce`
		if [[ $selinux_status == "Enforcing" ]];then
			echo "#检测selinux未关闭,请运行下面的命令关闭"
			echo ""
			echo "setenforce 0"
			echo "sed -i 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config"
			echo ""
		fi
	fi
}

function sed_client_ip()
{
	host_ip=`for i in $(ip link |grep -v 'link' |awk -F ':' '{print $2}');do if ! test $(ls /sys/devices/virtual/net/|grep $i);then ip a show $i|grep ' inet '|awk  '{print $2}';fi;done |awk -F '/' '{print $1}'`
	if [[ `grep "xxx.xxx.xxx.xxx" olive_client.py| grep -v "lower" |wc -l` -eq 1 ]];then
		echo "#检测到客户端SERVER_IP未配置,请运行下面的命令修改"
		echo ""
		echo "sed -i 's/xxx.xxx.xxx.xxx/$host_ip/' olive_client.py"
		echo ""
	fi
}

os=`check_os`
py_version=`get_python_version`
module="rsa cryptography"
#check_epel_repo
if  [[ `check_decrypt_mode` == "RSA_KEY" ]];then
	install_py_module
fi
install_python-pymysql
if [[ $? -eq 1 ]];then
	install_python-mysqldb
fi
disable_selinux
sed_client_ip
#create_database
