#!/bin/bash
#by scan
if [ -z $1 ] ;then
	_interface=`netstat -f inet -nr |grep default|head -1|awk '{print $NF}'`
else
	if [ "$1" -gt 0 ] 2>/dev/null; then
		_delta_t=$1
		_interface=`netstat -f inet -nr |grep default|head -1|awk '{print $NF}'`
	else
		_interface=$1
	fi
fi

if [ -z ${_delta_t} ];then
	if [ -z $2 ];then
		_delta_t=1
	else
		_delta_t=$2
	fi
fi


ifconfig ${_interface} > /dev/null 2>&1

if [[ $? -eq 0 ]] && [[ ${_delta_t} -gt 0 ]];then
	:
else
	echo "Usage: $0 interface interval"
	exit 1
fi

netstat -i -I ${_interface} -b -w ${_delta_t} |
	awk -v interval=${_delta_t} -v if_dev=${_interface}  'BEGIN { 
		printf (if_dev"   Speed(KB/s)     "NR"s     Traffic(Mb) \n      in     out              in      out\n")
	}
	{
		if ($0 !~ /(input|packets)/) 
		{
			# get input & output bytes
			inb=$3 ; oub=$6 ;
			# accumulate them
			cuminb+=$3 ; cumoub+=$6 ;
			if (NR%30 == 0)
			{
				if (NR > 99)
				{
					printf (if_dev"   Speed(KB/"interval"s)    "NR*interval"s    Traffic(Mb) \n      in     out              in      out\n")
				}
				else
				{
					printf (if_dev"   Speed(KB/"interval"s)     "NR*interval"s     Traffic(Mb) \n      in     out              in      out\n")
				}
			}		
			printf("%9.1f%7.1f          ",inb/'${_delta_t}'/1024, oub/'${_delta_t}'/1024)
			#printf("%9.1f%7.1f          ",inb/1024, oub/1024)
			printf("%8.2f%8.2f\n", cuminb/1048576, cumoub/1048576)
		}
	}'
