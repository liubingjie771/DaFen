<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title>比赛打分系统-管理系统</title>
<style type="text/css">
body { margin:20px;padding:20px;cursor:default; }
lyclub 
{
	text-align:center;
	font-size:30px;
	line-height:40px;
	background:lightgray;
	font-family: 黑体;
	font-weight:bold;
}
</style>
</head>
<body>
<center>
<p><button onclick="javascript:location.href='adds.php?main_create1';">创建新的比赛信息</button></p>
<?php
date_default_timezone_set("Asia/ShangHai");

ini_set('display_errors',0);            //错误信息1
ini_set('display_startup_errors',0);    //php启动错误信息1
error_reporting(0);                    //打印出所有的 错误信息-1
include("../sqlite3.php");

if($_COOKIE["lyclub_dafen_admin_auuid"]==""||$_COOKIE["lyclub_dafen_admin_auser"]==""||$_COOKIE["lyclub_dafen_admin_alias"]==""||$_COOKIE["lyclub_dafen_admin_brith"]==""||$_COOKIE["lyclub_dafen_admin_autel"]==""||$_COOKIE["lyclub_dafen_admin_auuqq"]==""||$_COOKIE["lyclub_dafen_admin_aumail"]==""||$_COOKIE["lyclub_dafen_admin_timeout"]=="")
{
	echo "<script>window.alert('管理用户信息有错误！请重新登录！');location.href='login.php';</script>";
	exit(0);
}

$db=new database("data/main.db");

if(@$_GET["del"]=="yes"&&@$_GET["bsgid"]!="")
{
	while(is_file("../data/FenDb/".$_GET["bsgid"].".db"))
	{
		unlink("../data/FenDb/".$_GET["bsgid"].".db");
	}
	if($db->exec("delete from bsinfo where bs_gid=".$_GET["bsgid"]))
	{
		echo "<script>location.href='index.php';</script>";
	}
	else
	{
		echo "<script>location.href='index.php?del=yes&bsgid=".$_GET["bsgid"]."';</script>";
	}
}
/*++++bsinfo+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * bs_gid  + bs_gtitle +  bs_pass   +  bs_gstart   + bs_gend      + bs_ginfo +  bs_cuser/bs_ctime + bs_bakinfo  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *比赛ID号 + 比赛标题  + 评委验证码 + 比赛开始时间 + 比赛结束时间 + 说明信息 + 创建用户的时间    + 比赛备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
if($_COOKIE["lyclub_dafen_admin_auser"]=="admin")
	$dta=$db->query("select * from bsinfo order by bs_gid asc");
else	
	$dta=$db->query("select * from bsinfo where bs_cuser='".$_COOKIE["lyclub_dafen_admin_auser"]."' order by bs_gid asc");
echo "<hr/>";
while($dtb=$dta->fetchArray())
{
	echo "</table><p>&nbsp;</p>";
	echo "<table border='1' cellspacing='0' cellpadding='2'>";
	echo "<tr><th>比赛代码</th><th>比赛题目</th><th>评分开始时间</th><th>评分结束时间</th><th>发布作者</th><th>发布时间</th><th>备注信息</th></tr>";
	echo "<tr><th>".$dtb["bs_gid"]."</th><th title='比赛说明信息:\n".$dtb["bs_ginfo"]."'>".$dtb["bs_gtitle"]."</th><th>".$dtb["bs_gstart"]."</th><th>".$dtb["bs_gend"]."</th><th>".$dtb["bs_cuser"]."</th><th>".$dtb["bs_ctime"]."</th><th>".$dtb["bs_bakinfo"]."</th></tr>";
	echo "<tr><th colspan='7'><button onclick='javascript:location.href=\"update.php?code=".$dtb["bs_gid"]."&fun=xmscore\";'>打分处理结果</button>&nbsp;&nbsp;<button onclick='javascript:location.href=\"update.php?code=".$dtb["bs_gid"]."&fun=xmtime\";'>添加打分时间</button>&nbsp;&nbsp;<button onclick='javascript:location.href=\"view.php?bsgid=".$dtb["bs_gid"]."\";'>查看打分结果</button>&nbsp;&nbsp;<button onclick='javascript:location.href=\"index.php?del=yes&bsgid=".$dtb["bs_gid"]."\";'>删除比赛信息和文件</button></th></tr>";
	echo "</table><p>&nbsp;</p>";
	echo "<hr/>";
}
?>
</center>
</body>
</html>