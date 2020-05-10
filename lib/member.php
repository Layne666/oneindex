<?php

class member{

public $userid;

public static function is_login()
{
	//$_SESSION["userid"]=1;
if ($_SESSION["userid"] >= "0"){

    echo "用户在线";
    echo $_SESSION["userid"];
}


}










}