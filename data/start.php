<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<link rel="shortcut icon" href="http://lyclub.f3322.net:82/web_images/32X32.ico" />
<title>初始化-数据</title>
<link rel="stylesheet" type="text/css" href="/data/conf/data_style.css" />
</head>
<body>
<?php
//date_default_timezone_set("UTC");
date_default_timezone_set("Asia/ShangHai");		//定义时区为中国上海时间(+8时区)
include("../sqlite3.php");
ini_set('display_errors',0);            //错误信息1
ini_set('display_startup_errors',0);    //php启动错误信息1
error_reporting(0);                    //打印出所有的 错误信息-1
//$GLOBALS["db1"]=new DataBase("data/main.db");		//全部db1变量为主要数据库文件链接

//初始化数据语句(main主要比赛信息)
/*++++bsinfo+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * bs_gid  + bs_gtitle +  bs_pass   +  bs_gstart   + bs_gend      + bs_ginfo +  bs_cuser/bs_ctime + bs_bakinfo  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *比赛ID号 + 比赛标题  + 评委验证码 + 比赛开始时间 + 比赛结束时间 + 说明信息 + 创建用户的时间    + 比赛备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
$ops[0]="drop table bsinfo";
$ops[1]="create table bsinfo(bs_gid bigint unique,bs_gtitle varchar(500) not null,bs_pass varchar(100) not null,bs_gstart datetime not null,bs_gend datetime not null,bs_ginfo varchar(1000) not null,bs_cuser varchar(100),bs_ctime datetime,bs_bakinfo varchar(1000))";
$ops[2]="insert into bsinfo values(201903001,'测试比赛评分一','".sha1("1234")."','2019-03-31 00:00:00','2019-04-01 23:59:59','通过初始化数据创建的测试比赛评分测试2019-03-31','admin','2019-03-30 10:01:02','')";
/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *  au_id   +  au_acc    +  au_pass     +  au_alias    +  au_tel    +  au_qq   +  au_mail     +  au_birth    +  au_bakinfo   +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * 用户ID号 + 用户账号名 + 用户账号密码 + 用户显示别名 + 用户手机号 + 用户QQ号 + 用户电子邮箱 + 用户生日日期 + 用户备注信息  +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
$ops[3]="drop table admin_users";
$ops[4]="create table admin_users(au_id bigint unique,au_acc varchar(100) not null,au_pass varchar(100) not null,au_alias varchar(100) not null,au_tel varchar(12),au_qq varchar(12),au_mail varchar(100),au_birth date,au_bakinfo varchar(1000))";
$ops[5]="insert into admin_users values(10001,'admin','".sha1("123456")."','lyclub2016','18562221224','351188949','liubingjie771@live.cn','1989-10-21','')";
//循环执行初始化语句(main主要比赛信息)
for($i=0;$i<count($ops);$i++)
{
	echo "<p>";
	if($GLOBALS["db1"]->exec($ops[$i]))
		echo "<span class='span_suc'>执行成功</span>";
	else
		echo "<span class='span_fail'>执行失败</span>";
	echo $ops[$i];
	echo "</p>";	
}

//初始化数据语句(201903001比赛信息)
$op1[0]=$ops[0];
$op1[1]=$ops[1];
$op1[2]=$ops[2];
/***************pwinfo评委人员信息表***************/
/*+++++pwinfo++++++++++++++++++++++++++++++++++++++++++++
  +  pw_id    +  pw_name  +  pw_job    +  pw_bakinfo    +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++
  + 评委ID号  + 评委姓名  +  评委职位  +  评委备注信息  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
$op1[3]="drop table pwinfo";
$op1[4]="create table pwinfo(pw_id int unique,pw_name varchar(100) not null,pw_job varchar(100),pw_bakinfo varchar(1000))";
$op1[5]="insert into pwinfo values(1,'admin','super','')";
$op1[6]="insert into pwinfo values(2,'lyclub','super','')";
$op1[7]="insert into pwinfo values(3,'lyclub2016','super','')";
$op1[8]="insert into pwinfo values(4,'super','super','')";
/***************xminfo项目信息表***************/
/*++++++xminfo++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  +  xm_id   +  xm_name   +  xm_title    +  xm_info   +  xm_bakinfo  +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  + 项目ID号 + 项目单位名 + 单位的项目名 + 项目的信息 + 项目备注信息 +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
$op1[9]="drop table xminfo";
$op1[10]="create table xminfo(xm_id bigint unique,xm_name varchar(100) not null,xm_title varchar(100) not null,xm_info varchar(1000),xm_bakinfo varchar(1000))";
$op1[11]="insert into xminfo values(20180101,'测试单位固定','固定分数的测试单位……','测试20180101说明信息','')";
$op1[12]="insert into xminfo values(20190301,'软件系2019网络1班','歌唱1……','ABC深刻搭街坊昆仑山地方','')";
$op1[13]="insert into xminfo values(20190302,'软件系2019网络2班','歌唱2……','12398看来就是对方','')";
/***************xmscore项目分数表***************/
/*++++++xmscore++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *  xm_id   +      pwsc1    +      pwsc2    +      pwsc3    +      pwsc4    + sc_str   + sc_max   + sc_min   +  sc_avg  + sc_start + sc_end   + sc_bakinfo  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * 项目ID号 +评委人员表ID=1 +评委人员表ID=2 +评委人员表ID=3 +评委人员表ID=4 +分数字符串+去掉最高分+去掉最低分+最终平均分+打分开始时+打分结束时+打分备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
$op1[14]="drop table xmscore";
$op1[15]="create table xmscore(xm_id bigint unique,pwsc1 int,pwsc2 int,pwsc3 int,pwsc4 int,sc_str varchar(5000),sc_max varchar(100),sc_min varchar(100),sc_avg double,sc_start datetime,sc_end datetime,sc_bakinfo varchar(1000))";
$op1[16]="insert into xmscore values(20180101,40,23,44,42,'40,42','44','23',41,'2019-03-31 11:23:22','2019-03-31 12:12:22','')";
$op1[17]="insert into xmscore values(20190301,89,72,54,90,'72,89','90','52',80.5,'2019-03-31 22:40:20','2019-03-31 22:45:00','')";
$op1[18]="insert into xmscore values(20190302,67,80,90,98,'80,90','98','67',85,'2019-03-31 22:46:20','2019-03-31 22:50:00','')";

//循环执行初始化语句(201903001比赛信息)
$db2=new DataBase("data/FenDb/201903001.db");
for($i=0;$i<count($op1);$i++)
{
	echo "<p>";
	if($db2->exec($op1[$i]))
		echo "<span class='span_suc'>执行成功</span>";
	else
		echo "<span class='span_fail'>执行失败</span>";
	echo $op1[$i];
	echo "</p>";	
}
?>
</body>
</html>
