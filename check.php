<?php

mb_internal_encoding("UTF-8");
set_time_limit(60);
define("CALL_SLEEP",1);

$debug = 0;
$isupdate=isset($_GET["s"]);

if (isset($_SERVER['HTTP_CLIENT_IP']))
    $ip = $_SERVER['HTTP_CLIENT_IP'];
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
else
    $ip = $_SERVER['REMOTE_ADDR'];


$link = mysqli_connect('localhost', 'user', 'pass', "db") or die($debug?mysqli_connect_error():"0");
$link->set_charset("utf8");

$ipE = explode('.',$ip);
$ipE = sprintf("%02X%02X%02X%02X",intval($ipE[0]),intval($ipE[1]),intval($ipE[2]),intval($ipE[3]));

$link->query("update LOW_PRIORITY ignore protest set lastvisit=now() where ip='$ipE' limit 1") or die($debug?$link->error:"0");
if (!$isupdate) {
  if ($link->affected_rows<1) {
    $country = getCN($ip);
    if ($country) {
      $link->query("insert LOW_PRIORITY ignore into protest (ip,country) value ('$ipE','$country')") or die($debug?$link->error:"0");
      echo "$country,me\n";
    }
  } else {
    $result = $link->query("SELECT country FROM protest where ip='$ipE' limit 1") or die($debug?$link->error:"0");
    if ($result->num_rows>0) { 
      $row = $result->fetch_array(MYSQLI_NUM);
      echo $row[0].",me\n";
    }
  }

  $link->query("insert LOW_PRIORITY ignore into protest_stats (num,numall) value ((select count(*) from protest where lastvisit>now()-interval 10 minute),(select count(*) from protest))");
}

$result = $link->query("SELECT country,count(ip) FROM protest where lastvisit>now()-interval 10 minute group by country order by country") or die($debug?$link->error:"0");
if ($result->num_rows>0) { 
  while ($row = $result->fetch_array(MYSQLI_NUM))
    echo implode(",",$row)."\n";
}


function getCN($ip) {
  $data = file_get_contents("http://freegeoip.net/csv/$ip");
  if ($data) {
    $data = explode(",",$data);
    if (count($data)>=2)
      return substr(strtolower($data[1]),0,2);
  }
  return false;
}
?>
