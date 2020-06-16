#!/usr/bin/python
#encoding=utf-8
import os
import atexit
import sys
import time
import threading
from signal import SIGTERM
import socket
import struct
import subprocess
import zipfile
import string
import random
import base64

#加密配置,请配置server端于此保持一致 建议修改
AUTH_KEY = 'aXA0YWkyOHdzO'
OFFSET = '17'
JAMSTR = '!@!'
ENDSTR = 'OLIVE_EOS'

#用于字符串分解,须与server和PHP一致 建议修改
ENDCMD = 'OLIVE_EOC'
OCT_CMD_PRE = 'OLIVE_CTRL_CENTER_CMD'
OCT_BAT_PRE = 'OLIVE_CTRL_CENTER_BAT'
SEP_STR = '@!@'

#server端IP,端口,必须修改
SERVER_IP = '192.168.10.252'
SERVER_PORT = 33331


#路径设置,根据需要修改
CLIENT_FILENAME = os.path.realpath(__file__)
HOME_DIR = sys.path[0] + '/'
LOG_DIR = 'log/'
PIDFILE = CLIENT_FILENAME.replace('.py','.pid')
LOG_FILE = HOME_DIR + LOG_DIR + 'oct.log'


def_cmd_rs = ['UPDATE_CLIENT','UPDATE_CLIENT_FILE','RESTART_CLIENT','UPDATE_SHFILE','CHECK_DEVINFO','CHECK_SERVICE','CPFILE']

def Save_Log(type, data):
    logdir = LOG_DIR
    logfile = LOG_FILE
    if not os.path.exists(logdir):
        os.system('mkdir -p ' + logdir)
    f = open(logfile, 'a')
    f.write(time.strftime('%Y-%m-%d %H:%M:%S', time.localtime()) + ' ' + type + ' ' + str(data) + '\n')
    f.close()
def Connect_Socket():
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    try:
        sock.connect((SERVER_IP,SERVER_PORT))
        return sock
    except Exception as e:
        Save_Log('Connect_Socket',e)

def Recv_Data(socket):
    data = ''
    while True:
        buf = socket.recv(1024).decode()
        if not len(buf) or buf.endswith(ENDSTR):
            data += buf
            break
        data += buf
    return data

if __name__ == '__main__':
    if len(sys.argv) == 3:
        client_ip = sys.argv[1]
        client_ip = client_ip.replace(","," ")
        cmd = sys.argv[2]
        print(client_ip + ' is running ' + cmd + ' wait a minute')
        cmd = OCT_CMD_PRE + SEP_STR + client_ip + SEP_STR  + cmd + SEP_STR + ENDCMD
        sock = Connect_Socket()
        if sock:
            sock.send(cmd.encode())
            data = Recv_Data(sock)
            sock.close()
            Save_Log(client_ip,data)
            print(data.replace("\n"+ENDSTR,''))
    else:
        print("Usage: %s client_ip cmd" % sys.argv[0])
        print('system command:')
        for cmd in def_cmd_rs:
            print(cmd)
        sys.exit(0)
