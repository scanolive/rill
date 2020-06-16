function fileChange(target,maxsize)
{     
	var isIE = /msie/i.test(navigator.userAgent) && !window.opera;           
	var fileSize = 0;          
	if (isIE && !target.files)
	{      
		var filePath = target.value;      
	    var fileSystem = new ActiveXObject("Scripting.FileSystemObject");         
		var file = fileSystem.GetFile (filePath);      
		fileSize = file.Size;     
    } 
	else 
	{     
         fileSize = target.files[0].size;      
	}    
	var size = fileSize;     
	if(size>maxsize*1024*1024)
	{   
		alert("附件不能大于"+maxsize+"M");  
	}
}  

function showHint_get_users(usertype,userid)
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    }
  }
URL = "get_users.php?userid="+userid+"&usertype="+usertype+"&t="+Math.random();
xmlhttp.open("GET",URL,true);
xmlhttp.send();
}

function showHint_socket(cmd,ip,verify_str)
{
var cmd = cmd.replace("&","!a@N#d$");
var postStr = "cmd=" + cmd + "&ip=" + ip + "&verify_str=" + verify_str;
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    }
  }
URL = "socket.php";
xmlhttp.open("POST",URL,true);
xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
xmlhttp.send(postStr);
}

  
function showHint_get_ip(gid,ip)
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("IpDiv").innerHTML=xmlhttp.responseText;
    }
  }
URL = "get_ip.php?gid="+gid+"&ip="+ip+"&t="+Math.random();
xmlhttp.open("GET",URL,true);
xmlhttp.send();
}

function showHint_get_allip(gid,ip)
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    }
  }
URL = "get_all_ip.php?gid="+gid+"&ip="+ip+"&t="+Math.random();
xmlhttp.open("GET",URL,true);
xmlhttp.send();
}

function ajax_do(URL)
{
var URL = URL + "&t="+Math.random();
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }

xmlhttp.open("GET",URL);
xmlhttp.send();
}


function showHint_get_filelist()
{
//var postStr = "selected_ips=" + select_ips;
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("filelist").innerHTML=xmlhttp.responseText;
    }
  }
URL = "filelist.php?t="+Math.random();
xmlhttp.open("GET",URL,true);
xmlhttp.send();  
}

function showHint_get()
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("testdiv").innerHTML=xmlhttp.responseText;
    }
  }
URL = "test.php?t="+Math.random();
xmlhttp.open("GET",URL,true);
xmlhttp.send();  
}


function showHint_upfile()
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("upload").innerHTML=xmlhttp.responseText;
    }
  }
URL = "upload.php?t="+Math.random();
xmlhttp.open("GET",URL,true);
xmlhttp.send();
}

function delip(id){
var ip=id;
document.getElementById(id).innerHTML="";
var ips=document.getElementById("selected_ips").value;
ips=ips.replace(ip,"");
document.getElementById("selected_ips").value=ips; 
}
function en_str(str){
str = str.replace('"','r#o#s_syh');		
str = str.replace("'",'r#o#s_dyh');		
str = str.replace(";",'r#o#s_fh');		
str = str.replace("\\",'r#o#s_fxg');		
//str = str.replace("|",'r#o#s_gdf');		
return str
}


//创建一个showhidediv的方法，直接跟ID属性
function showhidediv(id){
var sbtitle=document.getElementById(id);
if(sbtitle){
   if(sbtitle.style.display=='block'){
   sbtitle.style.display='none';
   }else{
   sbtitle.style.display='block';
   }
}
}

 
              var checkall=document.getElementsByName("delid[]");  
                function select(){                          //全选  
                    for(var $i=0;$i<checkall.length;$i++){  
                        checkall[$i].checked=true;  
                    }  
                }  
                function fanselect(){                        //反选  
                    for(var $i=0;$i<checkall.length;$i++){  
                        if(checkall[$i].checked){  
                            checkall[$i].checked=false;  
                        }else{  
                            checkall[$i].checked=true;  
                        }  
                    }  
                }           
                function noselect(){                          //全不选  
                    for(var $i=0;$i<checkall.length;$i++){  
                        checkall[$i].checked=false;  
                    }  
                }  


function p_del() {   
var msg = "您真的确定操作吗？\n请确认！";   
if (confirm(msg)==true){   
return true;   
}else{   
return false;   
}   
}

//var clear_flag = true;
function Cleartext(tname)
{
		
//	if(clear_flag)
//	{
		var t=document.getElementById(tname).value="";
//		clear_flag = false;
//	}
}

function goTopEx(){
        var obj=document.getElementById("goTopBtn");
        function getScrollTop(){
                return document.documentElement.scrollTop || document.body.scrollTop;
            }
        function setScrollTop(value){
                    document.documentElement.scrollTop = value;
   					 document.body.scrollTop = value;
            }    
        window.onscroll=function(){getScrollTop()>0?obj.style.display="":obj.style.display="none";}
        obj.onclick=function(){
            var goTop=setInterval(scrollMove,10);
            function scrollMove(){
                    setScrollTop(getScrollTop()/1.1);
                    if(getScrollTop()<1)clearInterval(goTop);
                }
        }
    }

function checkMail(str){ 
var strReg=""; 
var r; 
var strText=document.all(str).value; 
//strReg=/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i;
//strReg=new RegExp("^(([0-9a-zA-Z]+)|([0-9a-zA-Z]+[_.0-9a-zA-Z-]*[0-9a-zA-Z-]+))@([a-zA-Z0-9-]+[.])+([a-zA-Z]|net|NET|asia|ASIA|com|COM|gov|GOV|mil|MIL|org|ORG|edu|EDU|int|INT|cn|CN|cc|CC)$");
strReg=/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9_-]+((\.|-)[A-Za-z0-9_-]+)*\.[A-Za-z0-9_-]+$/;
r=strText.search(strReg); 
if(r==-1) { 
alert("邮箱格式错误!"); 
document.all(str).focus();
return false;
} 
}

function checkMobile(str){ 
var strReg=""; 
var r; 
var strText=document.all(str).value; 
//strReg=/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i;
strReg=/1[3-8]+\d{9}/;
r=strText.search(strReg); 
if (!str || str==null){
	alert("手机号不能为空!"); 
	document.all(str).focus();
	return false;
}
else if(strText.length!=11) { 
alert("请输入有效的手机号码！"); 
document.all(str).focus();
return false;
}
else if(r==-1) { 
alert("请输入有效的手机号码！"); 
document.all(str).focus();
return false;
} 
}
