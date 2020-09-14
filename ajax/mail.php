<?php 
header('Content-Type: application/json');
require_once('phpmailer/PHPMailerAutoload.php');
$mail = new PHPMailer;
$mail->CharSet = 'utf-8';
$mail->SMTPDebug = false;   

function getIp() {
  $keys = [
    'HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'REMOTE_ADDR',
  ];
  foreach ($keys as $key) {
    if (!empty($_SERVER[$key])) {
      $ip = trim(end(explode(',', $_SERVER[$key])));
      if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
      }
    }
  }
}

$ip = getIp();  
require_once 'SxGeo.php';
// подключаем файл с базой данных городов
$SxGeo = new SxGeo('SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY);
$city = $SxGeo->get($ip);

// широта
$lat = $city['city']['lat'];
// долгота
$lon = $city['city']['lon'];
// название города на русском языке
$city_name_ru = $city['city']['name_ru'];
// название города на английском языке
$city_name_en = $city['city']['name_en'];
// ISO-код страны
$country_code = $city['country']['iso'];

// для получения информации более полной информации (включая регион) можно осуществить через метод getCityFull
$city = $SxGeo->getCityFull($ip);
// название региона на русском языке
$region_name_ru = $city['region']['name_ru'];
// название региона на английском языке
$region_name_en = $city['city']['name_en'];
// ISO-код региона
$region_name_iso = $city['city']['iso'];                               // Enable verbose debug output


  
$today = date("d.m.y");

if(isset($_REQUEST['phone'])){
	$phone = $_REQUEST['phone'];
} elseif (isset($_REQUEST['Phone'])) {
	$phone = $_REQUEST['Phone'];
}
if(isset($_REQUEST['email'])){
	$email = $_REQUEST['email'];
} elseif (isset($_REQUEST['Email'])) {
	$email = $_REQUEST['Email'];
}
if(isset($_REQUEST['name'])){
	$name = $_REQUEST['name'];
} 
if(isset($_REQUEST['text'])){
	$text = $_REQUEST['text'];
} 
if($phone){
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.yandex.ru';  																							// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'site-noreply@amulex.ru'; // Ваш логин от почты с которой будут отправляться письма
	$mail->Password = 'BCBD86jhgVqzqrDjQK7v'; // Ваш пароль от почты с которой будут отправляться письма
	$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465; // TCP port to connect to / этот порт может отличаться у других провайдеров
	$mail->setFrom('site-noreply@amulex.ru'); // от кого будет уходить письмо?
	$mail->addAddress('nalog-call@amulex.ru'); 
	$mail->addAddress('amulexdata@gmail.com'); 
	$mail->addAddress('asamoylo@amulex.ru'); 

	//amulexdata@gmail.com
	$cli = '';
	
	$cookieNamePrefix = "_lnd_";
	$utmParams = ["utm_source", "utm_medium","utm_campaign","utm_term","utm_content", "client_id"];
	$utm_str = '';
	foreach ($utmParams as $key => $value) {
		$utm = $cookieNamePrefix.$value;
		if($_COOKIE[$utm]){
				$utm_str.= $value.": ".$_COOKIE[$utm]."<br>\n";
				if($value == 'client_id'){
					$cli = $_COOKIE[$utm];
				}
		}
	}
	
	


	$mail->isHTML(true); 
	$mail->Subject = "Лендинг Разводы - запрос консультации"; // Заголовок письма
	$mail->Body = "Телефон - ".$phone."\r\n<br>email - ".$email."\r\n<br>Имя - ".$name."\r\n<br><br><br><br>----- UTM метки ------<br>Наименование услуги: Бесплатная консультация <br>Город: ".$city_name_ru."<br>Страница с отправки: $href<br> ".$utm_str; // Текст письма// Результат


	

	//---- API eof ---
	if($mail->send() ) {
	 //echo 'Message could not be sent.';
	 //echo 'Mailer Error: ' . var_dump($mail->ErrorInfo);
		$data = [ 'success' => 'Ваша заявка отправлена '];
		echo json_encode($data);
	} else {
		
		$data = [ 'error' => 'Что-то пошло не так'];
		echo json_encode($data);
	}
	
} else {
	$data = [ 'error' => 'Что-то пошло не так'];
		echo json_encode($data);
}



