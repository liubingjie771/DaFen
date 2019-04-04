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
<?php
date_default_timezone_set("Asia/ShangHai");

ini_set('display_errors',0);            //错误信息1
ini_set('display_startup_errors',0);    //php启动错误信息1
error_reporting(0);                    //打印出所有的 错误信息-1
include("../sqlite3.php");


//setcookie("lyclub_dafen_admin_auuid",$udata["au_id"],$outtime);			//管理用户的ID号
//setcookie("lyclub_dafen_admin_auser",$udata["au_acc"],$outtime);			//管理用户的账号名
//setcookie("lyclub_dafen_admin_alias",$udata["au_alias"],$outtime);		//管理用户的显示名
//setcookie("lyclub_dafen_admin_brith",$udata["au_birth"],$outtime);		//管理用户的生日
//setcookie("lyclub_dafen_admin_autel",$udata["au_tel"],$outtime);			//管理用户的手机号
//setcookie("lyclub_dafen_admin_auuqq",$udata["au_qq"],$outtime);			//管理用户的QQ号码
//setcookie("lyclub_dafen_admin_aumail",$udata["au_mail"],$outtime);		//管理用户的邮箱地址
//setcookie("lyclub_dafen_admin_timeout",$outtime,$outtime);				//管理用户有效登录时间数值
		
if($_COOKIE["lyclub_dafen_admin_auuid"]==""||$_COOKIE["lyclub_dafen_admin_auser"]==""||$_COOKIE["lyclub_dafen_admin_alias"]==""||$_COOKIE["lyclub_dafen_admin_brith"]==""||$_COOKIE["lyclub_dafen_admin_autel"]==""||$_COOKIE["lyclub_dafen_admin_auuqq"]==""||$_COOKIE["lyclub_dafen_admin_aumail"]==""||$_COOKIE["lyclub_dafen_admin_timeout"]=="")
{
	echo "<script>window.alert('管理用户信息有错误！请重新登录！');location.href='login.php';</script>";
	exit(0);
}
/*++++bsinfo+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * bs_gid  + bs_gtitle +  bs_pass   +  bs_gstart   + bs_gend      + bs_ginfo +  bs_cuser/bs_ctime + bs_bakinfo  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *比赛ID号 + 比赛标题  + 评委验证码 + 比赛开始时间 + 比赛结束时间 + 说明信息 + 创建用户的时间    + 比赛备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

//create1  创建比赛信息表单未处理
function create_main_form1()
{
	echo <<<bscreate1
		<lyclub>第一步：填写比赛基本信息</lyclub>
		<form action="?main_create2" method="post">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th>比赛主题：</th>
					<td><input type="text" id="bs_gtit" name="bs_gtit" value="" /></td>
				</tr>
				<tr>
					<th>开始时间：</th>
					<td><input type="date" id="bs_gstart_d" name="bs_gstart_d" value="" /><input type="time" id="bs_gstart_t" name="bs_gstart_t" value="" /></td>
				</tr>
				<tr>
					<th>结束时间：</th>
					<td><input type="date" id="bs_gend_d" name="bs_gend_d" value="" /><input type="time" id="bs_gend_t" name="bs_gend_t" value="" /></td>
				</tr>
				<tr>
					<th>比赛信息：</th>
					<td><textarea id="bs_ginfo" name="bs_ginfo"></textarea></td>
				</tr>
				<tr>
					<th colspan="2"><input type="submit" value="下一步"/>&nbsp;&nbsp;<input type="reset" value="重填当前信息" /></th>
				</tr>
			</table>
			<input type="hidden" id="bs_pass" name="bs_pass" value="123456" />
			<input type="hidden" id="bs_gid" name="bs_gid" value="" />
		</form>
bscreate1;
}
//create2  确认比赛信息并生成ID号，最后手动输入验证码
function create_main_form2()
{
	if($_POST["bs_gtit"]==""||$_POST["bs_gstart_d"]==""||$_POST["bs_gstart_t"]==""||$_POST["bs_gend_d"]==""||$_POST["bs_gend_t"]==""||$_POST["bs_ginfo"]=="")
	{
		echo "<script>window.alert('以上信息必须全部填写！');history.go(-1);</script>";
	}
	elseif(strtotime($_POST["bs_gstart_d"]." ".$_POST["bs_gstart_t"])>=strtotime($_POST["bs_gend_d"]." ".$_POST["bs_gend_t"]))
	{
		echo "<script>window.alert('开始时间和结束时间不正确，请修改！');history.go(-1);</script>";
	}
	echo <<<bscreate2
		<lyclub>第二步：确认比赛基本信息并获取代码和输入验证码</lyclub>
		<form action="?main_create3" method="post">
			<table border="0" cellspacing="0" cellpadding="0">
bscreate2;
	echo "<tr><th>比赛主题：</th><td><input type=\"text\" id=\"bs_gtit\" name=\"bs_gtit\" value=\"".$_POST["bs_gtit"]."\" /></td></tr>";
	echo "<tr><th>开始时间：</th><td><input type=\"date\" id=\"bs_gstart_d\" name=\"bs_gstart_d\" value=\"".$_POST["bs_gstart_d"]."\" /><input type=\"time\" id=\"bs_gstart_t\" name=\"bs_gstart_t\" value=\"".$_POST["bs_gstart_t"]."\" /></td></tr>";
	echo "<tr><th>结束时间：</th><td><input type=\"date\" id=\"bs_gend_d\" name=\"bs_gend_d\" value=\"".$_POST["bs_gend_d"]."\" /><input type=\"time\" id=\"bs_gend_t\" name=\"bs_gend_t\" value=\"".$_POST["bs_gend_t"]."\" /></td></tr>";
	echo "<tr><th>比赛信息：</th><td><textarea id=\"bs_ginfo\" name=\"bs_ginfo\">".$_POST["bs_ginfo"]."</textarea></td></tr>";
	echo "<tr><th style=\"color:red\">输入验证码：</th><td><input type=\"text\" id=\"bs_pass\" name=\"bs_pass\" placeholder=\"".$_POST["bs_pass"]."\"/></td></tr>";
	echo "<tr><th colspan=\"2\"><p>&nbsp;&nbsp;<input type=\"submit\" value=\"下一步\"/></p></th></tr>";
	echo "</table>";
	echo "</form>";
}
//create2  生成sql的插入语句，输入评委信息
function create_main_form3()
{
	if($_POST["bs_gtit"]==""||$_POST["bs_gstart_d"]==""||$_POST["bs_gstart_t"]==""||$_POST["bs_gend_d"]==""||$_POST["bs_gend_t"]==""||$_POST["bs_ginfo"]==""||$_POST["bs_pass"]=="")
	{
		echo "<script>window.alert('以上信息必须全部填写！');history.go(-1);</script>";
	}
	elseif(strtotime($_POST["bs_gstart_d"]." ".$_POST["bs_gstart_t"])>=strtotime($_POST["bs_gend_d"]." ".$_POST["bs_gend_t"]))
	{
		echo "<script>window.alert('开始时间和结束时间不正确，请修改！');history.go(-1);</script>";
	}
	$bsdb=new database("data/main.db");
	$bsst=$bsdb->query("select max(bs_gid) as maxid from bsinfo where bs_gid>=".date("Ym")."001 ");
	$bssr=$bsst->fetchArray();
	if($bssr["maxid"]=="")
		$scbsid=date("Ym")."001";
	else
		$scbsid=$bssr["maxid"]+1;
	$main_sql=urlencode("insert into bsinfo values(".$scbsid.",'".$_POST["bs_gtit"]."','".sha1($_POST["bs_pass"])."','".$_POST["bs_gstart_d"]." ".$_POST["bs_gstart_t"].":00','".$_POST["bs_gend_d"]." ".$_POST["bs_gend_t"].":00','".$_POST["bs_ginfo"]."','".$_COOKIE["lyclub_dafen_admin_auser"]."','".date("Y-m-d H:i:s")."','')");
	if(!$bsdb->exec(urldecode($main_sql)))
	{
		echo "<script>window.alert(\"你输入的信息有问题请返回重新确认！\");history.go(-1);</script>";
		exit(0);
	}
	echo "<lyclub>第三步：输入评委信息</lyclub>";
	echo "<p>比赛代码为<font style=\"size:20px;font-weight:bold;color:red;\">".$scbsid."</font></p>";
	
	echo <<<PWJS
	<script language="javascript">
	function dodel(bt){
		var pwnum=document.getElementById("pwtable").rows.length;
		pwnum=parseInt(pwnum)-2;
		document.getElementById("bs_pw_num").value=pwnum;
		var tab = document.getElementById("pwtable");
		tab.deleteRow(bt.parentNode.parentNode.rowIndex);
		
	}
	function AddPW()
	{
		var pwnum=document.getElementById("pwtable").rows.length;
		document.getElementById("bs_pw_num").value=pwnum;
		var pwaddtr=document.getElementById("pwtable").insertRow();
		pwaddtr.insertCell(0).innerHTML="<input type=\"text\" id=\"pw_name[]\" name=\"pw_name[]\" value=\"\" />";
		pwaddtr.insertCell(1).innerHTML="<input type=\"text\" id=\"pw_job[]\" name=\"pw_job[]\" value=\"\" />";
		pwaddtr.insertCell(2).innerHTML="&nbsp;<button onclick='dodel(this);'>删除</button>&nbsp;";
	}
	</script>
PWJS;

	echo "<form action=\"?main_create4\" method=\"post\">";
	echo "<input type=\"hidden\" id=\"bs_gid\" name=\"bs_gid\" value=\"".$scbsid."\" />";
	echo "<input type=\"hidden\" id=\"bs_sql_info\" name=\"bs_sql_info\" value=\"".urlencode($main_sql)."\" />";
	echo "<input type=\"hidden\" id=\"bs_pw_num\" name=\"bs_pw_num\" value=\"1\" />";
	echo "<table id=\"pwtable\" name=\"pwtable\" border=\"1\" cellspacing=\"0\" cellpadding=\"2\">";
	echo "<tr style='background:lightblue'><th>评委姓名</th><th>评委职称</th><td>&nbsp;<a href='javascript:AddPW();'>添加</a>&nbsp;</td></tr>";
	echo "<tr><td><input type=\"text\" id=\"pw_name[]\" name=\"pw_name[]\" value=\"\" /></td><td><input type=\"text\" id=\"pw_job[]\" name=\"pw_job[]\" value=\"\" /></td><td>&nbsp;<button onclick='dodel(this);'>删除</button>&nbsp;</tr>";
	echo "</table>";
	echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"下一步\"/></p>";
	echo "</form>";
}
/*+++++pwinfo++++++++++++++++++++++++++++++++++++++++++++
  +  pw_id    +  pw_name  +  pw_job    +  pw_bakinfo    +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++
  + 评委ID号  + 评委姓名  +  评委职位  +  评委备注信息  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
//生成sql的插入语句，输入项目单位信息
function create_main_form4()
{
	if($_POST["bs_gid"]==""||$_POST["bs_sql_info"]=="")
	{
		echo "<script>window.alert(\"你没有从第一步开始的\n或者数据走丢\n请重新录入信息\");location.href='?main_crete1';</script>";
	}
	$pwnum=$_POST["bs_pw_num"];
	for($i=0;$i<$pwnum;$i++)
	{
		if($_POST["pw_name"][$i]=="")
		{
			echo "<script>window.alert(\"评委信息中姓名有空值!\");history.go(-1);</script>";
			break;
		}
		elseif($_POST["pw_name"][$i]==$_POST["pw_job"][$i])
		{
			echo "<script>window.alert(\"评委信息中姓名[".$_POST["pw_name"][$i]."]和职称".$_POST["pw_job"][$i]."不允许一样\");history.go(-1);</script>";
			break;
		}
	}
	$pwsql="";
	for($j=0;$j<$pwnum;$j++)
	{
		$pwsql=$pwsql.urlencode("insert into pwinfo values(".($j+1).",'".$_POST["pw_name"][$j]."','".$_POST["pw_job"][$j]."','')")."(<||>)";
	}
	$pwsql=substr($pwsql,0,-6);
	echo <<<XMJS
	<script language="javascript">
	function dodel(bt){
		var xmnum=document.getElementById("xmtable").rows.length;
		xmnum=parseInt(xmnum)-2;
		document.getElementById("bs_xm_num").value=xmnum;
		var tab = document.getElementById("xmtable");
		tab.deleteRow(bt.parentNode.parentNode.rowIndex);
		
	}
	function AddXM()
	{
		var xmnum=document.getElementById("xmtable").rows.length;
		document.getElementById("bs_xm_num").value=xmnum;
		var xmaddtr=document.getElementById("xmtable").insertRow();
		xmaddtr.insertCell(0).innerHTML="<input type=\"number\" id=\"xm_id[]\" name=\"xm_id[]\" value=\"\" />";
		xmaddtr.insertCell(1).innerHTML="<input type=\"text\" id=\"xm_name[]\" name=\"xm_name[]\" value=\"\" />";
		xmaddtr.insertCell(2).innerHTML="<input type=\"text\" id=\"xm_tit[]\" name=\"xm_tit[]\" value=\"\" />";
		xmaddtr.insertCell(3).innerHTML="<textarea id=\"xm_info[]\" name=\"xm_info[]\" ></textarea>";
		xmaddtr.insertCell(4).innerHTML="&nbsp;<button onclick='dodel(this);'>删除</button>&nbsp;";
	}
	</script>
XMJS;
	echo "<lyclub>第四步：填写项目单位和主题名称信息</lyclub>";
	echo "<p>比赛代码为<font style=\"size:20px;font-weight:bold;color:red;\">".$_POST["bs_gid"]."</font></p>";	
	echo "<form action=\"?main_create5\" method=\"post\">";
	echo "<input type='hidden' id='bsidinfo' name='bsidinfo' value='".$_POST["bs_gid"]."' />";
	echo "<input type='hidden' id='bseninfo' name='bseninfo' value='".urlencode($_POST["bs_sql_info"])."' />";
	echo "<input type='hidden' id='pweninfo' name='pweninfo' value='".urlencode($pwsql)."' />";
	echo "<input type=\"hidden\" id=\"bs_xm_num\" name=\"bs_xm_num\" value=\"1\" />";
	echo "<table id=\"xmtable\" name=\"xmtable\" border=\"1\" cellspacing=\"0\" cellpadding=\"2\">";
	echo "<tr style='background:lightblue'><th>项目号</th><th>项目单位名</th><th>项目的主题名称</th><th>说明信息</th><td>&nbsp;<a href='javascript:AddXM();'>添加</a>&nbsp;</td></tr>";
	echo "<tr><td><input type=\"number\" id=\"xm_id[]\" name=\"xm_id[]\" value=\"\" /></td><td><input type=\"text\" id=\"xm_name[]\" name=\"xm_name[]\" value=\"\" /></td><td><input type=\"text\" id=\"xm_tit[]\" name=\"xm_tit[]\" value=\"\" /></td><td><textarea id=\"xm_info[]\" name=\"xm_info\" ></textarea></td><td>&nbsp;<button onclick='dodel(this);'>删除</button>&nbsp;</tr>";
	echo "</table>";
	echo "<p><button onclick=\"javascript:history.go(-1);\">上一步</button>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"下一步\"/></p>";
	echo "</form>";
}
/*++++++xminfo++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  +  xm_id   +  xm_name   +  xm_title    +  xm_info   +  xm_bakinfo  +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  + 项目ID号 + 项目单位名 + 单位的项目名 + 项目的信息 + 项目备注信息 +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
function create_main_form5()
{
	if($_POST["bsidinfo"]==""||$_POST["bseninfo"]==""||$_POST["pweninfo"]=="")
	{
		echo "<script>window.alert(\"你没有从第一步开始的\n或者数据走丢\n请重新录入信息\");location.href='?main_crete1';</script>";
	}
	$xmnum=$_POST["bs_xm_num"];
	for($i=0;$i<$xmnum;$i++)
	{
		if($_POST["xm_id"][$i]=="")
		{
			echo "<script>window.alert(\"项目号有空值!\");history.go(-1);</script>";
			break;
		}
		elseif($_POST["xm_name"][$i]=="")
		{
			echo "<script>window.alert(\"单位名有空值!\");history.go(-1);</script>";
			break;
		}
		elseif($_POST["xm_name"][$i]==$_POST["xm_tit"][$i])
		{
			echo "<script>window.alert(\"单位名[".$_POST["xm_name"][$i]."]和主题名称".$_POST["xm_tit"][$i]."不允许一样\");history.go(-1);</script>";
			break;
		}
		$xmidsuc=0;
		$xmidfad=0;
		for($k=0;$k<=$i;$k++)
		{
			if($_POST["xm_id"][$i]==$_POST["xm_id"][$k])
			{
				$xmidfad=$xmidfad+1;
			}
			else
			{
				$xmidsuc=$xmidsuc+1;
			}
		}
		if($xmnum==($xmidsuc+$xmidfad)&&$xmidfad>1)
		{
			echo "<script>window.alert(\"[".$_POST["xm_id"][$i]."]有一样".$xmidfad."\");history.go(-1);</script>";
			break;
		}
		$xmsql[$i]="insert into xminfo values(".$_POST["xm_id"][$i].",'".$_POST["xm_name"][$i]."','".$_POST["xm_tit"][$i]."','".$_POST["xm_info"][$i]."','')";
	}
	$cdbB=new database("data/FenDb/".$_POST["bsidinfo"].".db");
	if(!$cdbB->exec("create table bsinfo(bs_gid bigint unique,bs_gtitle varchar(500) not null,bs_pass varchar(100) not null,bs_gstart datetime not null,bs_gend datetime not null,bs_ginfo varchar(1000) not null,bs_cuser varchar(100),bs_ctime datetime,bs_bakinfo varchar(1000))"))
	{
		echo "<script>window.alert(\"系统填写冲突，请重新输入提交0！\");location.href='?main_create1';</script>";
		exit(0);
	}
	if(!$cdbB->exec(urldecode(urldecode(urldecode($_POST["bseninfo"])))))
	{
		echo "<script>window.alert(\"系统填写冲突，请重新输入提交1！\");location.href='?main_create1';</script>";
		exit(0);
	}
	if(!$cdbB->exec("create table pwinfo(pw_id int unique,pw_name varchar(100) not null,pw_job varchar(100),pw_bakinfo varchar(1000))"))
	{
		echo "<script>window.alert(\"系统填写冲突，请重新输入提交2！\");location.href='?main_create1';</script>";
		exit(0);
	}
	$pwifarr=explode("(<||>)",urldecode(urldecode(urldecode($_POST["pweninfo"]))));
	for($m=0;$m<count($pwifarr);$m++)
	{
		if(!$cdbB->exec($pwifarr[$m]))
		{
			echo "<script>window.alert(\"系统填写冲突，请重新输入提交3-".$pwifarr[$m]."！\");location.href='?main_create1';</script>";
			exit(0);
		}
	}
	if(!$cdbB->exec("create table xminfo(xm_id bigint unique,xm_name varchar(100) not null,xm_title varchar(100) not null,xm_info varchar(1000),xm_bakinfo varchar(1000))"))
	{
		echo "<script>window.alert(\"系统填写冲突，请重新输入提交4！\");location.href='?main_create1';</script>";
		exit(0);
	}
	for($n=0;$n<count($xmsql);$n++)
	{
		if(!$cdbB->exec($xmsql[$n]))
		{
			echo "<script>window.alert(\"系统填写冲突，请重新输入提交5-".$xmsql[$n]."！\");location.href='?main_create1';</script>";
			exit(0);
		}
	}
/*++++++xmscore++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *  xm_id   +      pwsc1    +      pwsc2    +      pwsc3    +      pwsc4    + sc_str   + sc_max   + sc_min   +  sc_avg  + sc_start + sc_end   + sc_bakinfo  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * 项目ID号 +评委人员表ID=1 +评委人员表ID=2 +评委人员表ID=3 +评委人员表ID=4 +分数字符串+去掉最高分+去掉最低分+最终平均分+打分开始时+打分结束时+打分备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	$xmsc="create table xmscore(xm_id bigint unique,";
	$scpwA=$cdbB->query("select * from pwinfo");
	$pwznum="";
	while($scpwB=$scpwA->fetchArray())
	{
		$xmsc=$xmsc."pwsc".$scpwB["pw_id"]." int,";
		$pwznum=$pwznum."0,";
	}
	$xmsc=$xmsc."sc_str varchar(5000),sc_max varchar(100),sc_min varchar(100),sc_avg double,sc_start datetime,sc_end datetime,sc_bakinfo varchar(1000))";
	if(!$cdbB->exec($xmsc))
	{
		echo "<script>window.alert(\"系统填写冲突，请重新输入提交6！\");location.href='?main_create1';</script>";
		exit(0);
	}
	$scxmA=$cdbB->query("select xm_id from xminfo");
	while($scxmB=$scxmA->fetchArray())
	{
		if(!$cdbB->exec("insert into xmscore values(".$scxmB["xm_id"].",".$pwznum."null,null,null,0,null,null,null)"))
		{
			echo "<script>window.alert(\"系统填写冲突，请重新输入提交7-".$scxmB["xm_id"]."！\");location.href='?main_create1';</script>";
			exit(0);
			break;
		}
	}
	if(0<1)
	{
		echo "<script>window.alert(\"添加成功！\");location.href='index.php';</script>";
		exit(0);
	}
}


//管理用户登录后主页
function userinfo()
{
	echo "<p><button onclick=\"javascript:location.href='?main_create1';\">创建比赛</button></p>";
	//显示比赛信息列表，后面加上xmscore修改各个项目的时间
}

switch($_SERVER["QUERY_STRING"])
{
	case "main_create1":
		create_main_form1();	break;
	case "main_create2":
		create_main_form2();	break;
	case "main_create3":
		create_main_form3();	break;
	case "main_create4":
		create_main_form4();	break;
	case "main_create5":
		create_main_form5();	break;
	default: userinfo(); break;
}
?>
</center>
</body>
</html>