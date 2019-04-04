<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title>比赛结果</title>
<style type="text/css">
body { margin:20px;padding:20px;cursor:default; }
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

$kdb=new database("data/FenDb/".$_COOKIE["lyclub_dafen_gid"].".db");
$xmi=$kdb->query("select * from xmscore");
$pwn=$kdb->query("select * from pwinfo");
/*+++bsinfo++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * bs_gid  + bs_gtitle +  bs_pass   +  bs_gstart   + bs_gend      + bs_ginfo +  bs_cuser/bs_ctime + bs_bakinfo   +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *比赛ID号 + 比赛标题  + 评委验证码 + 比赛开始时间 + 比赛结束时间 + 说明信息 + 创建用户的时间    + 比赛备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

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
$bsi=$kdb->query("select * from bsinfo where bs_gid=".$_COOKIE["lyclub_dafen_gid"]);
$bsf=$bsi->fetchArray();
echo "<lyclub>比赛信息</lyclub>";
echo "<table border='2' cellpadding='0' cellspacing='0'>";
echo "<tr style='background:lightblue;color:blue;'>";
echo "<th>比赛主题</th>";
echo "<th>比赛相关说明</th>";
echo "<th>比赛开始时间</th>";
echo "<th>比赛结束时间</th>";
echo "<th>比赛发布时间</th>";
echo "<th>比赛备注信息</th>";
echo "</tr>";
echo "<tr>";
echo "<th title='比赛代码:".$bsf["bs_gid"]."'>&nbsp;".$bsf["bs_gtitle"]."&nbsp;</th>";
echo "<th title='".$bsf["bs_ginfo"]."'>&nbsp;".substr($bsf["bs_ginfo"],0,60)."...&nbsp;</th>";
echo "<th>&nbsp;".$bsf["bs_gstart"]."&nbsp;</th>";
echo "<th>&nbsp;".$bsf["bs_gend"]."&nbsp;</th>";
echo "<th title='发布者：".$bsf["bs_cuser"]."'>&nbsp;".$bsf["bs_ctime"]."&nbsp;</th>";
echo "<th>&nbsp;".$bsf["bs_bakinfo"]."&nbsp;</th>";
echo "</tr>";
echo "</table>";
/*++++++xmscore++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *  xm_id   +      pwsc1    +      pwsc2    +      pwsc3    +      pwsc4    + sc_str   + sc_max   + sc_min   +  sc_avg  + sc_start + sc_end   + sc_bakinfo  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * 项目ID号 +评委人员表ID=1 +评委人员表ID=2 +评委人员表ID=3 +评委人员表ID=4 +分数字符串+去掉最高分+去掉最低分+最终平均分+打分开始时+打分结束时+打分备注信息 +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
/*+++++pwinfo++++++++++++++++++++++++++++++++++++++++++++
  +  pw_id    +  pw_name  +  pw_job    +  pw_bakinfo    +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++
  + 评委ID号  + 评委姓名  +  评委职位  +  评委备注信息  +
**+++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
echo "<p></p>";
$xmi=$kdb->query("select * from xmscore order by sc_avg desc");
$pwn=$kdb->query("select * from pwinfo");
echo "<lyclub>评分结果</lyclub>";
echo "<table border='2' cellpadding='0' cellspacing='0'>";
echo "<tr style='background:lightblue;color:blue;'>";
echo "<th>最终名次</th>";
echo "<th>项目名称</th>";
echo "<th>最终的得分</th>";
echo "<th>去掉的最高分</th>";
echo "<th>去掉的最低分</th>";
$pwnum=0;
while($pwa=$pwn->fetchArray())
{
	echo "<th>&nbsp;&nbsp;<span title='".$pwa["pw_job"]."' >".$pwa["pw_name"]."</span>&nbsp;&nbsp;</th>";
	$pwnum=$pwnum+1;
}
echo "<th>评分开始时间</th>";
echo "<th>评分结束时间</th>";
echo "<th>&nbsp;&nbsp;备注信息&nbsp;&nbsp;</th>";
echo "</tr>";
$zxmnum=0;
while($xma=$xmi->fetchArray())
{
	echo "<tr>";
	echo "<th>第".($zxmnum=$zxmnum+1)."名</th>";
	$xmn=$kdb->query("select * from xminfo where xm_id=".$xma["xm_id"]);
/*++++++xminfo++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  +  xm_id   +  xm_name   +  xm_title    +  xm_info   +  xm_bakinfo  +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  + 项目ID号 + 项目单位名 + 单位的项目名 + 项目的信息 + 项目备注信息 +
**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	while($xmp=$xmn->fetchArray())
		echo "<th title='[".$xmp["xm_id"]."]\n".$xmp["xm_title"]."\n".$xmp["xm_info"]."'>&nbsp;".$xmp["xm_name"]."&nbsp;</th>";
	echo "<th style='color:red' title='".$xma["sc_str"]."'>&nbsp;&nbsp;".$xma["sc_avg"]."&nbsp;&nbsp;</th>";
	echo "<th style='color:orange;'>&nbsp;&nbsp;".$xma["sc_max"]."&nbsp;&nbsp;</th>";
	echo "<th style='color:orange;'>&nbsp;&nbsp;".$xma["sc_min"]."&nbsp;&nbsp;</th>";
	for($j=1;$j<=$pwnum;$j++)
		echo "<th style='color:lightgray;'>&nbsp;".$xma["pwsc".$j]."&nbsp;</th>";
	echo "<th>&nbsp;&nbsp;".$xma["sc_start"]."&nbsp;&nbsp;</th>";
	echo "<th>&nbsp;&nbsp;".$xma["sc_end"]."&nbsp;&nbsp;</th>";
	echo "<th>&nbsp;&nbsp;</th>";
	echo "</tr>";
}
echo "</table>";
?>
</body>
</html>