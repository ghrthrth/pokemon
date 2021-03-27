<?php

//Боевой
$api_id = 7632254;
$api_secret = 'gvdyr3rClHSV6S43AFLT';
/*
//Тест
$api_id = ;
$api_secret = '';
*/

//Проверяем на Ауткей
function vk_check_md($id, $auth_key) {
	global $api_id, $api_secret;
 
	$real_md5 = md5("$api_id" . "_" . $id . "_" . "$api_secret");
  
	if ($auth_key!=$real_md5) die("<a href='http://vk.com/love.tree'>Дерево любви</a>");
}


//Проверяем на друга
function vk_check_friends($id, $user_id, $status, $sign) {
	global $api_id, $api_secret;
 
	$real_sign = md5($id . "_" . $user_id . "_" . $status . "_" . $api_secret);
  
	if ($sign != $real_sign) return 0;
	
	return 1;
}



//Посылаем уведомление
function vk_send_notification($user_ids,$message)
{
 global $api_id, $api_secret;

 $message_code=urlencode($message);
  
 $rand=rand(1,300000);
 $time=time();

 $s="api_id=$api_id";
 $s=$s."message=$message";
 $s=$s."method=secure.sendNotification";
 $s=$s."random=$rand";
 $s=$s."timestamp=$time";
 $s=$s."user_ids=$user_ids";
 $s=$s."$api_secret";

 $md=md5("$s");
 
 $otvet=file_get_contents("http://api.vk.com/api.php?api_id=$api_id&message=$message_code&method=secure.sendNotification&random=$rand&sig=$md&timestamp=$time&user_ids=$user_ids");
 
 return $otvet;
}



//Устанавливаем счетчик
function vk_set_counter($id, $count)
{
 global $api_id, $api_secret;

 $rand=rand(1,300000);
 $time=time();
 
 $s="api_id=$api_id";
 $s=$s."counter=$count";
 $s=$s."method=secure.setCounter";
 $s=$s."random=$rand";
 $s=$s."timestamp=$time";
 $s=$s."uid=$id";
 $s=$s."$api_secret";
 
 $md=md5("$s");
 
 $otvet=file_get_contents("http://api.vk.com/api.php?api_id=$api_id&counter=$count&method=secure.setCounter&random=$rand&sig=$md&timestamp=$time&uid=$id");
 
 preg_match("(<response>(.+?)<\/response>)",$otvet,$match);
 return $match[1];  
}



//Устанавливаем уровень
function vk_set_level($id,$level) {
	global $api_id, $api_secret;

	$rand=rand(1,300000);
	$time=time();

	$s="api_id=$api_id";
	$s=$s."level=$level";
	$s=$s."method=secure.setUserLevel";
	$s=$s."random=$rand";
	$s=$s."timestamp=$time";
	$s=$s."uid=$id";
	$s=$s."$api_secret";

	$md=md5("$s");
 
	$otvet=file_get_contents("http://api.vk.com/api.php?api_id=$api_id&level=$level&method=secure.setUserLevel&random=$rand&sig=$md&timestamp=$time&uid=$id");
 
	return $otvet;
}



//Онлайн пользователей
function vk_users_get($uids) {
	global $api_id, $api_secret;

	$rand=rand(1,300000);
	$time=time();
	
	$s="api_id=$api_id";
	
	$s=$s."fields=online";
	$s=$s."method=users.get";
	$s=$s."random=$rand";
	$s=$s."timestamp=$time";
	$s=$s."uids=$uids";
	$s=$s."$api_secret";

	$md=md5("$s");
 
	$otvet=file_get_contents("http://api.vk.com/api.php?api_id=$api_id&fields=online&method=users.get&random=$rand&sig=$md&timestamp=$time&uids=$uids");
 
	return $otvet;
}





//Онлайн пользователей
function vk_is_app_user($user_id) {
	global $api_id, $api_secret;

	$rand=rand(1,300000);
	$time=time();
	
	$s="api_id=$api_id";
	
	$s=$s."method=users.isAppUser";
	$s=$s."random=$rand";
	$s=$s."timestamp=$time";
	$s=$s."user_id=$user_id";
	$s=$s."$api_secret";

	$md=md5("$s");
 
	$otvet=file_get_contents("http://api.vk.com/api.php?api_id=$api_id&method=users.isAppUser&random=$rand&sig=$md&timestamp=$time&user_id=$user_id");
 
	 preg_match("(<response>(.+?)<\/response>)",$otvet,$match);
	return $match[1];  
}



?>