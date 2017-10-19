<?php
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	require '../vendor/autoload.php';

	require '../src/config/db.php';

	require '../src/routes/dados.php';

	function getConnection(){
		return new PDO("mysql:host=localhost;dbname=moppe_leituras", "root", "root"); 
	}

	function sendMessage($message){
		$content = array(
			"en" => $message
			);
		
		$fields = array(
			'app_id' => "42023282-35cc-4192-a4aa-5956dd9e3602",
			'included_segments' => array('All'),
            'data' => array("foo" => "bar"),
			'contents' => $content
		);
		
		$fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic OGM1ZTM1MzYtMjRlMy00NDdjLWJlMWEtOTc0ODg4ZmIxZTVi'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
        print("\ntruta\n");
        
		return $response;
	}

	$app->get('/', function ($request, $response, $args) {
	
	echo '
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8">
			<meta name="description" content="API que transmite dados sobre os niveis de um rio">
			<meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
			<meta name="author" content="Edson Boldrini">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Moppe - Webservice</title>
			<link rel="stylesheet" type="text/css" href="../style.css">
		</head>
		<body>';

	echo '
		<h2>Moppe - Webservice</h2>
		<h3>Digite:</h3> 
		<ul style="list-style-type:disc">
			<li>/get_dados na url para acessar os dados do banco.</li> <br>
			<li>/get_insere/{id_dispositivo}/{valor_icos_fundo}/{valor_icos_superficie}/{valor_ultrassonico}/{latitude_sinal}/{latitude_inteiro}/{latitude_decimal}/{longitude_sinal}/{longitude_inteiro}/{longitude_decimal}/{elevacao}/{dia}/{mes}/{ano}/{hora}/{minuto}/{segundo} na url para inserir uma nova leitura no banco. Lembre de trocar os campos com {} para as variáveis que vc quer.</li>
			<ul>
				<li> ex: /get_insere/1/2/3/4/5/6/7/8/9/1/11/12/0/14/15/16/50/10/10/2017/22/22/22. </li>
				<li> gera: <br>
				id_dispositivo: 1 <br>
				temp_interna: 2 <br>
				temp_externa: 3 <br>
				acx: 4 <br>
				acy: 5 <br>
				acz: 6 <br>
				gyx: 7 <br>
				gyy: 8 <br>
				gyz: 9 <br>
				latitude: -11.12 <br>
				longitude: 14.15 <br>
				elevacao: 16 <br>
				velocidade: 50 <br>
				data_hora: 2017-10-10 22:22:22 </li>
			</ul>	
		</ul>';
		
	echo '
		<br>
		</body>
		</html>';

	});

	$app->get('/get_dados', function($request, $response, $args){
		
		header("Refresh: 5");

		echo '<!DOCTYPE html>
			<html>
			<head>
				<meta charset="UTF-8">
				<meta name="description" content="API que transmite dados sobre os niveis de um rio">
				<meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
				<meta name="author" content="Edson Boldrini">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Moppe - Leituras</title>
				<link rel="stylesheet" type="text/css" href="../style.css">
		  	</head>
			<body>';
		
		echo "<h2>Moppe - Leituras</h2>";

		echo "<h3>Dispositivo 1:</h3>";

		$response = getConnection();
		
		$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo = 1 ORDER BY id_leitura desc limit 10");

		echo '
			<table>
			<tr>
				<th>id_leitura</th>
				<th>Id_Dispositivo</th> 
				<th>Icos Fundo</th>
				<th>Icos Superfície</th>
				<th>Ultrassonico</th>
				<th>Latitude</th>
				<th>Longitude</th>
				<th>Elevação</th>
				<th>Data/Hora</th>
			</tr>';

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					echo "<tr>
							<td>$row->id_leitura</td>
							<td>$row->id_dispositivo</td> 
							<td>$row->valor_icos_fundo</td>
							<td>$row->valor_icos_superficie</td>
							<td>$row->valor_ultrassonico</td>
							<td>$row->latitude</td>
							<td>$row->longitude</td>
							<td>$row->elevacao</td>
							<td>$row->data_hora</td>
				  		 </tr>";	   
				}
			}
		}

		echo "<h3>Dispositivo 2:</h3>";

		$response = getConnection();
		
		$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo = 2 ORDER BY id_leitura desc limit 10");

		echo "
			<table>
			<tr>
				<th>id_leitura</th>
				<th>Id_Dispositivo</th> 
				<th>Icos Fundo</th>
				<th>Icos Superfície</th>
				<th>Ultrassonico</th>
				<th>Latitude</th>
				<th>Longitude</th>
				<th>Elevação</th>
				<th>Data/Hora</th>
			</tr>";

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					echo "<tr>
							<td>$row->id_leitura</td>
							<td>$row->id_dispositivo</td> 
							<td>$row->valor_icos_fundo</td>
							<td>$row->valor_icos_superficie</td>
							<td>$row->valor_ultrassonico</td>
							<td>$row->latitude</td>
							<td>$row->longitude</td>
							<td>$row->elevacao</td>
							<td>$row->data_hora</td>
				  		 </tr>";	   
				}
			}
		}
		
		echo '
			<br>
			</body>
			</html>';
		
		/*
		$response = sendMessage('Dados');
		$return["allresponses"] = $response;
		$return = json_encode( $return);
		
		print("\n\nJSON received:\n");
		print($return);
		print("\n");
		*/
	});

	$app->get('/get_insere/{id_dispositivo}/{valor_icos_fundo}/{valor_icos_superficie}/{valor_ultrassonico}/{latitude_sinal}/{latitude_inteiro}/{latitude_decimal}/{longitude_sinal}/{longitude_inteiro}/{longitude_decimal}/{elevacao}/{dia}/{mes}/{ano}/{hora}/{minuto}/{segundo}', function($request, $response, $args){

		echo '
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset="UTF-8">
				<meta name="description" content="API que transmite dados sobre os niveis de um rio">
				<meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
				<meta name="author" content="Edson Boldrini">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Moppe - Inserção</title>
				<link rel="stylesheet" type="text/css" href="../style.css">
			</head>
			<body>';

		$id_dispositivo = 			$request->getAttribute('id_dispositivo');
		$valor_icos_fundo = 		$request->getAttribute('valor_icos_fundo');
		$valor_icos_superficie = 	$request->getAttribute('valor_icos_superficie');
		$valor_ultrassonico = 		$request->getAttribute('valor_ultrassonico');
		$latitude_sinal = 			$request->getAttribute('latitude_sinal');
		$latitude_inteiro = 		$request->getAttribute('latitude_inteiro');
		$latitude_decimal = 		$request->getAttribute('latitude_decimal');
		$longitude_sinal = 			$request->getAttribute('longitude_sinal');
		$longitude_inteiro = 		$request->getAttribute('longitude_inteiro');
		$longitude_decimal = 		$request->getAttribute('longitude_decimal');
		$elevacao = 				$request->getAttribute('elevacao');
		$dia = 						$request->getAttribute('dia');
		$mes = 						$request->getAttribute('mes');
		$ano = 						$request->getAttribute('ano');
		$hora = 					$request->getAttribute('hora');
		$minuto = 					$request->getAttribute('minuto');
		$segundo = 					$request->getAttribute('segundo');

		if ($latitude_sinal == 1){
			$latitude= "-$latitude_inteiro.$latitude_decimal";	
		}
		else{
			$latitude= "$latitude_inteiro.$latitude_decimal";
		}

		if ($longitude_sinal == 1){
			$longitude= "-$longitude_inteiro.$longitude_decimal";
		}
		else{
			$longitude= "$longitude_inteiro.$longitude_decimal";
		}

		$data_hora = "$dia-$mes-$ano $hora:$minuto:$segundo";

		echo '<h2>Nova leitura:</h2>';

		echo "id_dispositivo: $id_dispositivo<br>";
		echo "valor_icos_fundo: $valor_icos_fundo<br>";
		echo "valor_icos_superficie: $valor_icos_superficie<br>";
		echo "valor_ultrassonico$valor_ultrassonico<br>";
		echo "latitude: $latitude<br>";
		echo "longitude: $longitude<br>";
		echo "elevacao: $elevacao<br>";
		echo "data_hora: $data_hora<br><br>";

		
		echo "INSERT INTO leituras (id_dispositivo, valor_icos_fundo, valor_icos_superficie, valor_ultrassonico, latitude, longitude, elevacao, data_hora) VALUES ('$id_dispositivo','$valor_icos_fundo','$valor_icos_superficie','$valor_ultrassonico','$latitude','$longitude','$elevacao','$data_hora');";
		
		$response = getConnection();
		
		$response = $response->query("INSERT INTO leituras (id_dispositivo, valor_icos_fundo, valor_icos_superficie, valor_ultrassonico, latitude, longitude, elevacao, data_hora) VALUES ('$id_dispositivo','$valor_icos_fundo','$valor_icos_superficie','$valor_ultrassonico','$latitude','$longitude','$elevacao','$data_hora');");

		echo '<br>';

		$response = sendMessage('Nova leitura adicionada');
		$return["allresponses"] = $response;
		$return = json_encode( $return);
		
		print("\n\nJSON received:\n");
		print($return);
		print("\n");
	});

	$app->run();

	/*
	$app->post('/post_insere', function($request, $response, $args){
		$id_dispositivo = 			$request->getParam('id_dispositivo');
		$valor_icos_fundo = 		$request->getParam('valor_icos_fundo');
		$valor_icos_superficie = 	$request->getParam('valor_icos_superficie');
		$valor_ultrassonico = 		$request->getParam('valor_ultrassonico');
		$latitude_sinal = 			$request->getParam('latitude_sinal');
		$latitude_inteiro = 		$request->getParam('latitude_inteiro');
		$latitude_decimal = 		$request->getParam('latitude_decimal');
		$longitude_sinal = 			$request->getParam('longitude_sinal');
		$longitude_inteiro = 		$request->getParam('longitude_inteiro');
		$longitude_decimal = 		$request->getParam('longitude_decimal');
		$elevacao = 				$request->getParam('elevacao');
		$dia = 						$request->getParam('dia');
		$mes = 						$request->getParam('mes');
		$ano = 						$request->getParam('ano');
		$hora = 					$request->getParam('hora');
		$minuto = 					$request->getParam('minuto');
		$segundo = 					$request->getParam('segundo');

		if ($latitude_sinal == 1){
			$latitude= "-$latitude_inteiro.$latitude_decimal";	
		}
		else{
			$latitude= "$latitude_inteiro.$latitude_decimal";
		}

		if ($longitude_sinal == 1){
			$longitude= "-$longitude_inteiro.$longitude_decimal";
		}
		else{
			$longitude= "$longitude_inteiro.$longitude_decimal";
		}

		$data_hora = "$dia-$mes-$ano $hora:$minuto:$segundo";

		$sql = "INSERT INTO leituras (id_dispositivo, valor_icos_fundo, valor_icos_superficie, valor_ultrassonico, latitude, longitude, elevacao, data_hora) VALUES (:id_dispositivo,:valor_icos_fundo',:valor_icos_superficie,:valor_ultrassonico,:latitude,:longitude,:elevacao,:data_hora)";

		try{
			$response = getConnection();
			
			$stmt = $response->prepare($sql);

			$stmt->bindParam(":id_dispositivo",			$id_dispositivo);
			$stmt->bindParam(":valor_icos_fundo",		$valor_icos_fundo);
			$stmt->bindParam(":valor_icos_superficie",	$valor_icos_superficie);
			$stmt->bindParam(":valor_ultrassonico",		$valor_ultrassonico);
			$stmt->bindParam(":latitude",				$latitude);
			$stmt->bindParam(":longitude",				$longitude);
			$stmt->bindParam(":elevacao",				$elevacao);
			$stmt->bindParam(":data_hora",				$data_hora);

			$stmt->execute();

			echo 'Leitura adicionada!';	
		}
		catch(PDOException $e){
			echo 'Leitura não adicionada!';	 
		}

	});

	Function getLeituras($request, $response, $args){
		echo "Moppe 2017 - Leituras<br><br>";

//		$connection = new PDO("mysql:host=localhost;dbname=moppe_leituras", "root", "moppe"); 
		$response = getConnection()->query("SELECT * FROM leituras");

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					echo "Id da leitura: ";
					echo $row->id_leitura . " / ";
					echo "Id do dispositivo: ";
					echo $row->id_dispositivo . " / ";
					echo "Lugar: ";
					echo $row->nome_lugar . " / ";
					echo "Latitude: ";
					echo $row->latitude . " / ";
					echo "Longitude: ";
					echo $row->longitude . " / ";
					echo "Altitude/Elevação: ";
					echo $row->elevacao . " / ";
					echo "Sensor icos do fundo: ";
					echo $row->valor_icos_fundo . " / ";
					echo "Sensor icos da superficie: ";
					echo $row->valor_icos_superficie . " / ";
					echo "Sensor ultrassônico: ";
					echo $row->valor_ultrassonico . " / ";
					echo "Data e hora: ";
					echo $row->data_hora . "<br>";
				}
			}
		}
	}

	Function addLeitura($request, $response, $args){
		$id_leitura = 				$request->getParam('id_leitura');
		$id_dispositivo = 			$request->getParam('id_dispositivo');
		$valor_icos_fundo = 		$request->getParam('valor_icos_fundo');
		$valor_icos_superficie = 	$request->getParam('valor_icos_superficie');
		$valor_ultrassonico = 		$request->getParam('valor_ultrassonico');
		$nome_lugar = 				$request->getParam('nome_lugar');
		$latitude = 				$request->getParam('latitude');
		$longitude = 				$request->getParam('longitude');
		$elevacao = 				$request->getParam('elevacao');
		$data_hora = 				$request->getParam('data_hora');

		$sql = 'INSERT INTO leituras (id_leitura, id_dispositivo, valor_icos_fundo, valor_icos_superficie, valor_ultrassonico, nome_lugar, latitude, longitude, elevacao, data_hora) VALUES (:id_leitura,:id_dispositivo,:valor_icos_fundo, :valor_icos_superficie,:valor_ultrassonico,:nome_lugar,:latitude,:longitude,:elevacao,:data_hora)';

		$connection = getConnection();
		
		$stmt = $connection->prepare($sql);

		$stmt->bindParam(":id_leitura",				$id_leitura);
		$stmt->bindParam(":id_dispositivo",			$id_dispositivo);
		$stmt->bindParam(":valor_icos_fundo",		$valor_icos_fundo);
		$stmt->bindParam(":valor_icos_superficie",	$valor_icos_superficie);
		$stmt->bindParam(":valor_ultrassonico",		$valor_ultrassonico);
		$stmt->bindParam(":nome_lugar",				$nome_lugar);
		$stmt->bindParam(":latitude",				$latitude);
		$stmt->bindParam(":longitude",				$longitude);
		$stmt->bindParam(":elevacao",				$elevacao);
		$stmt->bindParam(":data_hora",				$data_hora);

		$stmt->execute();

		echo 'Leitura adicionada!';
	}
	*/

	/*
	Function addLeitura(){
		$request = \Slim\Slim::getInstance()->request();
		$leitura = json_decode($request->getBody());
		$sql = "INSERT INTO `leituras` (`id_leitura`, `id_dispositivo`, `valor_icos_fundo`, `valor_icos_superficie`, `valor_ultrassonico`, `nome_lugar`, `latitude`, `longitude`, `elevacao`, `data_hora`) VALUES (':id_leitura', ':id_dispositivo', ':valor_icos_fundo', ':valor_icos_superficie', ':valor_ultrassonico', ':nome_lugar', ':latitude', ':longitude', ':elevacao', ':data_hora')";
		
		$connection = getConnection();
		
		$stmt = $connection->prepare($sql);
		$stmt->bindParam("id_leitura",$leitura->$id_leitura);
		$stmt->bindParam("id_dispositivo",$leitura->$id_dispositivo);
		$stmt->bindParam("valor_icos_fundo",$leitura->$valor_icos_fundo);
		$stmt->bindParam("valor_icos_superficie",$leitura->$valor_icos_superficie);
		$stmt->bindParam("valor_ultrassonico",$leitura->$valor_ultrassonico);
		$stmt->bindParam("nome_lugar",$leitura->$nome_lugar);
		$stmt->bindParam("latitude",$leitura->$latitude);
		$stmt->bindParam("longitude",$leitura->$longitude);
		$stmt->bindParam("elevacao",$leitura->$elevacao);
		$stmt->bindParam("data_hora",$leitura->$data_hora);

		$stmt->execute();
		$leitura->id = $connection->lastInsertId();
		echo json_encode($leitura);
	}
	*/


	

	/*

	$app->get('/leitura_sensor', "leituraSensor");

	Function leituraSensor(){
		$a = [];
		$a[] = ["nome"=> "Pedro", "idade"=> "23333"];
		$a[] = ["nome"=> "Edson", "idade"=> "4444"];
			
		echo json_encode($a);
	}
	*/