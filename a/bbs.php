<?php

$charset='Shift_JIS';   // UTF-8/Shift_JIS/ISO-2022-JP/EUC-JP/
$title='ＰＨＰ掲示板';  // 画面のタイトル
$viw=5;                 // １画面に表示するコメント数
//$admin='aDmin99,管理人';// 管理人の投稿を先頭に表示する（投稿名,表示名）

/*************************************************************************
  日本語文字セットは、UTF-8、Shift_JIS、ISO-2022-JP、EUC-JP が使えます。
このファイルは Shift_JIS で書かれていますから、他の文字セットを使う場合は
その文字セットで保存し直してください。
  Webサーバに新しいフォルダを作るか、既存の適当なフォルダにこのファイルを
アップロードしてブラウザからこのファイルを呼び出します。
そうすると、bbs.html というファイルが作成されます。次回からはこの bbs.html
をリクエストすると投稿できるようになります。
ご質問があるときは、http://www.bellcall.co.jp/supportboard/ へどうぞ！
**************************************************************************/
mb_language("Japanese");
mb_internal_encoding($charset);
if(preg_match('/shift|sjis/i',$charset))$charcode='sjis';
if(preg_match('/euc/i',$charset))$charcode='eucjp';
if(preg_match('/^jis|2022/i',$charset))$charcode='jis';
if(preg_match('/utf/i',$charset))$charcode='utf8';
if(get_magic_quotes_gpc()){$process=array(&$_GET,&$_POST,&$_COOKIE,&$_REQUEST);
while(list($key,$val)=each($process)){
foreach($val as $k=>$v){unset($process[$key][$k]);
if(is_array($v)){$process[$key][stripslashes($k)]=$v;
$process[]=&$process[$key][stripslashes($k)];}
else $process[$key][stripslashes($k)]=stripslashes($v);}}
unset($process);}
$form=<<<HTM
<html>
<head>
<meta name="Cache-control" content="No-cache">
<meta name="Cache-control" content="Must-revalidate">
<meta http-equiv="Content-Type" content="text/html; charset=$charset">
<style type="text/css">
body{background:#ffffef;}
span.n{color:#0000ff;}
span.d{font-size:12px;}
span.m{margin-left:28px;}
a{text-decoration:none;}
</style>
<script type="text/javascript"><!--
function check(form){
  function id(id){return(document.getElementById(id));}
  if(form.name.value==""||form.msg.value==""){
    id('dsp').innerHTML="<br>&#x5165;&#x529B;&#x3082;&#x308C;&#x304C;&#x3042;&#x308A;&#x307E;&#x3059;&#x3002;<br>";
    id('dsp').style.color="#ff0000";
    return false;
  }else return true;
}
--></script>
<title>$title</title>
</head>
<body>
<form action="bbs.php" method="POST" onsubmit="return check(this)">
<fieldset>
<h3>$title</h3>
<label>&#x304A;&#x540D;&#x524D;</label><br>
<input type="text" name="name"><br>
<label>&#x30B3;&#x30E1;&#x30F3;&#x30C8;</label><br>
<textarea name="msg"></textarea><br>
<input type="submit" name="submit" value="&#x6295;&#x7A3F;&#x3059;&#x308B;">
<span id="dsp"></span>
</form>
</fieldset>
<a href="http://webmastertool.jp/"><div style="text-align:right;font-size:14px;text-decoration:none">(c) WebmasterTOOL.JP&nbsp;&nbsp;</div></a>
HTM;
$file=$htmlfile='bbs.html';
if(!file_exists($file))file_put_contents($file,"$form</body></html>");
header("location: ./$file");
header("Content-type: text/html; charset=$charset");
$name=$_POST['name'];
$msg=$_POST['msg'];
if(!preg_match("/[\e\200-\377]/",$msg))$msg="";
if($name&&$msg){
  $pre=array('&','<','>');
  $aft=array('&amp;','&lt;','&gt;');
  if($charcode!='jis')$name=str_replace($pre,$aft,$name);
  else{$name=preg_replace("/(\x21\x21)+/","\x1B\x28\x42\x20\x1B\x24\x42",$name);
  $name=preg_replace("/\x1B\x28\x42\x1B\x24(\x42|\x4A)/","",$name);}
  $name=preg_replace("/(\x81\x40|\xA1\xA1|\xE3\x80\x80)+/","\x20",$name);
  $name=trim($name);
  $name=preg_replace("/(\x20)+/","\x20",$name);
  if($charcode!='jis')$msg=str_replace($pre,$aft,$msg);
  else{$msg=preg_replace("/(\x21\x21)+/","\x1B\x28\x42\x20\x1B\x24\x42",$msg);
  $msg=preg_replace("/\x1B\x28\x42\x1B\x24(\x42|\x4A)/","",$msg);}
  $msg=preg_replace("/(\x81\x40|\xA1\xA1|\xE3\x80\x80)+/","\x20",$msg);
  $msg=trim($msg);
  $msg=preg_replace("/(\x0D\x0A|\x0D)+/","\x0A",$msg);
  $msg=preg_replace("/(\x0A)+(\x20)+/","\x0A",$msg);
  $msg=preg_replace("/(\x20)+(\x0A)+/","\x0A",$msg);
  $msg=preg_replace("/(\x20)+/","\x20",$msg);
  $msg=preg_replace("/(\x0A)+/","<br>",$msg);
  $w=array('&#x65E5;','&#x6708;','&#x706B;','&#x6C34;','&#x6728;','&#x91D1;','&#x571F;');
  $date=date('Y/m/d(').$w[date('w')].date(') H:i:s');
  if(preg_match("/$name,(.+)/",$admin,$x)){$name=$x[1];file_put_contents("admin.msg","$date<>$name<>$msg\n");}else{file_put_contents("bbs.log","$date<>$name<>$msg\n",FILE_APPEND);}
}
$log=file("bbs.log",FILE_SKIP_EMPTY_LINES);
if(file_exists("admin.msg"))array_push($log,file_get_contents("admin.msg"));
if(count($log)>$viw){
  $p=floor(count($log)/$viw);
  if(fmod(count($log),$viw))$p+=1;
  $pg=1;
  $s=count($log);
  $l=$s-$viw;
  $index="<a href=\"bbs.html\">[&#x65B0;&#x7740;{$viw}&#x4EF6;]</a> ";
  for($i=2;$i<=$p;$i++){
    $s-=$viw;$l=$i==$p?1:$s-$viw+1;
    $index.="<a href=\"bbs_$i.html\">[$s-$l]</a> ";
  }
}
krsort($log);
foreach($log as $lognum=>$logval){
  $lognum+=1;
  $num++;
  $data=explode('<>',$logval);
  $vline.="<p>".$lognum."&#xFF1A; <span class='n'>$data[1]</span>&nbsp;&nbsp;<span class='d'>$data[0]</span><br><span class='m'>$data[2]</span></p>\n";
  if(!($num%$viw)||$num==count($log)){
    $index2=preg_replace("/<a href=\"$file\">(\[.+?\])<\/a>/","<b>$1</b>",$index);
    $vline.="<hr>
$index2
</body>
</html>";
    file_put_contents($file,$form.$vline);
    if($pg==$p)break;
    $pg++;
    $file=preg_replace("/(.+)\.(.+)/","$1_$pg.$2",$htmlfile);
    $vline="";
  }
}
exit();
/* 1997-2011 (c)Telecom Corporation */
?>