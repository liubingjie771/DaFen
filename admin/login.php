<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title>比赛打分系统-管理系统</title>
<style type="text/css">
body { margin:20px;padding:20px; }
lyclub 
{
	text-align:center;
	font-size:50px;
	background:lightgray;
	font-family: 黑体;
}
</style>
</head>
<body>
<center>

<?php
date_default_timezone_set("Asia/ShangHai");

ini_set('display_errors',0);            //错误信息1
ini_set('display_startup_errors',0);    //php启动错误信息1
error_reporting(0);                    //打印出所有的 错误信息-1

$uauacc="";
if($_POST["uau_acc"]==""&&$_POST["uau_pass"]=="")
{
		$gqtime=time()-3600;
		setcookie("lyclub_dafen_admin_auuid","",$gqtime);			//管理用户的ID号
		setcookie("lyclub_dafen_admin_auser","",$gqtime);		//管理用户的账号名
		setcookie("lyclub_dafen_admin_alias","",$gqtime);		//管理用户的显示名
		setcookie("lyclub_dafen_admin_brith","",$gqtime);		//管理用户的生日
		setcookie("lyclub_dafen_admin_autel","",$gqtime);		//管理用户的手机号
		setcookie("lyclub_dafen_admin_auuqq","",$gqtime);			//管理用户的QQ号码
		setcookie("lyclub_dafen_admin_aumail","",$gqtime);		//管理用户的邮箱地址
		setcookie("lyclub_dafen_admin_timeout","",$gqtime);				//管理用户有效登录时间数值
?>
<lyclub>管理户登录</lyclub>
<form action=' ' method='post'>
<table border=0' cellspacing='0' cellpadding='0'>
<tr><th>用户账号：</th><td><input type="text" id="uau_acc" name="uau_acc" value="" /></td></tr>
<tr><th>账号密码：</th><td><input type="password" id="uau_pass" name="uau_pass" value="" /></td></tr>
<tr><th colspan='2'><input type="submit" value="登录" /></th></tr>
</table>
</form>
<?php
exit(0);
}
function cokusr($udata)
{
/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *  au_id   +  au_acc    +  au_pass     +  au_alias    +  au_tel    +  au_qq   +  au_mail     +  au_birth    +  au_bakinfo   +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * 用户ID号 + 用户账号名 + 用户账号密码 + 用户显示别名 + 用户手机号 + 用户QQ号 + 用户电子邮箱 + 用户生日日期 + 用户备注信息  +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	if($udata["au_pass"]==sha1($_POST["uau_pass"]))
	{
		$outtime=time()+3600;
		setcookie("lyclub_dafen_admin_auuid",$udata["au_id"],$outtime);			//管理用户的ID号
		setcookie("lyclub_dafen_admin_auser",$udata["au_acc"],$outtime);		//管理用户的账号名
		setcookie("lyclub_dafen_admin_alias",$udata["au_alias"],$outtime);		//管理用户的显示名
		setcookie("lyclub_dafen_admin_brith",$udata["au_birth"],$outtime);		//管理用户的生日
		setcookie("lyclub_dafen_admin_autel",$udata["au_tel"],$outtime);		//管理用户的手机号
		setcookie("lyclub_dafen_admin_auuqq",$udata["au_qq"],$outtime);			//管理用户的QQ号码
		setcookie("lyclub_dafen_admin_aumail",$udata["au_mail"],$outtime);		//管理用户的邮箱地址
		setcookie("lyclub_dafen_admin_timeout",$outtime,$outtime);				//管理用户有效登录时间数值
		return null;
	}
	else
	{
		return "账户密码输入错误";
	}
}
if($_POST["uau_acc"]!="")
{
	$uauacc=$_POST["uau_acc"];
	include("../sqlite3.php");
	$db=new database("data/main.db");

	$uauselect="select * from admin_users where au_acc='".$uauacc."'";
	$bsa=$db->query($uauselect);	
	$bsf=$bsa->fetchArray();
	if(count($bsf)>1)
		if(($cerrf=cokusr($bsf))==null)
		{
			echo "<script>window.alert('管理用户登录成功！');location.href='index.php';</script>";
		}
		else
		{
			echo "<script>window.alert('".$cerrf."');history.go(-1);</script>";
		}	
	else
		echo "<script>window.alert('用户不存在，请重新输入1');history.go(-1);</script>";
}
?>
</center>
</body>
</html>