<?php
$hasError = false;

$data = [
			'name' => 'ken',
			'dob'  => '09-04-1986',
			'add'  => trim(htmlspecialchars(' address ')),
			'phone' => '0349981108',
			'email' => 'dall@hotmail.com',
			'message' => trim(htmlspecialchars(' hello '))
];

foreach ($data as $key => $value) {
	switch ($key) {
		case 'name':
			if (empty($value)||!preg_match("/^[a-zA-Z ]{2,}$/",$value)) {
				echo '1';
				$hasError = true;
			}
			break;
		case 'dob':
			if (empty($value)||!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$value)) {
				echo '2';
				$hasError = true;
			}
			break;
		case 'add':
			if (empty($value)) {
				echo '3';
				$hasError = true;
			}
			break;
		case 'phone':
			if (empty($value)||!preg_match("/^0[0-9]\s?\d{4}\s?\d{4}$/",$value)) {
				echo '4';
				$hasError = true;
			}
			break;
		case 'email':
			if (empty($value)||!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				echo '5';
				$hasError = true;
			}
			break;
		case 'message':
			if (empty($value)) {
				echo '6';
				$hasError = true;
			}
			break;
		
		default:
			echo '7';
			$hasError = true;
			break;
	}
}

if ($hasError === false) {
	try{
		$handle = new PDO('mysql:host=localhost;dbname=demo_data','dallas','198649');
		//echo 'connect to database.<br>';
	}
	catch(PDOException $e){
		echo 'database fail to connect :'.$e->getMessage();
	}

	$query = 'INSERT INTO form (name, dob, rtime, phone, email, address, message) VALUES (?,?,?,?,?,?,?)';
	echo $query;
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

echo $hasError;
?>
