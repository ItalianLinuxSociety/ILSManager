<?php

$UNIXDIR=array(
  "etc"=>"../../etc/",
  "php"=>"../../php/"
);

include $UNIXDIR["php"]."main.php";

if ($_POST ['secret'] == $API ['key']) {
  my_connect();
  sociils_nuovadomanda3 ();
}

