<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title>比赛打分系统</title>
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
<lyclub>比赛评分系统</lyclub>
<?php
date_default_timezone_set("Asia/ShangHai");


include("sqlite3.php");
ini_set('display_errors',0);            //错误信息1
ini_set('display_startup_errors',0);    //php启动错误信息1
error_reporting(0);                    //打印出所有的 错误信息-1

//查询比赛代码信息，成功直接写入cookie，失败不执行
function sessionid($cid)
{
/*+++++bsinfo++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * bs_gid  + bs_gtitle +  bs_pass   +  bs_gstart   + bs_gend      + bs_ginfo +  bs_cusr/bs_ctime + bs_bakinfo   +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *比赛ID号 + 比赛标题  + 评委验证码 + 比赛开始时间 + 比赛结束时间 + 说明信息 + 创建用户的时间    + 比赛备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/	
	if(strtotime($cid["bs_gend"])<=strtotime(date("Y-m-d H:i:s")))
	{
		echo "<script>window.alert('比赛已经过期，请重新输入新的比赛代码');</script>";
		return false;
	}
	else
	{
		$cokend=strtotime($cid["bs_gend"]);	//比赛结束时间
		setcookie("lyclub_dafen_gid",$cid["bs_gid"],$cokend);			//比赛代码写入
		setcookie("lyclub_dafen_gname",$cid["bs_gtitle"],$cokend);		//比赛英文名
		setcookie("lyclub_dafen_gtitle",$cid["bs_gtitle"],$cokend);		//比赛显示的别名
		setcookie("lyclub_dafen_end",$cid["bs_gend"],$cokend);			//比赛结束时间字符串
		setcookie("lyclub_dafen_start",$cid["bs_gstart"],$cokend);		//比赛开始时间字符串
		setcookie("lyclub_dafen_info",$cid["bs_ginfo"],$cokend);		//比赛信息说明
		setcookie("lyclub_dafen_pass",$cid["bs_pass"],$cokend);				//比赛验证码
		return true;
	}
}

function wrcok($bsid)
{
	$gqkend=time()-3600;
	setcookie("lyclub_dafen_gid",null,$gqkend);
	setcookie("lyclub_dafen_gname",null,$gqkend);
	setcookie("lyclub_dafen_gtitle",null,$gqkend);
	setcookie("lyclub_dafen_end",null,$gqkend);
	setcookie("lyclub_dafen_start",null,$gqkend);
	setcookie("lyclub_dafen_info",null,$gqkend);
/*+++++bsinfo++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * bs_gid  + bs_gtitle +  bs_pass   +  bs_gstart   + bg_gend      + bs_ginfo +  bs_cusr/bs_ctime + bs_bakinfo   +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *比赛ID号 + 比赛标题  + 评委验证码 + 比赛开始时间 + 比赛结束时间 + 说明信息 + 创建用户的时间    + 比赛备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	//判断后台数据有没有此代码信息，有返回true并把信息写入cookie
	//后台数据没有，则显示错误提示并返回false
	//获取空什么都不显示直接返回false
	$db=new database("data/main.db");
	
	if($bsid=="")
		return false;
	else
	{
		$mainselect="select * from bsinfo where bs_gid=".$bsid;
		$maindf=$db->query($mainselect);
		$mdfarr=$maindf->fetchArray();
	}
	if(count($mdfarr)>0&&$mdfarr!=null)
	{
		if(sessionid($mdfarr))
			return true;
		else
			return false;
	}
	else
	{
		echo "<script>window.alert('系统未查询到发布信息，请重新输入');history.go(-1);</script>";
		return false;
	}
}

if(wrcok($_POST["goid"]))
{
	switch($_POST["goxz"])
	{
		case "我是评委" : header("Location: pingwei.php"); break;
		case "我是观众" : header("Location: guest.php"); break;
	}
}
?>
<form action=" ?option_go" method="post" style="margin:20px;padding:20px;"></p>
<p>请输入比赛代码</p>
<p><input type="text" id="goid" name="goid" value="" /></p>
<p><input type="submit" id="goxz" name="goxz" value="我是评委" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" id="goxz" name="goxz" value="我是观众" />
</p>
</form>
</center>
</body>
</html>