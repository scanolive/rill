#!/usr/bin/python
#encoding=utf-8
import os
import re
import atexit
import sys
import types
import time
import zipfile
import struct
import threading
import socket
from signal import SIGTERM
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.mime.application import MIMEApplication
from cryptography.fernet import Fernet
import rsa
import pickle
import subprocess
import smtplib
import string
import random
#import base64
import hashlib
import datetime
try:
    import pymysql as MySQL
    from pymysql import cursors as mysql_cur
except:
    import MySQLdb as MySQL
    from MySQLdb import cursors as mysql_cur
if sys.version_info[0] == 2:
    reload(sys)
    sys.setdefaultencoding('utf-8')
    import urllib2 as url_req
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
    import urllib.request as url_req
    def pickle_loads(obj):
         return pickle.loads(obj,encoding='bytes')
    def bytes2str(bytes_or_str):
        if isinstance(bytes_or_str,bytes):
            s = bytes_or_str.decode()
        elif isinstance(bytes_or_str,str):
            s = bytes_or_str
        return s

#基本配置
'''--------------------------------------------------------------------------------------'''
#数据库配置 必须修改
DB_HOST = '127.0.0.1'
DB_NAME = 'olive'
DB_USER = 'olive'
DB_PASSWD = 'olive'
DB_PORT = 3306
#UNIX_SOCKET = '/tmp/mysql.sock'

#加密配置,请配置客户端于此保持一致 建议修改
AUTH_KEY = 17
#OFFSET = 17
JAMSTR = '!@!'
ENCODE_END_STR = 'OLIVE_EOS'

#用于字符串分解,须与OCT和PHP一致 建议修改
END_CMD_STR = 'OLIVE_EOC'
OCT_CMD_PRE = 'OLIVE_CTRL_CENTER_CMD'
OCT_BAT_PRE = 'OLIVE_CTRL_CENTER_BAT'
SEP_STR = '@!@'
SEP_STR2 = "R!I@L#L"
#页面监控,curl检测超时,根据需要修改
TIMEOUT = 10

#绑定监听本机的IP,0.0.0.0为本机所有ip,根据需要修改
BIND_IP = '0.0.0.0'
#绑定监听本机的端口,建议修改
BIND_PORT = 33331
#客户端端口,建议修改
CLIENT_PORT = 22221
#客户端默认监控端口(如客户端开启有以下端口,则默认监控),若无法从数据库中获取则使用此值
DEF_MON_PORTS = "80,3306,21,8080"

#路径设置,根据需要修改
HOME_DIR = sys.path[0] + '/'
SH_DIR = HOME_DIR + 'shell_file/'
LOG_DIR = HOME_DIR + 'log/'
ZIPFILE_DIR = HOME_DIR + 'zipfile/'
PIDFILE = HOME_DIR + 'my_server.pid'
LOG_FILE = LOG_DIR + 'my_server.log'
CLIENT_FILE = HOME_DIR + 'client_file.py'
#数据库中监控数据保存的天数,据需要修改
EXPIRE_MONITOR_DAYS = 300
#OCT(olive ctrl center)命令超时时间,单位秒,据需要修改
OCT_TIMEOUT = 10
#monweb检测间隔默认值,若无法从数据库中获取则使用此值
DEF_MONWEB_INTERVAL = 60



BLOCK_SIZE = 1024000
PAGE_SIZE = 4096

#定义函数
'''-----------------------------------------------------------------------------------------'''


#加密解密函数

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
    c = c + ENCODE_END_STR.encode()
    return c


def decrypt(s):
    s = s.rstrip(ENCODE_END_STR)
    if s.endswith(END_CMD_STR):
        return s.decode("utf-8")
    key = AUTH_KEY
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
    b = b.decode("utf-8")
    return b.strip()
'''

def decrypt(s):
    #print('jiemi',s)
    s = s.replace(ENCODE_END_STR,'')
    if s.endswith(END_CMD_STR):
        return s.decode("utf-8")
    c = fernet_obj.decrypt(s.encode())
    return c.decode("utf-8")

def encrypt(s):
    #print('jiami',s)
    #f = Fernet(server_key)
    c = fernet_obj.encrypt(s.encode("utf-8"))
    c = c + ENCODE_END_STR.encode("utf-8")
    #print(c)
    return c

#随机函数
def random_time():
    a =  random.randint(1, 200)
    b = a/2000.000
    return b

#获取本机所有ip的函数
def get_all_ips():
    cmd = "ip -4 address | grep ' inet ' | grep -vE 'docker0|tun' |awk '{print $2}'|awk -F '/' '{print $1}'"
    #cmd = "ifconfig|grep 'inet addr:'|grep -v '255.255.255.255'|cut -d: -f2|awk '{ print $1}'"
    dae = subprocess.Popen(cmd,stdout=subprocess.PIPE,stderr=subprocess.PIPE,shell=True)
    Cmd_Run_Err = dae.stderr.read().decode()
    Cmd_Run_Out = dae.stdout.read().decode()
    dae.stdout.close()
    dae.stderr.close()
    dae.wait()
    if Cmd_Run_Err == "":
        return Cmd_Run_Out.split()
    else:
        return ['127.0,0,1']

#发送邮件函数，需要从数据库中取得用于发送邮件的帐号信息

def Send_Mail(address,subject,content):
    sql = "select id,Name,Host,Port,Passwd,Address,SendName from mail_config limit 1"
    rs = Select(sql,'dict')
    if rs[0] and len(rs[1])>0 and len(address)>0:
        rs = rs[1]
        SMTPserver = rs[0]['Host'].strip()
        FROM = rs[0]['Address'].strip()
        USER = rs[0]['Name'].strip()
        PORT = rs[0]['Port']
        PASS = rs[0]['Passwd'].strip()
        to = ','.join(address)
        main_msg = MIMEMultipart()
        contype = 'application/octet-stream'  
        maintype, subtype = contype.split('/', 1)
        text_msg = MIMEText(content+'\n\n\n',_charset="utf-8")  
        main_msg.attach(text_msg)

        main_msg['to'] = to
        main_msg['from'] = FROM
        main_msg['subject'] = subject    
        fullText = main_msg.as_string( )

        send_num = 0
        while send_num < 2:
            smtp_server = smtplib.SMTP(SMTPserver,str(PORT))
            try:
                smtp_server.login(USER,PASS)
                smtp_server.sendmail(FROM, address, fullText)
                send_num = 100
            except smtplib.SMTPException  as  e:
                send_num = send_num + 1
                print( str(e))
                time.sleep(5)
            finally:
                smtp_server.quit()
                time.sleep(2)

    else:
        Save_Log('Send_Mail','The Db hava not config for mail or address is null!')


#发送短信的函数，参数为手机号和信息内容，其中手机号为列表和字符串，列表为多个手机号
def Send_Message(mobiles,msg):
    Url = 'http://sms.tom.com/via/via_mt.php?send_time=1018514142&site_id=tomnet&sign=32d499a525c0d57d80d8bf015c97daa8&mobile_no='
    url_host = Url.split('/')[2]
    if Ping_Ip(url_host) == 'alive':
        if type(mobiles) is types.StringType:
            mobiles = mobiles.split('!@#$%^&*')
        for mobile in mobiles:
            Url = Url + mobile +'&msg=' + msg.replace('&','%26')
            try:
                res = url_req.urlopen(Url).read()
                if res == '<hr>succFAIL':
                    Save_Log('Send_Message',str(mobile)+' '+msg+' Send succeed!')
                else:
                    Save_Log('Send_Message',str(mobile)+' '+msg+' Send failed!')
            except Exception as e:
                Save_Log('Send_Message',str(e)+Url)
    else:
        Save_Log('Send_Message','send message URL is Err!')

#保存日志函数，参数为日志类型和内容
def Save_Log(log_type, data):
    logdir = LOG_DIR
    logfile = LOG_FILE
    if not os.path.exists(logdir):
        os.system('mkdir -p ' + logdir)
    f = open(logfile, 'a')
    f.write(time.strftime('%Y-%m-%d %H:%M:%S', time.localtime()) + '|' + log_type + '|' + str(data) + '\n')
    f.close()
    try:
        #conn = MySQL.connect(host=DB_HOST,db=DB_NAME,user=DB_USER,passwd=DB_PASSWD,port=DB_PORT,unix_socket=UNIX_SOCKET)
        conn = MySQL.connect(host=DB_HOST,db=DB_NAME,user=DB_USER,passwd=DB_PASSWD,port=DB_PORT)
    except Exception  as e:
        conn = False
    if log_type != 'Conn_Db' and conn:
        sql = "insert err_logs set LogType='" + log_type + "', LogContent='" + str(data).replace("'","''") + "'"
        try:
            cursor = conn.cursor()
            cursor.execute(sql)
            conn.commit()
            conn.close()
            return 1
        except Exception  as e:
            f = open(logfile, 'a')
            f.write(time.strftime('%Y-%m-%d %H:%M:%S', time.localtime()) + '| add_err_logs  |' + str(e) + '\n')
            f.close()
    else:
        pass
def Purge_Monitor(days):
    sql = 'select Ipid from delipid where DelMon=0'
    delipid_rs = Select(sql)
    if delipid_rs and len(delipid_rs[1])>0:
        del_ipids = ''
        for i in delipid_rs[1]:
            del_ipids = del_ipids + str(i[0]) + ','
        del_ipids = '(' + del_ipids[:-1] + ')'
        del_mon_sql = 'delete from monitor where ipid in ' + del_ipids
        Do_Sqls(del_mon_sql)
        Do_Sqls('update delipid set DelMon=1 where ipid in ' + del_ipids)
    else:
        pass
    if days and days >= 30:
        purge_date = time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(time.time()-days*24*3600))
        purge_sql = "delete from monitor where MonTime < '" + purge_date  + "'"   
        Do_Sqls(purge_sql)
    else:
        pass

#打包压缩函数，参数为目录和要压缩文件名
def Zip_Dir(dirname,zipfilename):
    filelist = []
    if os.path.isfile(dirname):
        filelist.append(dirname)
    else:
        for root,dirs,files in os.walk(dirname):
            for name in files:
                filelist.append(os.path.join(root,name))

    zf = zipfile.ZipFile(zipfilename, "w", zipfile.zlib.DEFLATED)
    for tar in filelist:
        arcname = tar[len(dirname):]
        zf.write(tar,arcname)
    zf.close()

#解压缩函数，参数为压缩文件名和解压目录
def Unzip_File(zipfilename,unziptodir):
    if not os.path.exists(unziptodir):
        os.mkdir(unziptodir)
    zfobj = zipfile.ZipFile(zipfilename)
    for name in zfobj.namelist():
        name = name.replace('\\','/')

        if name.endswith('/'):
            os.mkdir(os.path.join(unziptodir,name))
        else:
            ext_filename = os.path.join(unziptodir,name)
            ext_dir= os.path.dirname(ext_filename)
            if not os.path.exists(ext_dir): 
                os.mkdir(ext_dir)
            outfile = open(ext_filename,'wb')
            outfile.write(zfobj.read(name))
            outfile.close()

#向客户端发送文件函数，参数为客户端ip和文件名
def S_File(ip,filename,savename,filetype,overwrite):
    def S_Block(data,socket,b_size,p_size):
        def S_data(data,socket,b_size,p_size):
            for i in range(0,b_size/p_size):
                s_date = data[i*p_size:(i+1)*p_size]
                socket.send(s_data)
            return_str = decrypt(socket.recv(1024).decode())
            if return_str == "YES":
                return True
            else:
                return False

        m = hashlib.md5()
        m.update(data)
        md5str = m.hexdigest()
        socket.send(encrypt(md5str))
        return_str = decrypt(socket.recv(1024).decode())
        s_num = 0
        if return_str == "YES":
            while True:
                s_data_result = S_data(data,socket,b_size,p_size)
                if s_data_result:
                    break
                else:
                    s_num = s_num + 1
        else:
            pass

def Send_File(ip,filename,savename,filetype,overwrite):
    def S_Block(data,ctrl_socket,data_socket,b_size,p_size,ip):
        m = hashlib.md5()
        m.update(data)
        md5str = m.hexdigest()
        s_num = 0
        while True:
            ctrl_socket.send(encrypt('MD5STR'+SEP_STR+md5str+SEP_STR+str(b_size)))
            return_str = decrypt(ctrl_socket.recv(1024).decode())
            if return_str == "Please send data":
                for i in range(0,b_size//p_size+1):
                    s_data = data[i*p_size:(i+1)*p_size]
                    data_socket.send(s_data)
                return_str = decrypt(ctrl_socket.recv(1024).decode())
                if return_str == "data is OK":
                    break
                else:
                    data_socket.close()
                    data_socket.connect((ip,CLIENT_PORT))
                    data_socket.send(encrypt('OLIVE_FILE_DATA' + SEP_STR + 'file_data' + SEP_STR + END_CMD_STR))
                    s_num = s_num + 1
                    time.sleep(5)
            else:
                pass

    def send_data(send_file,ctrl_socket,data_socket,BLOCK_SIZE,ip):
        f_size = os.stat(filename).st_size
        fp = open(send_file,'rb')
        B_num = f_size/BLOCK_SIZE + 1
        S_num = 0
        L_size = f_size
        while True:
            if L_size <= BLOCK_SIZE:
                filedata = fp.read(BLOCK_SIZE)
                S_Block(filedata,ctrl_socket,data_socket,len(filedata),PAGE_SIZE,ip)
                break
            else:
                filedata = fp.read(BLOCK_SIZE)
                S_Block(filedata,ctrl_socket,data_socket,len(filedata),PAGE_SIZE,ip)
            L_size = L_size - len(filedata)
            
        fp.close()
        ctrl_socket.send(encrypt('file have send done!'))

    if Check_Port(ip,CLIENT_PORT):
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        data_sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        try:
            sock.connect((ip,CLIENT_PORT))
            data_sock.connect((ip,CLIENT_PORT))
            sock.send(encrypt('OLIVE_SERVER_RECV_FILE' + SEP_STR + savename + SEP_STR + filetype + SEP_STR + str(overwrite) + SEP_STR + END_CMD_STR))
            data_sock.send(encrypt('OLIVE_FILE_DATA' + SEP_STR + 'file_data' + SEP_STR + END_CMD_STR))
            FILEINFO_SIZE = struct.calcsize('128sI')
            file_name = os.path.split(filename)[1].encode()
            fhead = struct.pack('128sI',file_name,os.stat(filename).st_size)
            sock.send(fhead)
            recv = sock.recv(65536).decode() 
            client_return = decrypt(recv)
            send_num = 0
            if client_return == 'can be send':    
                send_num = 0
                com_yorn = 'file is err'
                while send_num < 300 and com_yorn == 'file is err':
                    send_data(filename,sock,data_sock,BLOCK_SIZE,ip)
                    send_num = send_num + 1
                    com_yorn = decrypt(sock.recv(65536).decode())
                sock.close()
                if com_yorn == 'file is err':
                    result = [0,'failed']
                else:    
                    result = [1,'successful']
            else:
                result = [0,client_return]
                Save_Log('Send_File',ip + ' ' + filename + ' client say ' +  client_return)
        except Exception  as e:
            Save_Log('Send_File',ip+str(e))
            result = [0,str(e)]
    else:
        Save_Log('Send_File',ip + ' ' + filename + ' Client_Port is err')
        result = [0,'Client_Port is err!']
    return result

#数据库连接函数，返回连接
def Db_connect():
    global Db_Err_Time
    try:
        conn = MySQL.connect(host=DB_HOST,db=DB_NAME,user=DB_USER,passwd=DB_PASSWD,port=DB_PORT)
        if conn:
            Db_Err_Time = None
            return conn
        else:
            Db_Err_Time = time.time()
            return False
    except Exception  as e:
        Save_Log('Conn_Db',str(e))
        Db_Err_Time = time.time()
        return False
#数据库连接函数，返回连接
def Db_connect_dict():
    global Db_Err_Time
    try:
        conn = MySQL.connect(host=DB_HOST,db=DB_NAME,user=DB_USER,passwd=DB_PASSWD,port=DB_PORT,cursorclass = MySQL.cursors.DictCursor)
        if conn:
            Db_Err_Time = None
            return conn
        else:
            Db_Err_Time = time.time()
            return False
    except Exception  as e:
        Save_Log('Conn_Db',str(e))
        Db_Err_Time = time.time()
        return False


#数据库查询函数，参数为select语句，返回数据集
def Select(*args):
    if len(args) == 1:
        conn = Db_connect()
    else:
        conn = Db_connect_dict()
    if conn:
        sql = args[0]
        try:
            cursor = conn.cursor()
            cursor.execute(sql)
            rs = cursor.fetchall()
            cursor.close()
            conn.close()
            return [True,rs]
        except Exception  as e:
            Save_Log('Select',sql+str(e))
            return [False,[]]
    else:
        return [False,[]]

#数据库操作函数，参数为insert或update语句，可以为列表或字符串，返回操作结果

def Do_Sqls(sqls):
    #if type(sqls) is types.StringType:
    if not isinstance(sqls,list):
        sqls = sqls.split('!@#$%^&*')
    conn = Db_connect()
    if conn:
        try:
            cursor = conn.cursor()
            for sql in sqls:
                cursor.execute(sql)
            conn.commit()
            conn.close()
            return 1
        except Exception  as e:
            Save_Log('Do_Sqls',sql + ' ' + str(e))
            return 0
    else:
        pass

def Cron_Do():
    while True:
        conn = Db_connect()
        if conn:
            conn.close()
            time_now = time.strftime('%H:%M',time.localtime(time.time()))
            time_hour = time_now.split(':')[0]
            time_min = time_now.split(':')[1]
            if time_now == '00:05':
                Check_Service_All()
            elif time_now == '03:00':
                Save_Info_Day()
            elif time_now == '01:30':
                Check_Devinfo_All()
            elif time_now == '00:30':
                Purge_Monitor(EXPIRE_MONITOR_DAYS)
            else:
                pass
            pass
        else:
            pass
        time.sleep(40)
        #Init()


def List_Rs(in_rs,n):
    if in_rs and len(in_rs) > 0:
        if n <= 99 and len(in_rs[0])-1 >= n:
            out_rs = []
            for i in in_rs:
                out_rs.append(str(i[n]))
        else:
            out_rs = []
            for i in in_rs:
                tmp = []
                for j in range(len(i)):
                    tmp.append(str(i[j]))
                out_rs.append(tmp)
    else:
        out_rs = []
    return out_rs
        
def Send_Alarm(alarm_id):
    conn = Db_connect()
    if conn:
        conn.close()
        #alarm = dict_alarms_monweb.get(int(alarm_id),{})
        alarm = dict_alarms.get(int(alarm_id),{})
        if alarm:
            alarm_msg = alarm['Msg']
            alarm_type = alarm['Type']
            alarm_msg = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime()) + ' ' + alarm_msg
            gid = str(alarm['Gid'])
            get_notice_sql = "select UserMail,UserMobile,NoticeLevel from users,userofgroup where userofgroup.Uid = users.id and hour(now())>left(DutyTime,2) and right(DutyTime,2)>hour(now()) and locate(weekday(now()),DutyDate) and userofgroup.Gid=" + gid
            get_admin_notice = "select UserMail, UserMobile,NoticeLevel from users where UserType != 'user' and hour(now())>left(DutyTime,2) and right(DutyTime,2)>hour(now()) and locate(weekday(now()),DutyDate)"
            notice_admin_rs = Select(get_admin_notice,'dict')
            notice_admin_rs = notice_admin_rs[1]
            notice_rs = Select(get_notice_sql,'dict')
            notice_rs = notice_rs[1]
            mail_adr = ''
            mobile = ''
            for notice in notice_rs:
                if notice['NoticeLevel'] == 4: 
                    mail_adr += notice['UserMail']+' '
                    mobile += notice['UserMobile']+' '
                elif notice['NoticeLevel'] == 2:
                    mail_adr += notice['UserMail']+' '
                elif notice['NoticeLevel'] == 3:
                    mobile += notice['UserMobile']+' '
                else:
                    pass
            for notice in notice_admin_rs:
                if notice['NoticeLevel'] == 4: 
                    mail_adr += notice['UserMail']+' '
                    mobile += notice['UserMobile']+' '
                elif notice['NoticeLevel'] == 2:
                    mail_adr += notice['UserMail']+' '
                elif notice['NoticeLevel'] == 3:
                    mobile += notice['UserMobile']+' '
                else:
                    pass
            mail_adrs = mail_adr.split()
            mobiles = mobile.split()
            mail_adrs = list(set(mail_adrs))
            mobiles = list(set(mobiles))
            if len(mobiles)>0:
                Send_Message(mobiles,alarm_msg)
            if len(mail_adrs)>0:
                Send_Mail(mail_adrs,'Olive alarm',alarm_msg)
            update_issend_sql = "update alarms set IsSend=1,SendTime=now() where id="+str(alarm_id)
            Do_Sqls(update_issend_sql)
        else:
            pass
    else:
        pass

#查询数据中报警相关表的对应id，参数为ip和报警类型
def Get_Alarm_Id(ip,alarm_type):
    ipid = Get_Ipid(ip)
    get_alarm_id_sql = "select id from alarms where Ipid='"+str(ipid)+"' and Type='"+alarm_type+"' and IsBeOk=0 and IsAlarm=1 order by id desc limit 1"
    rs = Select(get_alarm_id_sql,'dict')





#发送报警函数，间隔查询数据库，取得报警信息并发送
def Send_Alarms():
    while True:
        conn = Db_connect()
        if conn:
            conn.close()
            get_alarms_sql = "select alarms.id,Msg,GroupId,Type,HostName from alarms left join ipinfo on alarms.Ipid=ipinfo.id  Left Join devinfo ON devinfo.Ipid=alarms.Ipid where IsSend=0;"
            alarms_rs = Select(get_alarms_sql,'dict')
            if alarms_rs[0] and len(alarms_rs[1])>0:
                alarms_rs = alarms_rs[1]
                for alarm in alarms_rs:
                    alarm_id = alarm['id']
                    alarm_msg = alarm['Msg']
                    alarm_type = alarm['Type']
                    alarm_hostname = alarm['HostName']
                    if alarm_type != "monweb":
                        alarm_msg = str(alarm_hostname) + ' ' + alarm_msg 
                    else:
                        pass

                    alarm_msg = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime()) + ' ' + alarm_msg
                    print( alarm_msg)
                    gid = str(alarm['Gid'])
                    get_notice_sql = "select UserMail,UserMobile,NoticeLevel from users,userofgroup where userofgroup.Uid = users.id and hour(now())>left(DutyTime,2) and right(DutyTime,2)>hour(now()) and locate(weekday(now()),DutyDate) and userofgroup.Gid=" + gid
                    get_admin_notice = "select UserMail, UserMobile,NoticeLevel from users where UserType != 'user' and hour(now())>left(DutyTime,2) and right(DutyTime,2)>hour(now()) and locate(weekday(now()),DutyDate)"
                    notice_admin_rs = Select(get_admin_notice,'dict')
                    notice_admin_rs = notice_admin_rs[1]
                    notice_rs = Select(get_notice_sql,'dict')
                    notice_rs = notice_rs[1]
                    mail_adr = ''
                    mobile = ''
                    for notice in notice_rs:
                        if notice['NoticeLevel'] == 4: 
                            mail_adr += notice[0]+' '
                            mobile += notice[1]+' '
                        elif notice['NoticeLevel'] == 2:
                            mail_adr += notice['UserMail']+' '
                        elif notice['NoticeLevel'] == 3:
                            mobile += notice['UserMobile']+' '
                        else:
                            pass
                    for notice in notice_admin_rs:
                        if notice['NoticeLevel'] == 4: 
                            mail_adr += notice['UserMail']+' '
                            mobile += notice['UserMobile']+' '
                        elif notice['UserMail'] == 2:
                            mail_adr += notice['UserMail']+' '
                        elif notice['NoticeLevel'] == 3:
                            mobile += notice['UserMobile']+' '
                        else:
                            pass
                    mail_adrs = mail_adr.split()
                    mobiles = mobile.split()
                    mail_adrs = list(set(mail_adrs))
                    mobiles = list(set(mobiles))
                    if len(mobiles)>0:
                        Send_Message(mobiles,alarm_msg)
                    if len(mail_adrs)>0:
                        Send_Mail(mail_adrs,'Olive alarm',alarm_msg)
                    update_issend_sql = "update alarms set IsSend=1,SendTime=now() where id="+str(alarm_id)
                    Do_Sqls(update_issend_sql)
            else:
                pass
        else:
            pass
        time.sleep(60)

#查询数据中报警相关表的对应id，参数为ip和报警类型
def Get_Alarm_Id(ip,alarm_type):
    ipid = Get_Ipid(ip)
    get_alarm_id_sql = "select id from alarms where Ipid='"+str(ipid)+"' and Type='"+alarm_type+"' and IsBeOk=0 and IsAlarm=1 order by id desc limit 1"
    rs = Select(get_alarm_id_sql,'dict')
    if rs[0] and len(rs[1])>0:
        rs = rs[1]
        alarm_id = str(rs[0]['id'])
    else:
        alarm_id = ''
    return alarm_id 

#检测监控数据后插入报警及更新相关项的操作函数
def Update_Mon_Sql(ip,gid,mon_type,msg,alarm_id):
    ipid = Get_Ipid(ip)
    global MaxAlarmId
    if glo_lock.acquire(1):
        insert_alarms_id = MaxAlarmId + 1
        MaxAlarmId = MaxAlarmId + 1
        glo_lock.release()
    if alarm_id == '':
        alarm_data = {'Ipid':ipid,'CreateTime':'NULL','UpdateTime':'NULL','Type':mon_type,'Msg':msg,'IsBeOk':0,'IsAlarm':1,'Gid':gid,'id':insert_alarms_id}
        Add_Dict(dict_alarms,'alarms',insert_alarms_id,alarm_data)
        ipinfo_alarms_ids = dict_ipinfo.get(ip,{}).get('Alarms_Ids',None)
        if ipinfo_alarms_ids:
            alarms_ids_str = ipinfo_alarms_ids + ',' + str(insert_alarms_id) 
        else:
            alarms_ids_str = str(insert_alarms_id)
        data_dict_ipinfo = {'Alarms_Ids':alarms_ids_str}
        Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict_ipinfo,1)
        Send_Alarm(insert_alarms_id)
    else:
        alarm_data = {'Ipid':ipid,'CreateTime':'NULL','UpdateTime':'NULL','Type':mon_type,'Msg':msg,'Gid':gid,'IsBeOk':1,'IsAlarm':0,'id':insert_alarms_id}
        Add_Dict(dict_alarms,'alarms',insert_alarms_id,alarm_data)
        alarm_data = {'IsBeOk':1,'UpdateTime':'NULL'}
        Update_Dict(dict_alarms,'alarms',int(alarm_id),'id',alarm_data,1)
        ipinfo_alarms_ids = dict_ipinfo.get(ip,{}).get('Alarms_Ids',None)
        alarms_ids_str = ''
        if ipinfo_alarms_ids:
            for i in ipinfo_alarms_ids.split(','):
                if i != alarm_id:
                    alarms_ids_str = alarms_ids_str + i + ','
        alarms_ids_str = alarms_ids_str[:-1]            
        data_dict_ipinfo = {'Alarms_Ids':alarms_ids_str}
        Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict_ipinfo,1)
        Send_Alarm(insert_alarms_id)

#检测客户端发送来的数据，参数为ip和客户端数据
def Check_Mon(ip,mondata):
    ipinfo = dict_ipinfo.get(ip,{})
    alarms_rs = Get_Alarms_rs(ip)
    gid = ipinfo.get('GroupId',1)
    if 'client' in alarms_rs:
        data_dict = {'ClientStatus':1}
        Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict,1)
        alarms_ids = dict_ipinfo.get(ip,{}).get('Alarms_Ids',None)
        alarm_id = ''
        if alarms_ids:
            alarms_ids_rs = alarms_ids.split(',')
            for i in alarms_ids_rs:
                if dict_alarms.get(int(i),{}).get('Type') == 'client':
                    alarm_id = str(i)
        Msg = ip + ' client is OK'
        Update_Mon_Sql(ip,gid,'client',Msg,alarm_id)

    def_ipinfo = dict_ipinfo.get('0.0.0.0',{})
    ip_enable = ipinfo.get('Enable',None)
    ip_alarm = ipinfo.get('Alarms_Ids',None)
    if mondata.find('{')>=0 and mondata != 'null' and ip_enable:
        mondata = eval(mondata)
        if 'network' in mondata and 'diskstat' in mondata  and 'process_num' in mondata and 'loadstat' in mondata and 'constat' in mondata and 'memory' in mondata and  'login' in mondata:
            rs = [def_ipinfo,ipinfo]
            if rs[1]['id']:
                ipid = str(rs[1]['id'])
            if rs[1]['GroupId']:
                gid = str(rs[1]['GroupId'])
            if rs[1]['Cpu_Pro']:
                cpu_pro = rs[1]['Cpu_Pro']
            else:    
                cpu_pro = 4
            if rs[1]['Alarms_Ids'] == None or rs[1]['Alarms_Ids'] == '':
                alarms_id_type = {}
            else:
                alarms_ids = rs[1]['Alarms_Ids']
                alarms_rs = alarms_ids.split(',')
                alarms_id_type = {}
                for i in alarms_rs:
                    alarm_type =  dict_alarms.get(int(i),{}).get('Type','') 
                    alarms_id_type[alarm_type] = i

            load_warn_msg = ip + ' Load Warning! '
            disk_warn_msg = ip + ' Disk Warning! '
            login_warn_msg = ip + ' Login Warning! '
            network_warn_msg = ip + ' Network Warning! '
            process_warn_msg = ip + ' Process Warning! '
            connect_warn_msg = ip + ' Connect Warning! '

            load_alarm_id = alarms_id_type.get('load','')
            disk_alarm_id = alarms_id_type.get('disk','')
            login_alarm_id = alarms_id_type.get('login','')
            network_alarm_id = alarms_id_type.get('network','')
            process_alarm_id = alarms_id_type.get('process','')
            connect_alarm_id = alarms_id_type.get('connect','')

            if rs[1]['LoadLevel'] == 0:
                load_level = rs[0]['LoadLevel']
            else:
                load_level = rs[1]['LoadLevel']
            if rs[1]['DiskLevel'] == 0:
                disk_level = rs[0]['DiskLevel']
            else:
                disk_level = rs[1]['DiskLevel']
            if rs[1]['LoginLevel'] == 0:
                login_level = rs[0]['LoginLevel']
            else:
                login_level = rs[1]['LoginLevel']
            if rs[1]['NetworkLevel'] == 0:
                network_level = rs[0]['NetworkLevel']
            else:
                network_level = rs[1]['NetworkLevel']
            if rs[1]['ConnectLevel'] == 0:
                connect_level = rs[0]['ConnectLevel']
            else:
                connect_level = rs[1]['ProcessLevel']
            if rs[1]['ProcessLevel'] == 0:
                process_level = rs[0]['ProcessLevel']
            else:
                process_level = rs[1]['ProcessLevel']

            for i in mondata.get('diskstat'):
                disk_used = mondata.get('diskstat').get(i).get('used')*100/mondata.get('diskstat').get(i).get('total')
                if disk_used > disk_level:
                    disk_warn_msg += i + ' ' + str(disk_used) + '% '
            if disk_warn_msg != ip + ' Disk Warning! ' and  disk_alarm_id == '':
                Update_Mon_Sql(ip,gid,'disk',disk_warn_msg,'')
            elif disk_warn_msg == ip + ' Disk Warning! ' and  disk_alarm_id != '':
                disk_warn_msg += 'is OK '
                Update_Mon_Sql(ip,gid,'disk',disk_warn_msg,disk_alarm_id)
            else:
                pass
            for j in mondata.get('loadstat'):
                load = mondata.get('loadstat').get(j)
                if load > load_level*cpu_pro/100.0:
                    load_warn_msg += str(j) + ' Mins Load ' + str(load) + " "
            if load_warn_msg != ip + ' Load Warning! ' and load_alarm_id == '':
                Update_Mon_Sql(ip,gid,'load',load_warn_msg,load_alarm_id)
            elif load_warn_msg == ip + ' Load Warning! ' and load_alarm_id != '':
                load_warn_msg += 'is OK '
                Update_Mon_Sql(ip,gid,'load',load_warn_msg,load_alarm_id)
            else:
                pass
        
            if mondata.get('login') >= login_level and login_alarm_id == '':
                login_warn_msg +=  str(mondata.get('login')) +  ' > ' + str(login_level)
                Update_Mon_Sql(ip,gid,'login',login_warn_msg,login_alarm_id)
            elif mondata.get('login') < login_level and login_alarm_id != '':
                login_warn_msg += 'is OK ' +  str(mondata.get('login')) +  ' < ' + str(login_level)
                Update_Mon_Sql(ip,gid,'login',login_warn_msg,login_alarm_id)
            else:
                pass
        
            if mondata.get('constat').get('allnum') >= connect_level and  connect_alarm_id == '':
                connect_warn_msg += str(mondata.get('constat').get('allnum')) +  ' > ' + str(connect_level)
                Update_Mon_Sql(ip,gid,'connect',connect_warn_msg,connect_alarm_id)
            elif mondata.get('constat').get('allnum') < connect_level and connect_alarm_id != '':
                connect_warn_msg += 'is OK ' + str(mondata.get('constat').get('allnum')) +  ' < ' + str(connect_level)    
                Update_Mon_Sql(ip,gid,'connect',connect_warn_msg,connect_alarm_id)
            else:
                pass

            if mondata.get('process_num') >= process_level and process_alarm_id == '':
                process_warn_msg += str(mondata.get('process_num')) +  ' > ' + str(process_level)
                Update_Mon_Sql(ip,gid,'process',process_warn_msg,process_alarm_id)
            elif mondata.get('process_num') < process_level and process_alarm_id != '':
                process_warn_msg += 'is OK ' + str(mondata.get('process_num')) +  ' < ' + str(process_level)    
                Update_Mon_Sql(ip,gid,'process',process_warn_msg,process_alarm_id)
            else:
                pass

            for eth in mondata.get('network',''):
                traffic = mondata.get('network').get(eth).get('outbond')/1024
                if traffic > network_level:
                    network_warn_msg += eth + ' ' + str(int(traffic)) + 'kb/s > ' + str(network_level) + 'kb/s'
            if network_warn_msg != ip + ' Network Warning! ' and network_alarm_id == '':
                Update_Mon_Sql(ip,gid,'network',network_warn_msg,network_alarm_id)
            elif network_warn_msg == ip + ' Network Warning! ' and network_alarm_id != '':
                network_warn_msg += 'is OK' 
                Update_Mon_Sql(ip,gid,'network',network_warn_msg,network_alarm_id)
            else:
                pass
        else:
            Save_Log('Check_Mon',ip + ' Mondata is imperfect!')            
    elif not (mondata.find('{')>=0 and mondata != 'null'):
        Save_Log('Check_Mon',ip + ' Mondata is Err!' + mondata)            
    else:
        pass

#从数据中查询ip的id
def Get_Ipid(ip):
    global dict_ipinfo
    if dict_ipinfo.get(ip,None):
        return dict_ipinfo.get(ip,None).get('id',None)
    else:
        return None

#插入监控数据到表的函数
def Insert_Monitor(ipid,ip,montext):    
    insert_monitor_sql = "insert into monitor set Ipid = " + str(ipid) + " ,MonText = '" + montext + "'"
    time_now = int(time.time())
    data_dict = {'LastCheckTime':time_now,'ClientStatus':1,'IsAlive':'alive'}
    if dict_ipinfo.get(ip,{}).get('ClientStatus') == 1 and dict_ipinfo.get(ip,{}).get('IsAlive') == 'alive':
        Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict,0)    
    else:
        data_dict = {'LastCheckTime':time_now}
        Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict,0)    
        data_dict = {'ClientStatus':1,'IsAlive':'alive'}
        Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict,1)    
    result = Do_Sqls(insert_monitor_sql)
    if result == 0:
        Save_Log('Insert_Monitor',str(ipid) + ' Add Mondata Err!')
    else:
        pass

#插入ip到数据库，用于客户端发送数据
def Insert_Ip(ip,ipid):
    grpid = 1
    time_now = int(time.time())
    data = {'id':ipid,'Ip':ip,'GroupId':grpid,'IsAlive':'alive','Status':None,'ClientStatus':1,'LoadLevel':0,'DiskLevel':0,'NetworkLevel':0,'LoginLevel':0,'ProcessLevel':0,'ConnectLevel':0,'Cpu_Pro':1,'Enable':1,'Alarms_Ids':None}
    Add_Dict(dict_ipinfo,'ipinfo',ip,data)
    Check_Devinfo(ip,ipid)

#检测网络状况的函数，参数ip
def Ping_Ip(ip):
    cmd = 'ping '+ip+' -c 2 -W 2'
    dae = subprocess.Popen(cmd,stdout=subprocess.PIPE,stderr=subprocess.PIPE,shell=True)
    Cmd_Run_Err = dae.stderr.read().decode("utf-8")
    Cmd_Run_Out = dae.stdout.read().decode("utf-8")
    dae.stdout.close()
    dae.stderr.close()
    dae.wait()
    if Cmd_Run_Out.find('time=') >= 0:
        return 'alive'
    else:
        return 'down'


def Get_Alarms_rs(ip):
    alarms_ids = dict_ipinfo.get(ip,{}).get('Alarms_Ids',None)
    alarms_ids_rs = []
    if alarms_ids:
        for i in alarms_ids.split(','):
            alarms_ids_rs.append(dict_alarms.get(int(i),{}).get('Type',''))
    return alarms_ids_rs
    

#主函数之一，检测主机网络状态的函数
def Check_Alive():
    time.sleep(4)
    while True:
        conn = Db_connect()
        if conn:
            conn.close()
            time11ago = int(time.time()) - 360
            time6ago = int(time.time()) - 360
            time_now = int(time.time())
            for k,v in dict_ipinfo.items():
                ip = k
                ipid = v.get('id')
                gid = v.get('GroupId')
                alarms_ids = v.get('Alarms_Ids',None)
                # print( alarms_ids,type(alarms_ids))
                if v.get('LastCheckTime') < time_now - 60 and ip != '0.0.0.0':
                    ###print( ip,v.get('Alarms_Ids',''))
                #if v.get('LastCheckTime') < time_now - 360 and v.get('ClientStatus') == 1:
                    if v.get('ClientStatus') == 1:
                        if not Check_Port(ip,CLIENT_PORT):
                            data_dict = {'ClientStatus':0}
                            Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict,1)
                            alarms_rs = Get_Alarms_rs(ip)
                            if  not 'client' in alarms_rs:
                                Msg = ip + ' client is err'
                                Update_Mon_Sql(ip,gid,'client',Msg,'')
                            else:
                                data_dict = {'LastCheckTime':time_now}
                                Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict,0)
                                alarms_rs = Get_Alarms_rs(ip)
                                alarms_ids = dict_ipinfo.get(ip,{}).get('Alarms_Ids',None)
                                                        
                                if alarms_ids:
                                    alarms_ids_rs = alarms_ids.split(',')
                                    alarm_id = ''
                                    for i in alarms_ids_rs:
                                        alarms = dict_alarms.get(int(i),{})
                                        if alarms.get('Type') == 'isalive' and alarms.get('IsAlarm') == 1 and alarms.get('IsBeOk') == 0:
                                            alarm_id = str(i)

                                            data_dict =  {'IsAlive':'alive'}
                                            Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict,1)

                                            Msg = ip + ' is alive'
                                            Update_Mon_Sql(ip,gid,'isalive',Msg,alarm_id)
                                                                #pass
                                        #if get('Alarms_Ids','')                                    

                    if Ping_Ip(ip) == 'down':
                        data_dict =  {'IsAlive':'down'}
                        Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict,1)
                        alarms_rs = Get_Alarms_rs(ip)
                        if not 'isalive' in alarms_rs:
                            Msg = ip + ' is down'
                            Update_Mon_Sql(ip,gid,'isalive',Msg,'')
                    pass
                if  ip != '0.0.0.0' and v.get('IsAlive') == 'down' and v.get('Enable') == 1:
                    isalive_now = Ping_Ip(ip)
                    if isalive_now == 'alive':
                        data_dict =  {'IsAlive':'alive'}
                        Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',data_dict,1)
                        alarms_ids = dict_ipinfo.get(ip,{}).get('Alarms_Ids',None)
                        if alarms_ids:
                            alarms_ids_rs = alarms_ids.split(',')
                            alarm_id = ''
                            for i in alarms_ids_rs:
                                if dict_alarms.get(int(i),{}).get('Type') == 'isalive':
                                    alarm_id = str(i)
                        Msg = ip + ' is alive'
                                
                        Update_Mon_Sql(ip,gid,'isalive',Msg,alarm_id)            
                        
        else:
            pass
        time.sleep(30)

#检测端口存活函数，参数ip和端口
def Check_Port(address, port):
    s = socket.socket()
    try:
        s.connect((address, port))
        s.close()
        return True
    except socket.error as  e:
        return False


def Update_Client_Key(address, port):
    s = socket.socket()
    try:
        s.connect((address, port))
        s.send('I love rill'.encode())
        s.close()
    except socket.error as  e:
        pass
        #print(e)

def Monweb():
    def check_url(monweb_id,url,status_now,mutex):
        url_host = url.split('/')[2]
        #if Ping_Ip(url_host) == 'alive':
        if True:
            try:
                rstcode = url_req.urlopen(url,data=None,timeout=TIMEOUT).getcode()
                if rstcode != status_now:
                    dict_data = {'RstCode':rstcode}
            except Exception  as e:
                time.sleep(3)
                try:
                    rstcode = url_req.urlopen(url,data=None,timeout=TIMEOUT).getcode()
                except Exception  as e:
                    if str(e).find('502') >= 0:
                        rstcode = 502
                    elif str(e).find('timed out') >= 0:
                        rstcode = 600
                    elif str(e).find('404') >= 0:
                        rstcode = 404
                    elif str(e).find('500') >= 0:
                        rstcode = 500
                    else:
                        rstcode = None
                        Save_Log('check_url',url + ' ' + str(e))

            with mutex:
                if rstcode and rstcode != status_now:
                    dict_data = {'RstCode':rstcode}
                    Update_Dict(dict_monweb,'monweb',monweb_id,'id',dict_data,1)    
                    global MaxAlarmId
                    insert_alarms_id = MaxAlarmId + 1
                    if glo_lock.acquire(1):
                        MaxAlarmId = MaxAlarmId + 1
                        glo_lock.release()
                    Url = dict_monweb.get(monweb_id,{}).get('MonUrl','')
                    gid = dict_monweb.get(monweb_id,{}).get('Gid',1)
                    alarm_id = dict_monweb.get(monweb_id,{}).get('Alarms_Ids','')
                    if alarm_id == None:
                        alarm_id = ''
                    if rstcode == 200 and alarm_id != '':
                        Msg = Url + ' Check URL OK ' + str(rstcode)
                        alarm_data = {'Ipid':monweb_id,'CreateTime':'NULL','UpdateTime':'NULL','Type':'monweb','Msg':Msg,'IsBeOk':1,'IsAlarm':0,'Gid':gid,'id':insert_alarms_id}
                        Add_Dict(dict_alarms,'alarms',insert_alarms_id,alarm_data)
                        alarm_data = {'IsBeOk':1,'UpdateTime':'NULL'}
                        Update_Dict(dict_alarms,'alarms',int(alarm_id),'id',alarm_data,1)
                        data_dict_monweb = {'Alarms_Ids':None}
                        Update_Dict(dict_monweb,'monweb',monweb_id,'id',data_dict_monweb,0)
                    elif rstcode != 200 and alarm_id == '':
                        Msg = Url + ' Check URL Err ' + str(rstcode)
                        alarm_data = {'Ipid':monweb_id,'CreateTime':'NULL','UpdateTime':'NULL','Type':'monweb','Msg':Msg,'IsBeOk':0,'IsAlarm':1,'Gid':gid,'id':insert_alarms_id}
                        Add_Dict(dict_alarms,'alarms',insert_alarms_id,alarm_data)
                        data_dict_monweb = {'Alarms_Ids':str(insert_alarms_id)}
                        Update_Dict(dict_monweb,'monweb',monweb_id,'id',data_dict_monweb,0)
                    Send_Alarm(insert_alarms_id)
        else:
            Save_Log('check_url',url_host+' ping err')

    while True:
                #sql_monweb = "select id,MonUrl,RstCode,Gid,(select group_concat(id) from alarms where Ipid=monweb.id and IsBeOk=0) as Alarms_Ids from monweb  where Enable = 1"
                #dict_monweb = Sync_Db('id',sql_monweb)
        for k,v in dict_monweb.items():
            monweb_id = k
            url = v.get('MonUrl','')
            status_now = v.get('RstCode')
                        #if status_now is None:
                        #        status_now = 200
            mutex = threading.Lock() 
            check_do = threading.Thread(target=check_url,args=(monweb_id,url,status_now,mutex))
            check_do.setDaemon(True)
            check_do.start()
        time.sleep(MONWEB_INTERVAL)

def Save_Info_Day():
    sql = "select id,ip from ipinfo where ip !='0.0.0.0' and IsAlive='alive' and ClientStatus=1 and Enable=1"
    ip_list = Select(sql)
    sqls = []
    if ip_list[0] and len(ip_list[1])>0:
        ip_list = ip_list[1]
        for i in ip_list:
            ipid = i[0]
            ip = i[1]
            num = 0
            while num < 3:
                info_day_result = Do_Client(ip,'OLIVE_CLIENT_SHELL' + SEP_STR + 'runall' + SEP_STR + END_CMD_STR)
                if info_day_result[0]:
                    info_day = info_day_result[1]
                    if info_day.find("}'||") >= 0 and info_day.find("'check_load'=>'{") >= 0:
                        info_day = info_day.replace("'","''")
                        date_today = time.strftime('%Y-%m-%d',time.localtime(time.time()))
                        sql = "insert info_day set Ipid=" + str(ipid) + ",  Data_txt='" + info_day + "', Addtime = '" + date_today + "'"
                        sqls.append(sql)
                        num = 10
                    else:
                        Save_Log('Save_Info_Day',ip + ' info_data is imperfect')
                        num += 1
                        time.sleep(8)
                else:
                    time.sleep(8)
                    Save_Log('Save_Info_Day',ip + ' get day_info err ' + str(num) + ' times')
                    num += 1
    Do_Sqls(sqls)    


#接收到客户端监控数据的流程控制函数
def Do_Mon(data_isok,data,client_ip):    
    global MaxIpid
    global dict_ipinfo
    conn = Db_connect()
    if conn:
        conn.close()
        ipid = Get_Ipid(client_ip)
        if not ipid:
            ipid = MaxIpid + 1
            Insert_Ip(client_ip,ipid)
            if glo_lock.acquire(1): 
                MaxIpid = MaxIpid + 1
                glo_lock.release()
            Check_Service(client_ip,ipid)
            time.sleep(0.5)
            sql = "update ports set IsMon=1 where ipid=" + str(ipid) + " and Port in " + str(MON_PORTS)
            Do_Sqls(sql)
        else:
            pass

        if data_isok == 'ISOK' and data != 'null':
            montext = data
            Check_Mon(client_ip,montext)
        else:
            montext = 'NoData'

        if ipid and montext != 'NoData':
            Insert_Monitor(ipid,client_ip,montext)
        else:
            Save_Log('Insert_Monitor',client_ip + ' The monitor date is err!')
    else:
        pass

#检测设备基本信息的函数
def Check_Devinfo(ip,ipid):
    if Check_Port(ip,CLIENT_PORT):
        sock = socket.socket()
        try:
            sock.connect((ip, CLIENT_PORT))
            sock.send(encrypt('OLIVE_CLIENT_SHELL' + SEP_STR + 'check_devinfo' + SEP_STR + END_CMD_STR))
            devinfo = decrypt(Recv_Data(sock))
            devinfo = eval(devinfo.replace('"devinfo":',''))
            cpu_pro = float(devinfo['Cpu_Pro'])
            dict_data = {'Cpu_Pro':cpu_pro}
            Update_Dict(dict_ipinfo,'ipinfo',ip,'Ip',dict_data,1)
            sql_str = ''
            n = 0
            for i in devinfo:
                n += 1
                if n < len(devinfo):
                    sql_str += i+"='"+devinfo[i]+"',"
                else:
                    sql_str += i+"='"+devinfo[i]+"'"
            get_sn_sql = "select SN from devinfo where Ipid="+str(ipid)
            rs = Select(get_sn_sql)
            if rs[0] and len(rs[1])>0:
                rs = rs[1]
                sql = "update devinfo set "+sql_str+" where Ipid=" + str(ipid)
            else:
                sql = "insert devinfo set "+sql_str+",Ipid=" + str(ipid)
            sqls = [sql]
            Do_Sqls(sqls)
        except Exception  as e:
            Save_Log('Check_Devinfo',ip + ' ' + str(e))
    else:
        Save_Log('Check_Devinfo',ip + ' Client_Port is err')

#检测客户开启服务的函数
def Check_Service(ip,ipid):
    if Check_Port(ip,CLIENT_PORT):
        sock = socket.socket()
        try:
            sock.connect((ip, CLIENT_PORT))
            sock.send(encrypt('OLIVE_CLIENT_SHELL' + SEP_STR + 'check_service' + SEP_STR + END_CMD_STR))
            services = decrypt(Recv_Data(sock))
            sqls = []
            if services.find('||') >= 0:
                del_sql = "delete from ports where IsMon=0 and  Ipid=" + str(ipid)
                Do_Sqls(del_sql)
                services = eval(services.replace("'check_service'=>'",'').replace("'||",""))
                for service in services:
                    port = str(service.split('_')[0])
                    service_name = service.split('_')[1]
                    get_ports_id_sql = "select id from ports where Port="+port+" and  Ipid=" + str(ipid)
                    rs = Select(get_ports_id_sql)
                    if rs[0] and len(rs[1])>0:
                        rs = rs[1]
                        sql = "update ports set Port="+port+",Service='" + service_name  + "'where id =" + str(rs[0][0])
                    else:
                        sql = "insert ports set Port="+port+",Service='" + service_name + "',Ipid =" + str(ipid)
                    sqls.append(sql)
                Do_Sqls(sqls)
            else:
                pass
        except Exception  as e:
            Save_Log('Check_Service',ip + ' ' + str(e))
    else:
        Save_Log('Check_Service',ip + ' Client_Port is err')


#间隔检测所有客户端开启服务的函数
def Check_Service_All():
    sql = "select ip,id from ipinfo where ip != '0.0.0.0' and IsAlive='alive' and ClientStatus=1 and Enable=1"
    ips_list = Select(sql)
    if ips_list[0] and len(ips_list[1])>0:
        ips_list = ips_list[1]
        for ip_list in ips_list:
            ip = ip_list[0]
            ipid = ip_list[1]
            Check_Service(ip,ipid)
    elif ips_list[0] and len(ips_list[1])==0:
        pass
    else:
        Save_Log('Check_Service_All','can not get ip_list for db!')

def Mon_Ports():
    time.sleep(5)
    while True:
        for k,v in dict_ports.items():
            pid = k
            port = v.get('Port')
            ip = v.get('Ip')
            status = v.get('Status')
            if Check_Port(ip,port):
                status_now = 1
                alarms_ids = dict_ipinfo.get(ip,{}).get('Alarms_Ids','')
                if  dict_ipinfo.get(ip,{}).get('Alarms_Ids','') and 'port' in Get_Alarms_rs(ip):
                    alarms_ids_rs = alarms_ids.split(',')
                    for i in alarms_ids_rs:
                        Msg = dict_alarms.get(int(i),{}).get('Msg','')
                        if Msg.find(' ' + str(port) + ' ') > -1:
                            alarm_id = i
                            gid = dict_ipinfo.get(ip,{}).get('GroupId',1)
                            Msg = ip + ' Port ' + str(port) + ' OK'
                            Update_Mon_Sql(ip,gid,'port',Msg,alarm_id)
                else:
                    pass
            else:
                status_now = 0
                alarms_ids = dict_ipinfo.get(ip,{}).get('Alarms_Ids','')
                if not alarms_ids or 'port' not in Get_Alarms_rs(ip):
                    gid = dict_ipinfo.get(ip,{}).get('GroupId',1)
                    Msg = ip + ' Port ' + str(port) + ' Err'
                    Update_Mon_Sql(ip,gid,'port',Msg,'')
                else:
                    alarms_ids_rs = alarms_ids.split(',')
                    alarm_id = ''
                    for i in alarms_ids_rs:
                        Msg = dict_alarms.get(int(i),{}).get('Msg','')
                        if Msg.find(' ' + str(port) + ' ') > -1:
                            alarm_id = i
                    if alarm_id == '':
                        gid = dict_ipinfo.get(ip,{}).get('GroupId',1)
                        Msg = ip + ' Port ' + str(port) + ' Err'
                        Update_Mon_Sql(ip,gid,'port',Msg,alarm_id)

            if status != status_now:
                data_dict_ports = {'Status':status_now}
                Update_Dict(dict_ports,'ports',pid,'id',data_dict_ports,1)    
        time.sleep(180)

def Update_Client_Key_All():
    sql = "select ip,id from ipinfo where ip != '0.0.0.0' and IsAlive='alive' and Enable=1 and ClientStatus=1"
    ip_list = Select(sql)
    if ip_list[0]:
        if len(ip_list[1])>0:
            ip_list = ip_list[1]
            for ips in ip_list:
                ip = ips[0]
                Update_Client_Key(ip,CLIENT_PORT)

#间隔检测所有客户端基本信息的函数
def Check_Devinfo_All():
    time.sleep(30)
    sql = "select ip,id from ipinfo where ip != '0.0.0.0' and IsAlive='alive' and Enable=1 and ClientStatus=1"
    ip_list = Select(sql)
    if ip_list[0]:
        if len(ip_list[1])>0:
            ip_list = ip_list[1]
            for ips in ip_list:
                ip = ips[0]
                ipid = ips[1]
                Check_Devinfo(ip,ipid)
        else:
             pass
             #Save_Log('Check_Devinfo_All','can not get ip_list for db!')
    else:
         Save_Log('Check_Devinfo_All','can not get ip_list for db!')
def Recv_Data(socket):
    data = ''
    while True:
        buf = socket.recv(65536).decode()
        if not len(buf) or buf.endswith(ENCODE_END_STR):
            data += buf    
            break 
        data += buf
    return data
    socket.close()

def Do_Client(ip,cmd):
    if cmd.split()[0] in NORUN_CMD:
        result = [0,"Command %s not allowed to run \n" % cmd.split()[0]]
        return result

    if Check_Port(ip,CLIENT_PORT):
        if cmd.find('OLIVE') < 0:
            olive_cmd = 'OLIVE_SERVER_CMD' + SEP_STR + cmd + SEP_STR + END_CMD_STR
        elif cmd.find('CLIENT_SHEEL#_#_#') >= 0:
            olive_cmd = 'OLIVE_CLIENT_SHELL'  + SEP_STR + cmd.replace('OLIVE_CLIENT_SHEEL#_#_#','') + SEP_STR + END_CMD_STR
        else:
            olive_cmd = cmd
        sock = socket.socket()
        try:
            sock.connect((ip, CLIENT_PORT))
            sock.send(encrypt(olive_cmd))
            data = decrypt(Recv_Data(sock))
            sock.close()
            result = [1,data]
        except Exception  as e:
            Save_Log('Do_Client_conn',ip + ' ' + cmd + ' ' + str(e))
            result = [0,ip + ' ' + cmd + ' ' + str(e)]
    else:
        result = [0,"Client_Port is err\n"]
        Save_Log('Do_Client',ip + ' ' + cmd + ' Client_Port is err')
    return result

def Update_Client(ip):
    client_file = CLIENT_FILE
    send_file_result = Send_File(ip,client_file,client_file,'CLIENT_FILE',1)
    if send_file_result[0] == 1:
        time.sleep(0.3)
        Do_Client(ip,'OLIVE_SELF_CMD' + SEP_STR + 'RESTART_CLIENT' + SEP_STR +END_CMD_STR)
        result = [1,'update client is done!']
    else:
        result = [0,send_file_result[1]]
    return result

def Do_Oct_Thread(ips,cmd,connect):
    count = 0
    global client_result_msg
    client_result_msg = []
    ip_num = len(ips)
    mutex = threading.Lock() 
    for ip in ips:
        Do_Oct_Cmd_Thread = threading.Thread(target = Do_Oct_Cmd,args=(ip,cmd,mutex))
        Do_Oct_Cmd_Thread.setDaemon(True)
        Do_Oct_Cmd_Thread.start()
    while True:
        if len(client_result_msg) == ip_num or count > OCT_TIMEOUT:
            break 
        else:
            pass
        time.sleep(0.1)
        count = count + 0.1
    all_result = '==================================== result ========================================='
    for i in client_result_msg:
        ip = i[0]
        if ip_num == 1:
            ipinfo_line = ''
        else:
            ipinfo_line = "------------------------------" + ip + "-------------------------------\n"
        all_result = all_result + "\n" + ipinfo_line + i[1]
    connect.send((all_result +"\n"+ ENCODE_END_STR).encode())


def Do_Oct_Cmd(ip,cmd,mutex):
    if cmd == 'UPDATE_CLIENT':
        with mutex:
            update_client_result = Update_Client(ip)
            result_msg = ip + ' ' + update_client_result[1]
    elif cmd == 'UPDATE_CLIENT_FILE':
        with mutex:
            send_file_result = Send_File(ip,CLIENT_FILE,CLIENT_FILE,'CLIENT_FILE',1)
            if send_file_result[0] == 1:
                result_msg = ip+' client file will update'
            else:
                result_msg = ip + ' ' + send_file_result[1]    
    elif cmd == 'RESTART_CLIENT':
        restart_client_result = Do_Client(ip,'OLIVE_SELF_CMD' + SEP_STR + 'RESTART_CLIENT' + SEP_STR + END_CMD_STR)
        result_msg = ip + restart_client_result[1]
    elif cmd == 'UPDATE_SHFILE':
        with mutex:
            update_shfile_result = Do_Client(ip,'OLIVE_SELF_CMD' + SEP_STR +'UPDATE_SHFILE' + SEP_STR + END_CMD_STR)
            result_msg = ip + update_shfile_result[1]
    elif cmd == 'CHECK_DEVINFO':
        ipid = Get_Ipid(ip)
        Check_Devinfo(ip,ipid)
        result_msg = ip + ' have check devinfo'
    elif cmd == 'CHECK_SERVICE':
        ipid = Get_Ipid(ip)
        Check_Service(ip,ipid)
        result_msg = ip + ' have check service'
    elif cmd.startswith('CPFILE'):
#        with mutex:
        local_file = cmd.split()[1]
        remote_file = cmd.split()[2]
        if not os.path.isfile(local_file):
            Save_Log('CPFILE','local file ' + local_file + ' does not exist!')
            result_msg = ip + '|local file ' + local_file + ' does not exist!'
        else:
            send_result = Send_File(ip,local_file,remote_file,'USER_FILE',0) 
            if send_result[0]:
                result_msg = 'cp '+ local_file + ' to ' + ip + ':' + remote_file + ' successful please check!'
            else:
                result_msg = 'cp '+ local_file + ' to ' + ip + ':' + remote_file + ' failed! Errors:' + send_result[1]
    elif cmd.endswith('&'):
        markid=random.randint(1, 200)*random.randint(1, 200)*random.randint(1, 200)+random.randint(1, 200)
        insert_sql = "insert into bg_result set Ip='" + ip + "',OutStr='The cmd is running,please wait!',CmdStr='" + cmd[:-1] +  "',MarkId=" + str(markid)
        Do_Sqls(insert_sql)
        cmd = str(markid) + SEP_STR2 + cmd
        result = Do_Client(ip,cmd)
        result_msg = result[1]
    elif cmd.startswith('DEFINE_CMD'):
        sh_file = cmd.split(SEP_STR2)[1]
        d_file = cmd.split(SEP_STR2)[2]
        cmd_str = cmd.split(SEP_STR2)[3]
        send_result = Send_File(ip,sh_file,d_file,'USER_FILE',1)
        if send_result[0]:
            markid=random.randint(1, 200)*random.randint(1, 200)*random.randint(1, 200)+random.randint(1, 200)
            insert_sql = "insert into bg_result set Ip='" + ip + "',CmdStr='" + cmd_str + "',OutStr='The cmd is running,please wait!',ShellFile='" + sh_file + "',MarkId=" + str(markid)
            Do_Sqls(insert_sql)
            result = Do_Client(ip,str(markid) + SEP_STR2 + cmd_str + '&')
            result_msg = result[1]
        else:
            result_msg = 'cp '+ sh_file + ' to ' + ip + ':' + d_file + ' failed! Errors:' + send_result[1]
    else:
        result = Do_Client(ip,cmd)
        result_msg = result[1]
    global client_result_msg
    client_result_msg.append([ip,result_msg])

def Do_Bg_Result(markid,out,err):
    update_sql = "update bg_result set Stat=1, OutStr='" + out + "',ErrStr='" + err + "',EndTime=now() where MarkId=" + str(markid)
    Do_Sqls(update_sql)

#主函数之一，监听客户端请求的流程控制函数
def Listen_Client():
    server_ips = get_all_ips()
    s = socket.socket()
    s.setsockopt(socket.SOL_SOCKET,socket.SO_REUSEADDR,1) 
    s.bind((BIND_IP,BIND_PORT))
    s.listen(1024)
    while True:
        try:
            connect,addr = s.accept()
            client_ip = addr[0]
            rev_data = connect.recv(65536)
            #print(rev_data)
            rev_data = rev_data.decode()
            #print(type(rev_data))
            if rev_data.endswith(ENCODE_END_STR):
                rev_data = decrypt(rev_data)
                if len(rev_data.split(SEP_STR))>=3:
                    data_type = rev_data.split(SEP_STR)[0]
                    if data_type == 'MON':
                        data_isok = rev_data.split(SEP_STR)[1]
                        data = rev_data.split(SEP_STR)[2]
                        Do_Mon(data_isok,data,client_ip)
                    elif data_type == 'REQ_SHFILE':
                        if not os.path.isdir(ZIPFILE_DIR):
                            os.mkdir(ZIPFILE_DIR)
                        zipname = ZIPFILE_DIR + time.strftime('%Y%m%d%H%M')+'.zip'
                        if not os.path.isfile(zipname):
                            Zip_Dir(SH_DIR,zipname)
                        Send_File(client_ip,zipname,zipname,'SHFILE',1)
                    elif data_type == 'BG_RUN_RESULT':
                        markid = rev_data.split(SEP_STR)[1]
                        out = rev_data.split(SEP_STR)[2].replace("\n","R!I@L#L")
                        err = rev_data.split(SEP_STR)[3].replace("\n","R!I@L#L")
                        Do_Bg_Result(markid,out,err)
                    else:
                        Save_Log('Listen_Client','Unkown type')
                else:
                    Save_Log('Listen_Client','Err type')

            elif client_ip in server_ips and rev_data.find(OCT_CMD_PRE + SEP_STR) >= 0 and len(rev_data.split(SEP_STR)) >=3:
                ips = rev_data.split(SEP_STR)[1]
                if ips != 'OLIVE_SERVER':
                    ips = ips.split()
                    cmd = rev_data.split(SEP_STR)[2].strip()
                    Do_Oct_Thread(ips,cmd,connect)
                else:
                    cmd = rev_data.split(SEP_STR)[2].strip()
                    if cmd == "Sync_Db":
                        time.sleep(2)
                        Sync_Db_Ipinfo()
                    elif cmd == "Sync_Db_Alarms":
                        time.sleep(2)
                        Sync_Db_Alarms()
                    elif cmd == "Sync_Db_Sys_Config":
                        time.sleep(2)
                        Sync_Db_Sys_Config()
                    elif cmd == "Sync_Db_IpAlarms":
                        time.sleep(2)
                        Sync_Db_Ipinfo()
                        Sync_Db_Alarms()
                    elif cmd == 'Sync_Db_Monweb':
                        time.sleep(2)
                        Sync_Db_Monweb()
                    else:
                        Save_Log('OLIVE_SERVER_DO',' try to do ' + cmd )

            elif rev_data == 'I love rill':
                connect.send(("Me tooooooooooooooooooooooooooo").encode())
                client_public_key, client_public_key_sha256 = pickle_loads(connect.recv(1024))
                client_public_key_sha256 = bytes2str(client_public_key_sha256)
                if hashlib.sha256(client_public_key).hexdigest() != client_public_key_sha256:
                    raise Exception("key is err")
                else:
                    client_public_key = pickle_loads(client_public_key)
                    en_server_key = rsa.encrypt(pickle.dumps(server_key,protocol=2), client_public_key)
                    en_server_key_sha256 = hashlib.sha256(en_server_key).hexdigest()
                    connect.send(pickle.dumps((en_server_key,en_server_key_sha256),protocol=2))
            elif rev_data == "":
                pass
            else:
                Save_Log('Listen_Client',client_ip + ' try to do something ' + rev_data)
            connect.close()
        except Exception as e:
            #print(e)
            Save_Log('Listen_Client',str(e))
#基本的守护进程类
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



def Init():
    '''
    global dict_ipinfo 
    global dict_alarms 
    global dict_ports
    global dict_monweb
    '''
    global server_key
    server_key = Fernet.generate_key()
    global fernet_obj
    fernet_obj = Fernet(server_key)
    global glo_lock
    glo_lock = threading.Lock()

    Sync_Db_Ipinfo()
    Sync_Db_Alarms()
    Sync_Db_Sys_Config()
    Sync_Db_Monweb()
    Sync_Db_Ports()

    

def Sync_Db(key,sql):
    dict_local = {}
    rs = Select(sql,'dict')
    if rs[0]:
        for i in rs[1]:
            k = i.get(key)
            dict_local[k] = i
    return dict_local

def Sync_Db_Ipinfo():
    sql_ipinfo = "select id,Ip,GroupId,IsAlive,ClientStatus,LoadLevel,DiskLevel,NetworkLevel,ProcessLevel,LoginLevel,ConnectLevel,Enable,1395337260 as LastCheckTime,Cpu_Pro,(select group_concat(id) from alarms where alarms.ipid=ipinfo.id and IsBeOk=0) as Alarms_Ids,(select group_concat(ports.id) from ports where ipinfo.id=ports.ipid and ports.IsMon=1) as Ports_ids from ipinfo"
    global dict_ipinfo
    dict_ipinfo = Sync_Db('Ip',sql_ipinfo)

def Sync_Db_Alarms():
    sql_alarms = "select * from alarms where IsBeOk=0 and Type!='monweb'"
    global dict_alarms
    dict_alarms = Sync_Db('id',sql_alarms)

def Sync_Db_Ports():
    sql_ports = "select ports.id,Ip,Port,ports.Status from ports left join ipinfo on ports.ipid=ipinfo.id where IsMon=1 and ipinfo.ip!='0.0.0.0'"
    global dict_ports
    dict_ports = Sync_Db('id',sql_ports)

def Sync_Db_Sys_Config():
    sql_sys_config = "select KeyName,KeyValue from sys_config where KeyName in('def_mon_ports','monweb_interval','norun_cmd')";
    dict_sys_config = Sync_Db('KeyName',sql_sys_config);
    global MON_PORTS,NORUN_CMD,MONWEB_INTERVAL
    MON_PORTS = '(' + dict_sys_config.get('def_mon_ports',{'KeyValue':DEF_MON_PORTS}).get('KeyValue') + ')'
    NORUN_CMD = dict_sys_config.get('norun_cmd',{'KeyValue':DEF_MON_PORTS}).get('KeyValue').split(',')
    MONWEB_INTERVAL = int(dict_sys_config.get('monweb_interval',{'KeyValue':str(DEF_MONWEB_INTERVAL)}).get('KeyValue'))

def Sync_Db_Monweb():
    sql_monweb = "select id,MonUrl,RstCode,Gid,(select group_concat(id) from alarms where Ipid=monweb.id and IsBeOk=0) as Alarms_Ids from monweb  where Enable = 1"
    global dict_monweb
    dict_monweb = Sync_Db('id',sql_monweb)


def Update_Dict(dict_name,db_table,key,key_name,new_value,sync_db):
    global dict_local
    dict_local = dict_name
    value = dict_local.get(key,None)
    if value:
        update_sql = 'update ' + db_table + ' set '
        for k,v in new_value.items():
            value[k] = v
            if isinstance(v,str) and v!='NULL':
                update_sql = update_sql + k + "='" + v + "',"
            else:
                update_sql = update_sql + k + '=' + str(v) + ','
        dict_local[key] = value
        if sync_db:
            update_sql = update_sql[:-1] + ' where ' + key_name + "='" + str(key) + "'"
            Do_Sqls(update_sql)


def Add_Dict(dict_name,db_table,key,data):
    global dict_local
    dict_local = dict_name
    dict_local.setdefault(key,data)
    insert_sql = 'insert into ' + db_table + ' set '
    for k,v in data.items():
        if v == None:
            v = 'NULL'
        if isinstance(v,str) and v!='NULL' and v!='now()':
            insert_sql = insert_sql + k + "='" + v + "',"
        else:
            insert_sql = insert_sql + k + "=" + str(v) + ","
    Do_Sqls(insert_sql[:-1])





#自定义守护进程类，重新run函数
class My_daemon(Daemon):
    def run(self):
        conn = Db_connect()
        if conn:
            Init()
            #Check_Devinfo_All()
            Ipid_rs = Select('select id from ipinfo order by id desc limit 1')
            Alarm_rs = Select('select id from alarms order by id desc limit 1')
            global MaxIpid
            global MaxAlarmId
            if glo_lock.acquire(1):
                if Ipid_rs[0] and len(Ipid_rs[1]) > 0:
                    MaxIpid = Ipid_rs[1][0][0]
                else:
                    MaxIpid = 0
                if Alarm_rs[0] and len(Alarm_rs[1]) > 0:
                    MaxAlarmId = Alarm_rs[1][0][0]
                else:
                    MaxAlarmId = 0
                glo_lock.release()
            update_client_key_thread = threading.Thread(target = Update_Client_Key_All)
            update_client_key_thread.setDaemon(True)
            update_client_key_thread.start()
            check_devinfo_thread = threading.Thread(target = Check_Devinfo_All)
            check_devinfo_thread.setDaemon(True)
            check_devinfo_thread.start()
            cron_do_thread = threading.Thread(target = Cron_Do)
            cron_do_thread.setDaemon(True)
            cron_do_thread.start()
            monweb_thread = threading.Thread(target = Monweb)
            monweb_thread.setDaemon(True)
            monweb_thread.start()
            mon_ports_thread = threading.Thread(target = Mon_Ports)
            mon_ports_thread.setDaemon(True)
            mon_ports_thread.start()
            check_alive_thread = threading.Thread(target = Check_Alive)
            check_alive_thread.setDaemon(True)
            check_alive_thread.start()
            Listen_Client()
            print("ssssssssssssssssssssssssssssssssssssssss")
        else:
            print( "Try connect mysql false, please check it!")


#实例化守护进程类，并定义参数选型
'''============================================================================================'''
if __name__ == '__main__':
    daemon = My_daemon(pidfile=PIDFILE,homedir=HOME_DIR,process_name=sys.argv[0])
    if len(sys.argv) == 2:
        if 'START' == (sys.argv[1]).upper():
            daemon.start()
        elif 'STOP' == (sys.argv[1]).upper():
            daemon.stop()
        elif 'RESTART' == (sys.argv[1]).upper():
            daemon.restart()
        else:
            print( "Unknow Command!")
            print( "Usage: %s start|stop|restart" % sys.argv[0])
            sys.exit(2)
        sys.exit(0)
    else:
        print( "Usage: %s start|stop|restart" % sys.argv[0])
        sys.exit(0)
