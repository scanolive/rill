#!/usr/bin/env python
# coding: utf-8
import os
import atexit
import sys
import time
import threading
import signal
import socket
from signal import SIGTERM
import struct
import subprocess
import zipfile
import string
import random
import hashlib
import base64
import rsa
import pickle
from cryptography.fernet import Fernet

if sys.version_info[0] == 2:
    reload(sys)
    sys.setdefaultencoding('utf-8')
    def pickle_loads(obj):
        return pickle.loads(obj)
    def bytes2str(bytes_or_str):
        if isinstance(bytes_or_str,bytes):
            s = bytes_or_str.decode()
        elif isinstance(bytes_or_str,unicode):
            s = bytes_or_str.encode()
        elif isinstance(bytes_or_str,str):
            s = bytes_or_str
        return s    
else:
    def pickle_loads(obj):
        return pickle.loads(obj,encoding='bytes')
    def bytes2str(bytes_or_str):
        if isinstance(bytes_or_str,bytes):
            s = bytes_or_str.decode()
        elif isinstance(bytes_or_str,str):
            s = bytes_or_str
        return s    


#--------------------------加密配置--------------------------
#加密解密配置,请与服务器端一致
AUTH_KEY = 17
#OFFSET = 17
JAMSTR = '!@!'
ENCODE_END_STR = 'OLIVE_EOS'
END_CMD_STR = 'OLIVE_EOC'
#--------------------------加密配置--------------------------


#--------------------------系统配置-------------------------
#server端IP,端口,client端口,必须修改
SERVER_IP = 'xxx.xxx.xxx.xxx'
SERVER_IPS = [SERVER_IP]
SERVER_PORT = 33331
BIND_PORT = 22221

#client端绑定监听的端口,可修改为具体ip
BIND_IP = 'AUTO'

#分割符,请与server端一致
SEP_STR = '@!@'
SEP_STR2 = 'R!I@L#L'

#路径设置,根据需要修改
HOME_DIR = sys.path[0] + '/'
SH_DIR = HOME_DIR + 'shell/'
LOG_DIR = HOME_DIR + 'log/'
BG_OUT_DIR = HOME_DIR + 'bg_out/'
PIDFILE = HOME_DIR + 'my_client.pid'
LOG_FILE = LOG_DIR + 'my_client.log'

#执行server端命令超时时间,单位秒
RUN_CMD_TIMEOUT = 8

#server命令执行结果返回的长度,单位字节
CMD_RESULT_LENGTH = 40960
#server命令后台执行结果返回的长度,单位字节
BG_RESULT_LENGTH = 4096
#--------------------------系统配置-------------------------

#------------------------------------------安全配置-------------------------------------
#运行级别(ctrl,monitor,custom),ctrl完全控制,monitor仅监控,custom自定义
RUN_LEVEL = 'ctrl'

#自定义(custom)选项,只有运行级别为custom时生效
#是否可以执行服务器端命令
SERVER_CMD_ENABLE = 'NO'
#是否可以服务器端自动更新客户端命令
AUTO_UPDATE_CLIENT = 'NO'
#是否允许服务器向本地拷贝文件
CPFILE_ENABLE = 'NO'
#是否允许自动获取shell脚本,若不允许,则需要手动拷贝shell文件
AUTO_GET_SHFILE = 'YES'
#是否允许服务器端推送shell文件到本地,开启此选项需同时开启允许自动获取shell脚本选项
AUTO_UPDATE_SHFILE = 'NO'

if RUN_LEVEL == 'ctrl':
    SERVER_CMD_ENABLE = 'YES'
    AUTO_UPDATE_CLIENT = 'YES'
    CPFILE_ENABLE = 'YES'
    AUTO_UPDATE_SHFILE = 'YES'
    AUTO_GET_SHFILE = 'YES'
elif RUN_LEVEL == 'monitor':
    SERVER_CMD_ENABLE = 'NO'
    AUTO_UPDATE_CLIENT = 'NO'
    CPFILE_ENABLE = 'NO'
    AUTO_UPDATE_SHFILE = 'NO'
    AUTO_GET_SHFILE = 'NO'
else:
    if AUTO_GET_SHFILE == 'NO':
        AUTO_UPDATE_SHFILE = 'NO'
#-------------------------------------------安全配置--------------------------------------

#--------------------------常量变量-------------------------
CLIENT_FILENAME = os.path.realpath(__file__)
RUN_STATE = True
#--------------------------常量变量-------------------------

#--------------------------
ENCODE_END_BYTE = ENCODE_END_STR.encode()


#---------------------------生成rsa密钥---------------------

RSA_KEY = rsa.newkeys(1024)


#加密函数
'''
def encode(s_str):
    if sys.version_info.major == 2:
        return encode2(s_str)
    else:
        return encode3(s_str)
def decode(s_str):
    if sys.version_info.major == 2:
        return decode2(s_str)
    else:
        return decode3(s_str)
'''

'''
def encrypt(s):
    key = AUTH_KEY
    b = bytearray(str(s).encode("utf-8"))
    n = len(b)
    c = bytearray(n*2)
    j = 0
    for i in range(0, n):
        b1 = b[i]
        b2 = b1 ^ key
        c1 = b2 % 31
        c2 = b2 // 31
        c1 = c1 + 42
        c2 = c2 + 42
        c[j] = c1
        c[j+1] = c2
        j = j+2
    c = c + ENCODE_END_STR.encode("utf-8")
    return c
    #return c.decode("utf-8")


def decrypt(s):
    key = AUTH_KEY
    
    s = s.decode().rstrip(ENCODE_END_STR)
    c = bytearray(str(s).encode("utf-8"))
    n = len(c)
    if n % 2 != 0:
        return ""
    n = n // 2
    b = bytearray(n)
    j = 0
    for i in range(0, n):
        c1 = c[j]
        c2 = c[j + 1]
        j = j + 2
        c1 = c1 - 42
        c2 = c2 - 42
        b2 = c2 * 31 + c1
        b1 = b2 ^ key
        b[i] = b1
    return b.decode("utf-8")

'''

def decrypt(s):
    global fernet_obj
    s = s.decode().replace(ENCODE_END_STR,'')
    if s.endswith(END_CMD_STR):
        print(s)
        print('----------------------------------------------------------------------------------------------')
        return s.decode("utf-8")
    #f = Fernet(server_key)
    try:
        c = fernet_obj.decrypt(s.encode("utf-8"))
    except Exception as e:
        print(e)
        fernet_obj = Get_server_key(SERVER_IP,SERVER_PORT)
        print(fernet_obj)
        c = b""
    return c.decode("utf-8")


def encrypt(s):
    #print('jiami',s)
    #f = Fernet(server_key)
    c = fernet_obj.encrypt(s.encode("utf-8"))
    c = c + ENCODE_END_STR.encode("utf-8")
    return c


#保存日志函数
def Save_Log(type, data):
    logdir = LOG_DIR
    logfile = LOG_FILE
    if not os.path.exists(logdir):
        os.system('mkdir -p ' + logdir)
    f = open(logfile, 'a')
    f.write(time.strftime('%Y-%m-%d %H:%M:%S', time.localtime()) + ' ' + type + ' ' + str(data) + '\n')
    f.close()


def New_Socket():
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    return sock 

#守护进程类
class Daemon:
    def __init__(self,pidfile,homedir,process_name):
        self.pidfile = pidfile
        self.homedir = homedir
        self.process_name = process_name

    def    daemonize(self):
        try:
            if os.fork() > 0:
                os._exit(0)
        except OSError as  error:
            print( 'fork #1 failed: %d (%s)' % (error.errno, error.strerror))
            os._exit(1)
    
        os.chdir(self.homedir)
        os.setsid()
        os.umask(0)
    
        try:
            pid = os.fork()
            if pid > 0:
                os._exit(0)
        except OSError as  error:
            print( 'fork #2 failed: %d (%s)' % (error.errno, error.strerror))
            os._exit(1)
        atexit.register(self.delpid)
        pid = str(os.getpid())
        open(self.pidfile,'w+').write("%s\n" % pid)
    def delpid(self):
        os.remove(self.pidfile)

    def    start(self):
        '''    
        try:
            pf = file(self.pidfile,'r')
            pid = int(pf.read().strip())
            pf.close()
        except IOError: as 
            pid = None
        if pid:
            message = "Start error,pidfile %s already exist. Daemon already running?\n"
            sys.stderr.write(message % self.pidfile)
            sys.exit(1)
        '''
        check_process_cmd = 'ps -ef|grep -v "grep"|grep "' + self.process_name +'"'
        check_procees_res = subprocess.call([check_process_cmd],stdout=subprocess.PIPE,shell=True)
        check_listen_cmd = "ss -4tlnp|awk '{print $4}'|grep " + str(BIND_PORT)
        check_listen_res = subprocess.call([check_listen_cmd],stdout=subprocess.PIPE,shell=True)
        if  check_listen_res != 0 and check_procees_res != 0:
            message = "Start error,process already exist. Daemon already running?\n"
            sys.stderr.write(message)
            sys.exit(1)
                
        self.daemonize()
        self.run()

    def stop(self):
        try:
            pf = open(self.pidfile,'r')
            pid = int(pf.read().strip())
            pf.close()
        except IOError as e:
            pid = None
        if not pid:
            message = "pidfile %s does not exist. osa Daemon not running?\n"
            sys.stderr.write(message % self.pidfile)
            return
        try:
            while True:
                os.kill(pid, SIGTERM)
                time.sleep(0.1)
        except OSError as  err:
            err = str(err)
            if err.find("No such process") > 0:
                if os.path.exists(self.pidfile):
                    os.remove(self.pidfile)
            else:
                sys.exit(1)
    def restart(self):
        self.stop()
        self.start()
                
    def run(self):
        pass

def Check_Port(address, port):
    s = socket.socket()
    try:
        s.connect((address, port))
        s.close()
        return True
    except socket.error as  e:
        return False

#检测网络状况的函数，参数ip
def Ping_Ip(ip):
    cmd = 'ping '+ip+' -c 3 -W 2'
    dae = subprocess.Popen(cmd,stdout=subprocess.PIPE,stderr=subprocess.PIPE,shell=True,executable='/bin/bash')
    Cmd_Run_Err = dae.stderr.read().decode("utf-8")
    Cmd_Run_Out = dae.stdout.read().decode("utf-8")
    dae.stdout.close()
    dae.stderr.close()
    dae.wait()
    if Cmd_Run_Out.find('time=') >= 0:
        return 'alive'
    else:
        return 'down'

def Get_Client_Ip():
    global BIND_IP
    cmd_all = "ip -4 address | grep ' inet ' | grep -v '127.0.0.1\|\/32' |awk '{print $2}'|awk -F '/' '{print $1}'"
    cmd_lan = "ip -4 address | grep ' inet ' | grep -v '127.0.0.1\|\/32' |awk '{print $2}'|awk -F '/' '{print $1}'|sed -n '/^\(172\.\([1][6-9]\|[2][0-4]\|3[01]\)\.\|10\.\|192\.168\.\)/p'"
    All_Ip = Subprocess_Do(cmd_all)
    Lan_Ip = Subprocess_Do(cmd_lan)
    if BIND_IP != 'AUTO':
        return ['USER_DEFINED',BIND_IP]
    elif All_Ip[0] == 0  and  Lan_Ip[0] == 0  and  All_Ip[1] == Lan_Ip[1]:
        return ['ALL_LAN','0.0.0.0']
    elif All_Ip[0] == 1 or Lan_Ip[0] == 1:
        return ['IFCONFIG_ERR','0.0.0.0'] 
    elif All_Ip[0] == 0  and  Lan_Ip[0] == 0  and  len(All_Ip[1]) > len(Lan_Ip[1]) and len(Lan_Ip[1]) >= 7:
        return ['HAVE_PUBLIC',Lan_Ip[1].split("\n")[0]]
    elif All_Ip[0] == 0  and  Lan_Ip[0] == 0  and  len(Lan_Ip[1]) < 7:
        return ['LISTEN_PUBLIC','0.0.0.0']
    else:
        return ['UNKNOWN_ERR','0.0.0.0']

def Test_Server():
    if SERVER_IP == "XXX.XXX.XXX.XXX".lower():
        print("SERVER_IP is not config")
        sys.exit(1)
    if AUTO_GET_SHFILE != 'YES':
        if os.path.isfile(SH_DIR+'/mon_all_stat') and os.path.isdir(SH_DIR) and len(os.listdir(SH_DIR)) > 5:
            pass
        else:
            print( 'no shfile and auto get shfile is not allow,please check it!')
            sys.exit(1)
    else:
        pass

    test_ip = Get_Client_Ip()
    test_result = test_ip[0]
    Client_Ip = str(test_ip[1])
    if test_result == 'LISTEN_PUBLIC':
        Warn_Msg = 'listen public address'
    elif test_result == 'UNKNOWN_ERR':
        Warn_Msg = 'test listen ip unknown err!'
    elif test_result == 'IFCONFIG_ERR':
        Warn_Msg = 'run ifconfig err'
    else:
        Warn_Msg = ''
    if Warn_Msg != '':
        print( 'Warning! ' + Warn_Msg)
    print( 'Testing connect to the server %s:%d, please wait a moment' % (SERVER_IP,SERVER_PORT) )
    if Ping_Ip(SERVER_IP) == 'down':
        print( 'OLIVE_SERVER:' + SERVER_IP + ' unreachable,please check the network')
        sys.exit(1)
    #global server_key
    global fernet_obj
    fernet_obj = Get_server_key(SERVER_IP,SERVER_PORT)
    #print(server_key)
    #fernet_obj = Fernet(server_key)
    if not fernet_obj:
        print( 'OLIVE_PORT:' + str(SERVER_PORT) +' or get server key is Err,please check olive_server port')
        sys.exit(1)
    else:
        print( 'Test Connection Successful OLIVE_CLIENT is running listen ' + Client_Ip + ':' + str(BIND_PORT) )


#检测端口存活函数，参数ip和端口
def Get_server_key(address, port):
    if not Check_Port(address, port):
        return False
    rsa_key = RSA_KEY
    public_key = rsa_key[0]
    private_key = rsa_key[1]
    send_key = pickle.dumps(public_key,protocol=2)
    send_key_sha256 = hashlib.sha256(send_key).hexdigest()
    s = New_Socket()
    optval = struct.pack("ii",1,0)
    s.setsockopt(socket.SOL_SOCKET,socket.SO_LINGER,optval)
    try:
        s.connect((address, port))
        hello_str = 'I love rill'
        s.send(hello_str.encode())
        re_hello = s.recv(1024).decode()
        if re_hello == "Me tooooooooooooooooooooooooooo":
            s.send(pickle.dumps((send_key, send_key_sha256),protocol=2))
            server_key, server_key_sha256 = pickle_loads(s.recv(1024))
            server_key_sha256 = bytes2str(server_key_sha256)
            #print(type(server_key_sha256))
            #print(type(server_key))
            #revsymKey, symKeySha256 = pickle.loads(s.recv(1024))
            #print('revsymKey',revsymKey)
            #print(type(hashlib.sha256(server_key).hexdigest()))
            #print(type(server_key_sha256))
            #print(hashlib.sha256(server_key).hexdigest())
            #print(server_key_sha256)
            if hashlib.sha256(server_key).hexdigest() != server_key_sha256:
                raise Exception("key is err")
            else:
                server_key = pickle_loads(rsa.decrypt(server_key, private_key))
        else:
            server_key = False
        s.close()
        fernet_obj = Fernet(server_key)
        return fernet_obj
    except socket.error as  e:
        return False
#接受文件函数
def Recv_File(newsock,savename,overwrite):
    time.sleep(1)
    global FILE_DATA_SOCK
    def r_data(filename,ctrl_socket):
        fp = open(filename,'wb')
        while True:
                
            recv = ctrl_socket.recv(2048)
            recv_str = decrypt(recv)
            if recv_str.split(SEP_STR)[0] == 'MD5STR':
                ctrl_socket.send(encrypt('Please send data'))
                r_md5str = recv_str.split(SEP_STR)[1]
                b_size = recv_str.split(SEP_STR)[2]
                restsize = int(b_size)
                filedata = b''
                while 1:
                    if restsize > 1024:
                        tmp_str = FILE_DATA_SOCK.recv(1024)
                    #    print( len(tmp_str))
                        filedata = filedata + tmp_str
                    else:
                        tmp_str = FILE_DATA_SOCK.recv(restsize)
                        filedata = filedata + tmp_str
                        if restsize != len(tmp_str):
                            tmp_str = FILE_DATA_SOCK.recv(1024)
                            filedata = filedata + tmp_str
                        break
                    restsize = int(b_size) - len(filedata)
#                    if restsize <= 0: break
                    if len(filedata) == int(b_size):break
                m = hashlib.md5()
                m.update(filedata)
                md5str = m.hexdigest()
                if md5str == r_md5str:
                    ctrl_socket.send(encrypt('data is OK'))
#                    if len(filedata) < 1024000:
#                            print( filedata)
                    fp.write(filedata)
                else:
                    FILE_DATA_SOCK.close()
                    ctrl_socket.send(encrypt('data is err'))
                    time.sleep(1)
                    ctrl_socket.send(encrypt('Please send data'))
#        elif recv_str == "send done":
#            socket.send(encrypt('recv done'))
            else:
                break        
        fp.flush()
        time.sleep(1)
        fp.close()



    overwrite = str(overwrite)
    if overwrite.isdigit():
        overwrite = int(overwrite)
    else:
        overwrite = 0
    FILEINFO_SIZE = struct.calcsize('128sI')
    try:
        fhead = newsock.recv(FILEINFO_SIZE)
        #print(fhead)
        newsock.send(encrypt('can be send'))
        filename, filesize = struct.unpack('128sI', fhead)
        filename = filename.decode().strip('\00')
        savedir = os.path.dirname(savename)
        if not os.path.isdir(savedir) and savedir != '':
            os.makedirs(savedir)
        if os.path.isdir(savename):
            savename = savename + '/' + filename 
        if os.path.isfile(savename) and not overwrite:
            timenow = time.strftime('%Y%m%d%H%M%S', time.localtime())
            savename = savename + '_' + timenow
        recv_num = 0
        while recv_num < 3:
#            recvdata(savename)
            r_data(savename,newsock)
            recv_num = recv_num + 1
            recv_size = os.path.getsize(savename)
            if  recv_size == filesize :
                newsock.send(encrypt('file is OK'))
                break
            else:
                newsock.send(encrypt('file is err'))

            time.sleep(0.1)
        time.sleep(0.5)
        newsock.close()
        if os.path.getsize(savename) == filesize:
            Save_Log('Recv_File','Received ' + filename + ' save as ' + savename + '  successfully!')
        else:
            Save_Log('Recv_File','Received ' + filename + ' save as ' + savename + '  failed!')
        return savename
        newsock.close()
    except Exception  as e:
        Save_Log('Recv_File',str(e))
        newsock.close()

#连接server端函数
def Connect_Socket():
    sock = New_Socket()
    try:
        sock.connect((SERVER_IP,SERVER_PORT))
        return sock
    except Exception  as e:
        return False
        Save_Log('Connect_Socket',e)

#向server端口发送获取shell文件请求的函数
def Req_Shfile():
    if AUTO_GET_SHFILE == 'YES':
        try:
            conn = Connect_Socket()
            conn.send(encrypt('REQ_SHFILE' + SEP_STR + '__OK__' + SEP_STR + '__OK'))
            conn.close()
        except Exception  as e:
            Save_Log('Req_Shfile',e)
    else:
        Save_Log('Req_Shfile','auto get shfile is not allow')

#更新shell文件的函数
def Update_Shfile():
    try:
        os.system('rm -rf '+SH_DIR)
        Req_Shfile()
        Save_Log('Update_Shfile','have update shell file!')
    except Exception  as e:
        Save_Log('Update_Shfile',str(e))

#解压zip文件函数
def Unzip_File(zipfilename,unziptodir):
    if not os.path.exists(unziptodir): os.mkdir(unziptodir)
    zfobj = zipfile.ZipFile(zipfilename)
    for name in zfobj.namelist():
        name = name.replace('\\','/')

        if name.endswith('/'):
            os.mkdir(os.path.join(unziptodir,name))
        else:
            ext_filename = os.path.join(unziptodir,name)
            ext_dir= os.path.dirname(ext_filename)
            if not os.path.exists(ext_dir): os.mkdir(ext_dir)
            outfile = open(ext_filename,'wb')
            outfile.write(zfobj.read(name))
            outfile.close()

#接收socket数据函数
def Recv_Data(socket):
    data = b''
    while True:
        buf = socket.recv(1024)
        if not len(buf) or buf.endswith(ENCODE_END_BYTE):
            data += buf
            break
        data += buf
    return data

#线程化执行命令函数
def Subprocess_Do(cmd):
    cmd_result = []
    timeout = RUN_CMD_TIMEOUT
    begin_time = time.time()
    seconds_passed = 0
    dae = subprocess.Popen(cmd,stdout=subprocess.PIPE,stderr=subprocess.PIPE,shell=True,executable='/bin/bash')
    while True:
        if dae.poll() is not None:
            Cmd_Run_Err = dae.stderr.read().decode("utf-8")
            Cmd_Run_Out = dae.stdout.read().decode("utf-8")
            dae.stdout.close()
            dae.stderr.close()
            break
        seconds_passed = time.time() - begin_time
        if timeout and seconds_passed > timeout:
            os.kill(dae.pid, signal.SIGTERM)
            Cmd_Run_Err = 'TIMEOUT'
            Cmd_Run_Out = ''
            break
        time.sleep(0.1)
    if Cmd_Run_Err == '':
        cmd_result.append(0)
        cmd_result.append(Cmd_Run_Out)
    else:
        cmd_result.append(1)
        cmd_result.append(Cmd_Run_Err)
        Save_Log('Subprocess_Do',cmd + ' run err ' + Cmd_Run_Err)
    return cmd_result



#监控任务函数
def Mon_Task():
    time.sleep(3)
    while True:    
        cmd_ifnum = "ip -4 address | grep ' inet ' | grep -v '127.0.0.1\|\/32' |wc -l"
        #cmd_ifnum = "ifconfig|grep 'inet addr:'|grep -v '127.0.0.1'|grep -v '255.255.255.255'|wc -l"
        ifnum = Subprocess_Do(cmd_ifnum)
        if ifnum[0] == 0:
            interval = 300.0 - 2.1*int(ifnum[1])
        else:
            interval = 295.5

        cmd = SH_DIR+'mon_all_stat'
        if os.path.isfile(cmd):
            run_num = 0
            cmd_result = Subprocess_Do(cmd)
            while cmd_result[0] == 1 and run_num < 2:
                cmd_result = Subprocess_Do(cmd)
                if cmd_result[0] == 0 and cmd_result[1] != 'null':
                    run_num = 100
                else:
                    time.sleep(5)
                    cmd_result = Subprocess_Do(cmd)
                    run_num += 1

            if cmd_result[0] == 0 and cmd_result[1] != 'null':
                data = 'ISOK' + SEP_STR + cmd_result[1]
            else:
                data = 'ERR' + SEP_STR + '__NoData'
                Save_Log('Mon_Task',str(cmd_result[0]) +' '+ cmd_result[1])
                    
            data = 'MON' + SEP_STR + data
            sock = Connect_Socket()
            if sock:
                sock.send(encrypt(data))
                sock.close()
            else:
                pass
            time.sleep(interval)
        else:
            Req_Shfile()
            time.sleep(20)

#运行命令函数

def Run_Cmd(conn,cmd):
    if cmd.strip().endswith('&'):
        markid=cmd.split(SEP_STR2)[0]
        cmd = cmd.split(SEP_STR2)[1]
        cmd = cmd.strip().replace('&','')
        cmd_list = cmd.split()
        if not str(cmd_list[0]) in ['/bin/bash','/bin/sh','bash','sh']:
            cmd_file = cmd_list[0]
        elif len(cmd_list) > 2 and  str(cmd_list[1]).startswith('-'):
            cmd_file = cmd_list[2]
        else:
            cmd_file = cmd_list[1]
        if cmd_file.startswith('./'):
            cmd_file = cmd_file[2:]    
        cmd_file = os.path.basename(cmd_file)
        bg_out_dir = BG_OUT_DIR    
        if not os.path.exists(bg_out_dir):
            os.system('mkdir -p ' + bg_out_dir)
        err_file=bg_out_dir + cmd_file +'_'+time.strftime('%Y%m%d%H%M%S',time.localtime(time.time()))+'_err'
        out_file=bg_out_dir + cmd_file +'_'+time.strftime('%Y%m%d%H%M%S',time.localtime(time.time()))+'_out'
        out_fd = open(out_file,'w+')
        err_fd = open(err_file,'w+')
        dae = subprocess.Popen(cmd,stdout=out_fd,stderr=err_fd,shell=True,executable='/bin/bash')
        data = 'The cmd is running in background'
        conn.send(encrypt(data))
        time.sleep(0.3)
        conn.close()
        while True:
            if dae.poll() is not None:
                out_fd.seek(0)
                err_fd.seek(0)
                Cmd_Run_Out = out_fd.read(BG_RESULT_LENGTH)
                Cmd_Run_Err = err_fd.read(BG_RESULT_LENGTH)
                if len(Cmd_Run_Out) == BG_RESULT_LENGTH:
                    Cmd_Run_Out = Cmd_Run_Out + "\n" + "......"
                if len(Cmd_Run_Err) == BG_RESULT_LENGTH:
                    Cmd_Run_Err = Cmd_Run_Err + "\n" + "......"
                data = 'BG_RUN_RESULT' + SEP_STR + str(markid) + SEP_STR + Cmd_Run_Out + SEP_STR + Cmd_Run_Err
                sock = Connect_Socket()
                if sock:
                    sock.send(encrypt(data))
                sock.close()
                out_fd.close()
                err_fd.close()
                break
            time.sleep(2)
    else:
        timeout = RUN_CMD_TIMEOUT
        begin_time = time.time()
        seconds_passed = 0
        pro = subprocess.Popen(cmd,stdout=subprocess.PIPE,stderr=subprocess.PIPE,shell=True,executable='/bin/bash')
        Cmd_Run_Out = ''
        Cmd_Run_Err = ''
        while True:
            if pro.poll() is not None:
                Cmd_Run_Err += pro.stderr.read().decode("utf-8")
                Cmd_Run_Out = pro.stdout.read().decode("utf-8") + Cmd_Run_Out
                break
            seconds_passed = time.time() - begin_time
            if timeout and seconds_passed > timeout:
                os.kill(pro.pid, signal.SIGTERM)
                Cmd_Run_Err = 'TIMEOUT'
                Cmd_Run_Out = 'TIMEOUT\n'
            time.sleep(0.1)  
        if Cmd_Run_Err == '' or Cmd_Run_Err == 'TIMEOUT':
            data = Cmd_Run_Out
        else:
            data = Cmd_Run_Err
        if len(data) > CMD_RESULT_LENGTH and not(data.startswith("'") and data[0:-1].endswith("'||")):
            data = data[0:CMD_RESULT_LENGTH] + "\n" + '......'
        conn.send(encrypt(data))
        pro.stdout.close()
        pro.stderr.close()
        time.sleep(0.3)
        conn.close()

#重启client端函数
def Restart_client():
    time.sleep(3)
    global RUN_STATE
    RUN_STATE = False
    Save_Log('Restart_client','Stop signal have send! The process will restart after 5s')

#执行server端命令
def Do_Server(conn,recv):
    if len(recv.split(SEP_STR)) >= 3:
        cmd_type = recv.split(SEP_STR)[0]
        cmd = recv.split(SEP_STR)[1]
        if RUN_LEVEL == 'monitor':
            if cmd_type == 'OLIVE_CLIENT_SHELL':
                cmd = SH_DIR + cmd
                Run_Cmd(conn,cmd)
            else:
                conn.send(encrypt('server_ctrl not allow!'))
                conn.close()
        else:        
            if cmd_type == 'OLIVE_SERVER_RECV_FILE':
                savename = recv.split(SEP_STR)[1]
                filetype = recv.split(SEP_STR)[2]
                overwrite = recv.split(SEP_STR)[3]
                if CPFILE_ENABLE == 'YES':
                    if filetype == 'CLIENT_FILE':
                        if AUTO_UPDATE_CLIENT == 'YES':
                            Recv_File(conn,CLIENT_FILENAME,1)
                        else:
                            conn.send(encrypt('this client not allow auto update!'))
                            conn.close()
                    elif filetype == 'SHFILE':
                        if AUTO_GET_SHFILE == 'YES':
                            savename = os.path.basename(savename)
                            zipname = Recv_File(conn,savename,0)
                            Unzip_File(zipname,SH_DIR)
                            os.system('chmod -R +x '+SH_DIR)
                            os.system('rm -rf '+zipname)
                        else:
                            conn.send(encrypt('auto get shfile is not allow!'))
                    else:
                        Recv_File(conn,savename,overwrite)
                    conn.close()
                else:
                    if filetype == 'SHFILE':
                        savename = os.path.basename(savename)
                        zipname = Recv_File(conn,savename,0)
                        Unzip_File(zipname,SH_DIR)
                        os.system('chmod -R +x '+SH_DIR)
                        os.system('rm -rf '+zipname)
                    else:
                        conn.send(encrypt('this client not allow copy file!'))
            elif cmd_type == 'OLIVE_CLIENT_SHELL':
                cmd = SH_DIR + cmd
                Run_Cmd(conn,cmd)
            elif cmd_type == 'OLIVE_SERVER_CMD':
                if SERVER_CMD_ENABLE == 'YES':
                    Run_Cmd(conn,cmd)
                else:
                    conn.send(encrypt('server_cmd not allow!'))
                    conn.close()
            elif cmd_type == 'OLIVE_SELF_CMD':
                if cmd == 'RESTART_CLIENT':
                    conn.send(encrypt('client process will restart!'))
                    conn.close()
                    Restart_client()
                elif cmd == 'UPDATE_SHFILE':
                    if AUTO_UPDATE_SHFILE == 'YES' and AUTO_GET_SHFILE == 'YES':
                        conn.send(encrypt('client shfile will update!'))
                        conn.close()
                        Update_Shfile()
                    else:
                        conn.send(('this client not allow auto update or auto get shfile!'))
                        conn.close()
                else:
                    conn.close()
            elif cmd_type == 'OLIVE_FILE_DATA':
                    global FILE_DATA_SOCK
                    FILE_DATA_SOCK = conn
            else:
                conn.close()
                Save_Log('Warning!',cmd_type + ' cmd_type form is err!')
    else:
        conn.close()
        Save_Log('Warning!',recv + ' recvdata form is err!')

#继承守护进程类重写run函数
class My_daemon(Daemon):
    def run(self):
        Client_Ip = Get_Client_Ip()[1]
        t = threading.Thread(target=Mon_Task)
        t.setDaemon(True)
        t.start()
        s = New_Socket()
        optval = struct.pack("ii",1,0)
        s.setsockopt(socket.SOL_SOCKET,socket.SO_LINGER,optval)
        s.setsockopt( socket.SOL_SOCKET, socket.SO_REUSEADDR, 1 )
        s.bind((Client_Ip,BIND_PORT))
        s.listen(5)
        while RUN_STATE:
            conn,addr = s.accept()
            if addr[0] in SERVER_IPS:
                recv_byte = Recv_Data(conn)
                #print(recv_byte)
                recv_str = bytes2str(recv_byte)
                #print('recv data ----------------> ',recv_str)
                if recv_str == 'I love rill':
                    conn.close()
                    time.sleep(3)
                    global fernet_obj
                    fernet_obj = Get_server_key(SERVER_IP,SERVER_PORT)
                elif len(recv_byte) > 0 and recv_byte.endswith(ENCODE_END_BYTE):
                    recv_str = decrypt(recv_byte)
                    if recv_str.endswith(END_CMD_STR):
                        do_server_thread=threading.Thread(target = Do_Server,args=(conn,recv_str))
                        do_server_thread.setDaemon(True)
                        do_server_thread.start()
                    else:
                        Save_Log('Warning!',recv_str + ' CMD form err!')
                elif len(recv_byte) == 0:
                    pass
                else:
                    Save_Log('Warning!',recv_str + ' recvdata is null or no encrypt!')
            else:
                Save_Log('Warning!',addr[0] + ' try do something')
                conn.send('Get out!')
                conn.close()
        s.close()
        os.system(SH_DIR+'restart_client start ' + CLIENT_FILENAME)
        sys.exit()

if __name__ == '__main__':
    daemon = My_daemon(pidfile=PIDFILE,homedir=HOME_DIR,process_name=sys.argv[0])
    if len(sys.argv) == 2:
        if 'START' == (sys.argv[1]).upper():
            Test_Server()
            daemon.start()
        elif 'STOP' == (sys.argv[1]).upper():
            daemon.stop()
        elif 'RESTART' == (sys.argv[1]).upper():
            Test_Server()
            daemon.restart()
        else:
            print( "Unknow Command!")
            print( "Usage: %s start|stop|restart" % sys.argv[0])
            sys.exit(2)
        sys.exit(0)
    else:
        print( "Usage: %s start|stop|restart" % sys.argv[0])
        sys.exit(0)

