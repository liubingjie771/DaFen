<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title>评委打分</title>
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


include("sqlite3.php");
ini_set('display_errors',0);            //错误信息1
ini_set('display_startup_errors',0);    //php启动错误信息1
error_reporting(0);                    //打印出所有的 错误信息-1



//setcookie("lyclub_dafen_gid",null,$gqkend);
//setcookie("lyclub_dafen_gname",null,$gqkend);
//setcookie("lyclub_dafen_gtitle",null,$gqkend);
//setcookie("lyclub_dafen_end",null,$gqkend);
//setcookie("lyclub_dafen_start",null,$gqkend);
//setcookie("lyclub_dafen_info",null,$gqkend);
$cokend=strtotime($_COOKIE["lyclub_dafen_end"]);	//比赛结束时间

if($_COOKIE["lyclub_dafen_gid"]==""||$_COOKIE["lyclub_dafen_gname"]==""||$_COOKIE["lyclub_dafen_gtitle"]==""||$_COOKIE["lyclub_dafen_end"]==""||$_COOKIE["lyclub_dafen_start"]==""||$_COOKIE["lyclub_dafen_info"]=="")
{
	echo <<<ERR
	<script lanaguage="javascript">
		window.alert("没有正确获取到比赛信息，请重新输入代码！");
		location.href="index.php";
	</script>
ERR;
	exit(0);
}

if($_SERVER["QUERY_STRING"]=="login")
{
	$kdb=new database("data/FenDb/".$_COOKIE["lyclub_dafen_gid"].".db");
	$pwi=$kdb->query("select * from pwinfo where pw_name='".$_POST["pwacc"]."'");
	$pwf=$pwi->fetchArray();
	if($_POST["pwacc"]==null)
	{
		echo <<<ERR
		<script lanaguage="javascript">
			window.alert("评委姓名不能为空，请重新输入！");
			history.go(-1);
		</script>
ERR;
	}
	elseif($pwf["pw_id"]==0 && $pwf["pw_id"]==null)
	{
		echo <<<ERR
		<script lanaguage="javascript">
			window.alert("评委姓名不正确，请重新输入！");
			history.go(-1);
		</script>
ERR;
	}
	elseif($_COOKIE["lyclub_dafen_pass"]!=sha1($_POST["pwpas"]))
	{
		echo <<<ERR
		<script lanaguage="javascript">
			window.alert("验证码不正确，请重新输入！");
			history.go(-1);
		</script>
ERR;
	}
	else
	{
		setcookie("lyclub_dafen_pwname",$pwf["pw_name"],$cokend);			//cookie写入评委姓名
		setcookie("lyclub_dafen_pwid","pwsc".$pwf["pw_id"],$cokend);		//cookie写入评委ID号（带pwsc开头的ID号）
		setcookie("lyclub_dafen_pwjob",$pwf["pw_job"],$cokend);				//cookie写入评委单位名
		header("Location: ?Add");												//跳转?Add
	}
	
}
elseif($_SERVER["QUERY_STRING"]=="Add")
{
/*++++++xmscore++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *  xm_id   +      pwsc1    +      pwsc2    +      pwsc3    +      pwsc4    + sc_start + sc_end   + sc_bakinfo  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * 项目ID号 +评委人员表ID=1 +评委人员表ID=2 +评委人员表ID=3 +评委人员表ID=4 +打分开始时+打分结束时+打分备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	$kdb=new database("data/FenDb/".$_COOKIE["lyclub_dafen_gid"].".db");
	$xmi=$kdb->query("select * from xmscore");
/*++++++xminfo++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  +  xm_id   +  xm_name   +  xm_title    +  xm_info   +  xm_bakinfo  +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  + 项目ID号 + 项目单位名 + 单位的项目名 + 项目的信息 + 项目备注信息 +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	$dfnm=array();
	while($xmf=$xmi->fetchArray())
	{
		if($xmf["sc_start"]=="")
		{
			continue;
		}
		elseif($xmf["sc_end"]=="")
		{
			continue;
		}
		elseif(strtotime($xmf["sc_start"])>strtotime(date("Y-m-d H:i:s")))
		{
			continue;
		}
		elseif(strtotime(date("Y-m-d H:i:s"))>strtotime($xmf["sc_end"]))
		{
			continue;
		}
		$xma=$kdb->query("select * from xminfo where xm_id=".$xmf["xm_id"]);
		$xmb=$xma->fetchArray();
		$dfnm[]="<option value='".$xmb["xm_id"]."'>".$xmb["xm_name"]."</option>\n";
	}

	echo <<<DAFENFORM1
	<lyclub>评委打分界面</lyclub>
	<form action="?dafen" method="post">
	<table border="0" style="margin:20px;padding:20px;">
	<tr><td>评分单位：</td><td><select type="text" id="df_name" name="df_name">\n
DAFENFORM1;
	for($i=0;$i<count($dfnm);$i++)
	{
		echo $dfnm[$i];
	}
	echo <<<DAFENFORM2
	\n</select><input type="button" onclick="javascript:location.reload();" value="刷新" /></td></tr>
	<tr><td>输入分数：</td><td><input type='number' id='df_score' name='df_score' value='0' min='0' max='100'/>(0-100)</td></tr>
	<tr><td colspan="2" align="center"><input type="submit" value="评分" /></td></tr>
	</table>
	</form>
DAFENFORM2;
}
elseif($_SERVER["QUERY_STRING"]=="dafen")
{
	if($_POST["df_score"]==""||$_POST["df_score"]==0)
	{
		echo <<<FCES
		<script language="javascript">
			window.alert("分数不能为空！");
			location.href='?Add';
		</script>
FCES;
	}
	elseif($_POST["df_name"]=="")
	{
		echo <<<FCES
		<script language="javascript">
			window.alert("获取单位失败，请重新输入！");
			location.href='?Add';
		</script>
FCES;
	}
	$kdb=new database("data/FenDb/".$_COOKIE["lyclub_dafen_gid"].".db");
/*++++++xmscore++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *  xm_id   +      pwsc1    +      pwsc2    +      pwsc3    +      pwsc4    + sc_start + sc_end   + sc_bakinfo  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * 项目ID号 +评委人员表ID=1 +评委人员表ID=2 +评委人员表ID=3 +评委人员表ID=4 +打分开始时+打分结束时+打分备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	if($kdb->exec("update xmscore set ".$_COOKIE["lyclub_dafen_pwid"]."=".$_POST["df_score"]." where xm_id=".$_POST["df_name"]))
	{
		echo <<<FCES
		<script language="javascript">
			window.alert("评分成功！");
			location.href='?Add';
		</script>
FCES;
	}
	else
	{
		echo <<<FCES
		<script language="javascript">
			window.alert("评分失败！");
			location.href='?Add';
		</script>
FCES;
	}
}
else
{
	//用户登录界面
	echo <<<USERFORM
	<lyclub>评委验证登录</lyclub>
	<form action="?login" method="post">
	<table border="0" style="margin:20px;padding:20px;">
	<tr><td align="right">评委姓名：</td><td><input type="text" id="pwacc" name="pwacc" value="" /></td></tr>
	<tr><td align="right">验证密码：</td><td><input type="text" id="pwpas" name="pwpas" value="" /></td></tr>
	<tr><td colspan="2" align="center"><input type="submit" value="登录" /></td></tr>
	</table>
	</form>
USERFORM;
}	
?>
</center>
</body>
</html>