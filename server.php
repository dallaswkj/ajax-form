<?php
//error flag
$hasError = false;

//recaptcha info
//extension=php_openssl.dll
//allow_url_fopen = On
//$secrect = '6LdmfwkTAAAAANbui15iRBJqkWMszPJTpTowamXU';
$secrect = '6LfOjgkTAAAAACG9hzFvHFbajC7jbwKl6-Yy7RKT';
$rsp = $_POST['g-recaptcha-response'];
$ip = $_SERVER['REMOTE_ADDR'];
$url="https://www.google.com/recaptcha/api/siteverify?secret={$secrect}&response={$rsp}";
//check recaptcha return json
//{
//  "success": true|false,
//  "error-codes": [...]   // optional
//}
$res = json_decode(file_get_contents($url));
if ($res->success) {

	$data = [
				'name' => $_POST['name'],
				'dob'  => $_POST['dob'],
				'add'  => trim(htmlspecialchars($_POST['add'], ENT_QUOTES )),
				'phone' => $_POST['phone'],
				'email' => $_POST['email'],
				'message' => trim(htmlspecialchars($_POST['message'], ENT_QUOTES ))
	];

	//PHP side data validation
	foreach ($data as $key => $value) {
		switch ($key) {
			case 'name':
				if (empty($value)||!preg_match("/^[a-zA-Z ]{2,}$/",$value)) {
					$hasError = true;
				}
				break;
			case 'dob':
				if (empty($value)||!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$value)) {
					$hasError = true;
				}
				break;
			case 'add':
				if (empty($value)) {
					$hasError = true;
				}
				break;
			case 'phone':
				if (empty($value)||!preg_match("/^0[0-9]\s?\d{4}\s?\d{4}$/",$value)) {
					$hasError = true;
				}
				break;
			case 'email':
				if (empty($value)|| !filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$hasError = true;
				}
				break;
			case 'message':
				if (empty($value)) {
					$hasError = true;
				}
				break;
			
			default:
				$hasError = true;
				break;
		}
	}
}
else{
	//if not pass google captcha return error msg
	$hasError = true;
}

//write into database
//------------------------------------
//CREATE TABLE IF Not EXISTS form (
//	ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
//	name VARCHAR(50) NOT NULL DEFAULT '',
//	dob DATE NOT NULL,
//	rtime INT NOT NULL,
//	phone VARCHAR(10) NOT NULL,
//	email VARCHAR(50) NOT NULL,
//	address VARCHAR(100) NOT NULL DEFAULT '',
//	message TEXT NOT NULL DEFAULT '',
//	PRIMARY KEY(ID),
//	INDEX(name,email,phone)
//	)ENGINE=innoDB;
//------------------------------------

if ($hasError !== true) {
	try{
		$handle = new PDO('mysql:host=localhost;dbname=demo_data','dallas','198649');
		//echo 'connect to database.<br>';
	}
	catch(PDOException $e){
		echo 'database fail to connect :'.$e->getMessage();
	}

	$query = 'INSERT INTO form (name, dob, rtime, phone, email, address, message) VALUES (?,?,?,?,?,?,?)';
	//echo $query;
	$stm = $handle->prepare($query);

	//format date
	list($d,$m,$y) = explode('-', $data['dob']);
	$data['dob'] = "{$y}-{$m}-{$d}";
	//echo $data['dob'];

	//format phone number
	$data['phone'] = str_replace(' ', '', $data['phone']);
	//echo $data['phone'];

	//execute query
	$stm->execute(array($data['name'], $data['dob'], time(), $data['phone'], $data['email'], $data['add'], $data['message']));

	if ($stm->rowCount()>0) {
		$hasError = false;
	}
	
}



//return json to front-end
header('Content-Type: application/json');
echo json_encode($hasError);
?>