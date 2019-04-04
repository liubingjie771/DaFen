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

$dbnum="201903001";
if($_GET["code"]!="")
	$dbnum=$_GET["code"];
$GLOBALS["db"]=new database("data/FenDb/".$dbnum.".db");

$dta=$db->query("select * from xmscore ");

//项目提交打分时间处理
function xm_update_commit()
{
	echo "<script language='javascript'>\n";
	if($_POST["st_d"]==""||$_POST["st_t"]=="")
	{
		echo "window.alert('未完全设置开始时间');\n";
		echo "history.go(-1);\n";
	}
	elseif($_POST["en_d"]==""||$_POST["en_t"]=="")
	{
		echo "window.alert('未完全设置结束时间');\n";
		echo "history.go(-1);\n";
	}
	elseif(strtotime($_POST["st_d"]." ".$_POST["st_t"])>=strtotime($_POST["en_d"]." ".$_POST["en_t"]))
	{
		echo "window.alert('开始时间和结束时间未正确设置！');\n";
		echo "history.go(-1);\n";
	}
	elseif($GLOBALS["db"]->exec("update xmscore set sc_start='".$_POST["st_d"]." ".$_POST["st_t"].":00',sc_end='".$_POST["en_d"]." ".$_POST["en_t"].":00' where xm_id='".$_POST["xm"]."' "))
	{
		echo "window.alert('设定成功');\n";
		echo "location.href='?code=".$_GET["code"]."&fun=xmtime';\n";
	}
	else
	{
		echo "window.alert('数据库读写错误！');\n";
		echo "history.go(-1);\n";
	}
	echo "</script>";
}

//项目提交打分时间表单
function xm_update_time($a)
{
	echo "<lyclub>项目设定打分时间</lyclub>";
	echo "<p style='color:red;font-weight:bold;'>请注意：每项只能设定一次打分时间</p>";
	echo "<form action='?code=".$_GET["code"]."&fun=xmtime2' method='post'>";
	echo "<p>请选择项目：<select id=\"xm\" name=\"xm\">";
	while($b=$a->fetchArray())
	{
		if($b["sc_start"]!="")
			continue;
		elseif($b["sc_end"]!="")
			continue;
		$k=$GLOBALS["db"]->query("select * from xminfo where xm_id=".$b["xm_id"]);
		$j=$k->fetchArray();
		echo "<option value=\"".$j["xm_id"]."\">".$j["xm_id"]."--".$j["xm_name"]."--".$j["xm-title"]."</option>";
	}
	echo "</select></p>";
	echo "<p>开始时间：<input type='date' id='st_d' name='st_d' value='' /><input type='time' id='st_t' name='st_t' value='' /></p>";
	echo "<p>结束时间：<input type='date' id='en_d' name='en_d' value='' /><input type='time' id='en_t' name='en_t' value='' /></p>";
	echo "<p><input type='submit' value='设定' />&nbsp;&nbsp;<input type='reset' value='重置' />&nbsp;&nbsp;<input type='button' value='关闭' onclick='javascript:location.href=\"index.php\";' /></p>";
	echo "</form>";
}

//项目打平均分处理
function xm_update_score2()
{
	if($_POST["xm"]=="")
	{
		echo "<script>window.alert('请重新选择！');history.go(-1);</script>";
		exit(0);
	}
	$pwn=$GLOBALS["db"]->query("select count(pw_id) as znum from pwinfo");
	$pwn=$pwn->fetchArray();
	$scm=$GLOBALS["db"]->query("select * from xmscore where xm_id=".$_POST["xm"]);
	$scn=$scm->fetchArray();
	$sc_str="";
	for($i=1;$i<=$pwn["znum"];$i++)
	{
		$sc_str=$sc_str.$scn["pwsc".$i].",";
	}
	$sc_str=substr($sc_str,0,-1);
	$sc_arr=explode(",",$sc_str);
	sort($sc_arr);
	$sc_min=$sc_arr[0];
	$sc_max=$sc_arr[(count($sc_arr)-1)];
	$sc_arr=array_splice($sc_arr,1,(count($sc_arr)-2));
	$sc_str="";
	$sc_sum=0;
	for($k=0;$k<count($sc_arr);$k++)
	{
		$sc_str=$sc_str.$sc_arr[$k].",";
		$sc_sum=$sc_sum+$sc_arr[$k];
	}
	$sc_str=substr($sc_str,0,-1);
	$sc_avg=$sc_sum/count($sc_arr);
	$sc_sql="update xmscore set sc_str='".$sc_str."',sc_max='".$sc_max."',sc_min='".$sc_min."',sc_avg=".$sc_avg." where xm_id=".$_POST["xm"];
	if($GLOBALS["db"]->exec($sc_sql))
	{
		echo "<script>window.alert('打分成功！');location.href='?code=".$_GET["code"]."&fun=xmscore';</script>";
	}
	else
	{
		echo "<script>window.alert('打分错误！请重试');history.go(-1);</script>";
	}
}
/*++++++xmscore++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *  xm_id   +      pwsc1    +      pwsc2    +      pwsc3    +      pwsc4    + sc_str   + sc_max   + sc_min   +  sc_avg  + sc_start + sc_end   + sc_bakinfo  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * 项目ID号 +评委人员表ID=1 +评委人员表ID=2 +评委人员表ID=3 +评委人员表ID=4 +分数字符串+去掉最高分+去掉最低分+最终平均分+打分开始时+打分结束时+打分备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/


//项目打平均分表单
function xm_update_score($a)
{
	echo "<lyclub>项目设定总分</lyclub>";
	echo "<p style='color:red;font-weight:bold;'>请注意：每项只能设定一次打平均分</p>";
	echo "<form action='?code=".$_GET["code"]."&fun=xmscore2' method='post'>";
	echo "<p>请选择项目：<select id=\"xm\" name=\"xm\">";
	while($b=$a->fetchArray())
	{
		if($b["sc_str"]!="")
			continue;
		$k=$GLOBALS["db"]->query("select * from xminfo where xm_id=".$b["xm_id"]);
		$jk=$k->fetchArray();
		echo "<option value=\"".$jk["xm_id"]."\">".$jk["xm_id"]."--".$jk["xm_name"]."</option>";
	}
	echo "</select></p>";
	echo "<p><input type='submit' value='打平均分' />&nbsp;&nbsp;<input type='button' value='关闭' onclick='javascript:location.href=\"index.php\";' /></p>";
	echo "</form>";
}

switch($_GET["fun"])
{
	case "xmtime" :
		xm_update_time($dta); break;
	case "xmtime2" :
		xm_update_commit(); break;
	case "xmscore" :
		xm_update_score($dta); break;
	case "xmscore2" :
		xm_update_score2(); break;
	default:
		break;
}
?>
</center>
</body>
</html>