<?php

header("Content-Type: application/json; encoding=utf-8");

include("mainfile.php");
include("vkontakte.php");

global $db;

$time = time();

//Проверим подпись
$input = $_POST;
$sig = $input['sig'];
unset($input['sig']);
ksort($input);

//Реальная подпись
$signature = '';

foreach ($input as $k => $v) {
  $signature .= $k . '=' . $v;
}

//Если подпись не верна
if ($sig != md5($signature.$api_secret)) {
  $response['error'] = array(
    'error_code' => 10,
    'error_msg' => 'Несовпадение вычисленной и переданной подписи запроса.',
    'critical' => true
  );
} 
else {
	//Информация о товаре
	if ($notification_type == 'get_item' || $notification_type == 'get_item_test') {
		//Обычные товары
		switch ($item) {
			case 'item1':
				$response['response'] = array(
					'title' => '50 монет',
					'photo_url' => 'http://dragongrow.ru/images/vk/money.png',
					'price' => 1
				);
			break;

			case 'item2':
				$response['response'] = array(
					'title' => '252 монеты',
					'photo_url' => 'http://dragongrow.ru/images/vk/money.png',
					'price' => 5
				);
			break;
			
			case 'item3':
				$response['response'] = array(
					'title' => '505 монет',
					'photo_url' => 'http://dragongrow.ru/images/vk/money.png',
					'price' => 10
				);
			break;
			
			case 'item4':
				$response['response'] = array(
					'title' => '1011 монет',
					'photo_url' => 'http://dragongrow.ru/images/vk/money.png',
					'price' => 20
				);
			break;
			
			case 'item5':
				$response['response'] = array(
					'title' => '2530 монет',
					'photo_url' => 'http://dragongrow.ru/images/vk/money.png',
					'price' => 50
				);
			break;
			
			case 'item6':
				$response['response'] = array(
					'title' => '5075 монет',
					'photo_url' => 'http://dragongrow.ru/images/vk/money.png',
					'price' => 100
				);
			break;
			
			case 'item7':
				$response['response'] = array(
					'title' => '15500 монет',
					'photo_url' => 'http://dragongrow.ru/images/vk/money.png',
					'price' => 300
				);
			break;
			

			
			case 'vip':
				//Узнаем цену ВИПа
				$re=$db->sql_fetchrow($db->sql_query("select * from systems where id = 5"));
				
				//Цена
				$votes = intval($re['votes']) + 1;
			
				//Если прошло 24 часа
				if ($re['timestamp'] + 24*60*60 < $time) $votes = 1;
			
				//Ответ
				$response['response'] = array(
					'title' => 'Стать VIP',
					'photo_url' => 'http://dragongrow.ru/images/vk/vip.png',
					'price' => $votes
				);
			break;

			
			case 'energy':
				$response['response'] = array(
					'title' => 'Полное восстановление энергии',
					'photo_url' => 'http://dragongrow.ru/images/vk/energy.png',
					'price' => 10
				);
			break;
			
			
			default:
				$response['error'] = array(
					'error_code' => 20,
					'error_msg' => 'Товара не существует.',
					'critical' => true
				);
			break;
		}
	}

	if ($notification_type == 'order_status_change' || $notification_type == 'order_status_change_test') {
		if ($status == 'chargeable') {
			//Добавляем в БД заказ
			$check = $db->sql_affectedrows($db->sql_query("insert into payments values('$order_id','$user_id','$receiver_id','$item','$item_price','$time')"));

			//$order_id - идентификатор заказа 
			//$user_id - отправитель заказа
			//$receiver_id - получатель заказа
			
			//Если покупка произошла, покупаем товар
			if ($check > 0) {
				switch ($item) {
				
					//Монеты
					case 'item1':
						$db->sql_query("update users set money=money+50 where uid=$receiver_id");
					break;
					
					case 'item2':
						$db->sql_query("update users set money=money+252 where uid=$receiver_id");
					break;
					
					case 'item3':
						$db->sql_query("update users set money=money+505 where uid=$receiver_id");
					break;
					
					case 'item4':
						$db->sql_query("update users set money=money+1011 where uid=$receiver_id");
					break;
					
					case 'item5':
						$db->sql_query("update users set money=money+2530 where uid=$receiver_id");
					break;
					
					case 'item6':
						$db->sql_query("update users set money=money+5075 where uid=$receiver_id");
					break;
					
					case 'item7':
						$db->sql_query("update users set money=money+15500 where uid=$receiver_id");
					break;
					
		
					//Остальное
					case 'vip':
						$db->sql_query("update systems set value=$receiver_id,votes=if(timestamp+24*60*60<$time,1,votes+1),timestamp=$time where id=5");
					break;
					
					case 'energy':
						$db->sql_query("update users set energy=22+3*floor(1 + log(2, exp/10+1)) where uid=$receiver_id");
					break;

				}
				
				//Если это оффер
				if (strripos($item, "offer_") === false) {
				} else {					
					$db->sql_query("update users set money=money+$item_price*50 where uid=$receiver_id");
				}
			}
			
			$response['response'] = array(
				'order_id' => $order_id
			);
		} 
		else {
			$response['error'] = array(
				'error_code' => 100,
				'error_msg' => 'Передано непонятно что вместо chargeable.',
				'critical' => true
			);
		}
	}
}

echo json_encode($response);

?>