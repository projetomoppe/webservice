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
		return new PDO("mysql:host=localhost;dbname=id3002618_moppe", "id3002618_moppe", "moppe");  
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

	$app->get('/get_home', function ($request, $response, $args) {

		header("Refresh: 5");
				
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
		</head>
		<body>
		';

		echo '
		<h2>Moppe - Webservice</h2>
		<h3>Digite:</h3> 
		<ul style="list-style-type:disc">
			<li>/get_dados na url para acessar os dados do banco.</li> <br>
			<li>/get_insere/{id_dispositivo}/{valor_icos_fundo}/{valor_icos_superficie}/{valor_ultrassonico}/{latitude_sinal}/{latitude_inteiro}/{latitude_decimal}/{longitude_sinal}/{longitude_inteiro}/{longitude_decimal}/{elevacao}/{dia}/{mes}/{ano}/{hora}/{minuto}/{segundo} na url para inserir uma nova leitura no banco. Lembre de trocar os campos com {} para as variáveis que vc quer.</li>
			<ul>
				<li> ex: get_insere/1/0/1/30.578/1/20/15156156/1/40/4898489/25/2017/09/22/09/14/12 </li>
				<li> gera: <br>
				id_dispositivo: 1 <br>
				valor_icos_fundo: 0 <br>
				valor_icos_superficie: 1 <br>
				valor_ultrassonico: 30.578 <br>
				latitude: -20.15156156 <br>
				longitude: -40.4898489 <br>
				elevacao: 25 <br>
				data_hora: 2017-09-22 09:14:12 </li> 
			</ul>	
		</ul>
		';
			
		echo '
		<br>
		</body>
		</html>
		';

	});
	

	$app->get('/get_notificacao', function($request, $response, $args){

		header("Refresh: 5");
		
		session_start();		
		
		echo '
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8">
			<meta name="description" content="API que transmite dados sobre os niveis de um rio">
			<meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
			<meta name="author" content="Edson Boldrini">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Moppe - Leituras</title>
			<style>
			table, td, th {
				border: 1px solid black;
			}

			table {
				width: 50%;
			}
			
			th {
				text-align: center;
			}

			td {
				text-align: center;
			}
			</style>
			</head>
		<body>
		';
		
		echo "<h2>Moppe - Leituras</h2>";
		
		try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=1 ORDER BY id_leitura desc limit 10");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}

		echo '
		<h3>Dispositivo 1:</h3>
		<table>
		<tr>
			<th>id_leitura</th>
			<th>Id_dispositivo</th> 
			<th>Icos Fundo</th>
			<th>Icos Superfície</th>
			<th>Ultrassonico</th>
			<th>Latitude</th>
			<th>Longitude</th>
			<th>Elevação</th>
			<th>Data/Hora</th>
			<th>Nível</th>
		</tr>
		';

		$contN = 0;
		$contI = 0; 
		$contC = 0;

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
						$nivel = "Normal";
						$contN +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
						$nivel = "Interm.";
						$contI +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
						$nivel = "Crítico";
						$contC +=1;
					}
					
					echo "
					<tr>
						<td>$row->id_leitura</td>
						<td>$row->id_dispositivo</td> 
						<td>$row->valor_icos_fundo</td>
						<td>$row->valor_icos_superficie</td>
						<td>$row->valor_ultrassonico</td>
						<td>$row->latitude</td>
						<td>$row->longitude</td>
						<td>$row->elevacao</td>
						<td>$row->data_hora</td>
						<td>$nivel</td>
					</tr>
					";
						   
				}
			}
			else {
				echo "Sem leituras para esse dispositivo";
			}
		}else{
			echo "Erro SQL";
		}

		echo '
		</table>
		<br>
		';

		echo "Contador normal = $contN<br>Contador intermediário = $contI<br>Contador crítico = $contC<br>";

		$nivelAnterior1 = $_SESSION['nivelAnterior1'];
		
		if ($contN>7 && $nivelAnterior1!="n"){
			$nivelAnterior1 = "n";
			$response = sendMessage('Dispositivo 1 - Nível normal');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		if ($contI>7 && $nivelAnterior1!="i"){
			$nivelAnterior1 = "i";
			$response = sendMessage('Dispositivo 1 - Nível intermediário');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		if ($contC>7 && $nivelAnterior1!="c"){
			$nivelAnterior1 = "c";
			$response = sendMessage('Dispositivo 1 - Nível crítico');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		$_SESSION['nivelAnterior1'] = $nivelAnterior1;

		//Começo do código para segundo dispositivo

		try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=2 ORDER BY id_leitura desc limit 10");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}
		
		echo '
		<h3>Dispositivo 2:</h3>
		<table>
		<tr>
			<th>id_leitura</th>
			<th>Id_dispositivo</th> 
			<th>Icos Fundo</th>
			<th>Icos Superfície</th>
			<th>Ultrassonico</th>
			<th>Latitude</th>
			<th>Longitude</th>
			<th>Elevação</th>
			<th>Data/Hora</th>
			<th>Nível</th>
		</tr>
		';

		$contN = 0;
		$contI = 0; 
		$contC = 0;

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
						$nivel = "Normal";
						$contN +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
						$nivel = "Interm.";
						$contI +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
						$nivel = "Crítico";
						$contC +=1;
					}
					
					echo "
					<tr>
						<td>$row->id_leitura</td>
						<td>$row->id_dispositivo</td> 
						<td>$row->valor_icos_fundo</td>
						<td>$row->valor_icos_superficie</td>
						<td>$row->valor_ultrassonico</td>
						<td>$row->latitude</td>
						<td>$row->longitude</td>
						<td>$row->elevacao</td>
						<td>$row->data_hora</td>
						<td>$nivel</td>
					</tr>
					";
						
				}
			}
			else {
				echo "Sem leituras para esse dispositivo";
			}
		}else{
			echo "Erro SQL";
		}

		echo '
		</table>
		<br>
		';
 
		echo "Contador normal = $contN<br>Contador intermediário = $contI<br>Contador crítico = $contC<br>";

		$nivelAnterior2 = $_SESSION['nivelAnterior2'];

		if ($contN>7 && $nivelAnterior2!="n"){
			$nivelAnterior2 = "n";
			$response = sendMessage('Dispositivo 2 - Nível normal');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		if ($contI>7 && $nivelAnterior2!="i"){
			$nivelAnterior2 = "i";
			$response = sendMessage('Dispositivo 2 - Nível intermediário');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		if ($contC>7 && $nivelAnterior2!="c"){
			$nivelAnterior2 = "c";
			$response = sendMessage('Dispositivo 2 - Nível crítico');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		$_SESSION['nivelAnterior2'] = $nivelAnterior2;

		echo '
		<br>
		</body>
		</html>
		';

		
	});

	$app->get('/get_historico', function($request, $response, $args){

		header("Refresh: 5");
		
		session_start();		
		
		echo '
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8">
			<meta name="description" content="API que transmite dados sobre os niveis de um rio">
			<meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
			<meta name="author" content="Edson Boldrini">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Moppe - Leituras</title>
			<style>
			table, td, th {
				border: 1px solid black;
			}

			table {
				width: 50%;
			}
			
			th {
				text-align: center;
			}

			td {
				text-align: center;
			}
			</style>
			</head>
		<body>
		';
		
		echo "<h2>Moppe - Leituras</h2>";
		
		try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=1 ORDER BY id_leitura desc limit 10");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}

		echo '
		<h3>Dispositivo 1:</h3>
		<table>
		<tr>
			<th>id_leitura</th>
			<th>Id_dispositivo</th> 
			<th>Icos Fundo</th>
			<th>Icos Superfície</th>
			<th>Ultrassonico</th>
			<th>Latitude</th>
			<th>Longitude</th>
			<th>Elevação</th>
			<th>Data/Hora</th>
			<th>Nível</th>
		</tr>
		';

		$contN = 0;
		$contI = 0; 
		$contC = 0;

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
						$nivel = "Normal";
						$contN +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
						$nivel = "Interm.";
						$contI +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
						$nivel = "Crítico";
						$contC +=1;
					}
					
					echo "
					<tr>
						<td>$row->id_leitura</td>
						<td>$row->id_dispositivo</td> 
						<td>$row->valor_icos_fundo</td>
						<td>$row->valor_icos_superficie</td>
						<td>$row->valor_ultrassonico</td>
						<td>$row->latitude</td>
						<td>$row->longitude</td>
						<td>$row->elevacao</td>
						<td>$row->data_hora</td>
						<td>$nivel</td>
					</tr>
					";
						   
				}
			}
			else {
				echo "Sem leituras para esse dispositivo";
			}
		}else{
			echo "Erro SQL";
		}

		echo '
		</table>
		<br>
		';

		echo "Contador normal = $contN<br>Contador intermediário = $contI<br>Contador crítico = $contC<br>";

		//Começo do código para segundo dispositivo

		try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=2 ORDER BY id_leitura desc limit 10");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}
		
		echo '
		<h3>Dispositivo 2:</h3>
		<table>
		<tr>
			<th>id_leitura</th>
			<th>Id_dispositivo</th> 
			<th>Icos Fundo</th>
			<th>Icos Superfície</th>
			<th>Ultrassonico</th>
			<th>Latitude</th>
			<th>Longitude</th>
			<th>Elevação</th>
			<th>Data/Hora</th>
			<th>Nível</th>
		</tr>
		';

		$contN = 0;
		$contI = 0; 
		$contC = 0;

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
						$nivel = "Normal";
						$contN +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
						$nivel = "Interm.";
						$contI +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
						$nivel = "Crítico";
						$contC +=1;
					}
					
					echo "
					<tr>
						<td>$row->id_leitura</td>
						<td>$row->id_dispositivo</td> 
						<td>$row->valor_icos_fundo</td>
						<td>$row->valor_icos_superficie</td>
						<td>$row->valor_ultrassonico</td>
						<td>$row->latitude</td>
						<td>$row->longitude</td>
						<td>$row->elevacao</td>
						<td>$row->data_hora</td>
						<td>$nivel</td>
					</tr>
					";
						
				}
			}
			else {
				echo "Sem leituras para esse dispositivo";
			}
		}else{
			echo "Erro SQL";
		}

		echo '
		</table>
		<br>
		';
 
		echo "Contador normal = $contN<br>Contador intermediário = $contI<br>Contador crítico = $contC<br>";

		echo '
		<br>
		</body>
		</html>
		';

		
	});

	$app->get('/get_insere/{id_dispositivo}/{valor_icos_fundo}/{valor_icos_superficie}/{valor_ultrassonico}/{latitude_sinal}/{latitude_inteiro}/{latitude_decimal}/{longitude_sinal}/{longitude_inteiro}/{longitude_decimal}/{elevacao}/{dia}/{mes}/{ano}/{hora}/{minuto}/{segundo}', function($request, $response, $args){
		
		//header("Refresh: 5; url = /moppe-ws/public/index.php/get_notificacao");

		session_start();

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

		if ($valor_icos_fundo == 1 && $valor_icos_superficie == 0){
			$nivel = "Normal";
		}

		if ($valor_icos_fundo == 0 && $valor_icos_superficie == 0){
			$nivel = "Intermediário";
		}

		if ($valor_icos_fundo == 0 && $valor_icos_superficie == 1){
			$nivel = "Crítico";
		}

		echo '
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8">
			<meta name="description" content="API que transmite dados sobre os niveis de um rio">
			<meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
			<meta name="author" content="Edson Boldrini">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Moppe - Insere</title>
			<style>

			</style>
			</head>
		<body>
		';

		echo "<h2>Nova leitura:</h2>";
		echo "Id_dispositivo: $id_dispositivo<br>";
		echo "Valor_icos_fundo: $valor_icos_fundo<br>";
		echo "Valor_icos_superficie: $valor_icos_superficie<br>";
		echo "Valor_ultrassonico: $valor_ultrassonico<br>";
		echo "Latitude: $latitude<br>";
		echo "Longitude: $longitude<br>";
		echo "Elevacao: $elevacao<br>";
		echo "Data_hora: $data_hora<br>";
		echo "Nível: $nivel<br><br>";

		try{
			$response = getConnection();
			
			$response = $response->query("INSERT INTO leituras (id_dispositivo, valor_icos_fundo, valor_icos_superficie, valor_ultrassonico, latitude, longitude, elevacao, data_hora) VALUES ('$id_dispositivo','$valor_icos_fundo','$valor_icos_superficie','$valor_ultrassonico','$latitude','$longitude','$elevacao','$data_hora');");	
		
			echo 'Código SQL:<br>';
			echo "INSERT INTO leituras (id_dispositivo, valor_icos_fundo, valor_icos_superficie, valor_ultrassonico, latitude, longitude, elevacao, data_hora) VALUES ('$id_dispositivo','$valor_icos_fundo','$valor_icos_superficie','$valor_ultrassonico','$latitude','$longitude','$elevacao','$data_hora');<br>";	
			echo '<br><b>Leitura adicionada!</b><br>';	
		}	
		catch(PDOException $e){
			echo '<br><b>Leitura não adicionada!</b><br>';	 
		}

		try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=1 ORDER BY id_leitura desc limit 10");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}

		$contN = 0;
		$contI = 0; 
		$contC = 0;

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
						$nivel = "Normal";
						$contN +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
						$nivel = "Interm.";
						$contI +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
						$nivel = "Crítico";
						$contC +=1;
					}
					  
				}
			}
			else {
				echo "Sem leituras para esse dispositivo";
			}
		}else{
			echo "Erro SQL";
		}

		echo "<br>D1:<br><br>Contador normal = $contN<br>Contador intermediário = $contI<br>Contador crítico = $contC<br>";

		$nivelAnterior1 = $_SESSION['nivelAnterior1'];
		
		if ($contN>7 && $nivelAnterior1!="n"){
			$nivelAnterior1 = "n";
			$response = sendMessage('Dispositivo 1 - Nível normal');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		if ($contI>7 && $nivelAnterior1!="i"){
			$nivelAnterior1 = "i";
			$response = sendMessage('Dispositivo 1 - Nível intermediário');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		if ($contC>7 && $nivelAnterior1!="c"){
			$nivelAnterior1 = "c";
			$response = sendMessage('Dispositivo 1 - Nível crítico');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		$_SESSION['nivelAnterior1'] = $nivelAnterior1;

		//Começo do código para segundo dispositivo

		try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=2 ORDER BY id_leitura desc limit 10");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}
		
		$contN = 0;
		$contI = 0; 
		$contC = 0;

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
						$nivel = "Normal";
						$contN +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
						$nivel = "Interm.";
						$contI +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
						$nivel = "Crítico";
						$contC +=1;
					}
									
				}
			}
			else {
				echo "Sem leituras para esse dispositivo";
			}
		}else{
			echo "Erro SQL";
		}

		echo "<br>D2:<br><br>Contador normal = $contN<br>Contador intermediário = $contI<br>Contador crítico = $contC<br>";
		
		$nivelAnterior2 = $_SESSION['nivelAnterior2'];
		
		if ($contN>7 && $nivelAnterior2!="n"){
			$nivelAnterior2 = "n";
			$response = sendMessage('Dispositivo 2 - Nível normal');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		if ($contI>7 && $nivelAnterior2!="i"){
			$nivelAnterior2 = "i";
			$response = sendMessage('Dispositivo 2 - Nível intermediário');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		if ($contC>7 && $nivelAnterior2!="c"){
			$nivelAnterior2 = "c";
			$response = sendMessage('Dispositivo 2 - Nível crítico');
			$return["allresponses"] = $response;
			$return = json_encode( $return);
			
			print("\n\nJSON received:\n");
			print($return);
			print("\n");
		}

		$_SESSION['nivelAnterior2'] = $nivelAnterior2;


		echo '
		<br>
		</body>
		</html>';

	});

	$app->get('/get_d1', function($request, $response, $args){

		try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=1 ORDER BY id_leitura desc limit 1");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}

		if($response->execute()){	
			if($response->rowCount() > 0){
				$row = $response->fetch(PDO::FETCH_OBJ);
				if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
					$nivel = "Normal";
				}

				if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
					$nivel = "Interm.";
				}

				if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
					$nivel = "Crítico";
				}
			}
			else {
				echo "<br>Sem leituras para esse dispositivo<br>";
			}
		}else{
			echo "<br>Erro SQL<br>";
		}
		
		list ($data, $tempo) = explode(' ', $row->data_hora);
		list ($ano, $mes, $dia) = explode('-', $data);
		list ($hora, $minuto, $segundo) = explode(':', $tempo);

		echo '
		<!DOCTYPE html lang="pt-br">
		<html>
		<head>
    		<meta charset="utf-8">
    		<meta http-equiv="refresh" content="60">
    		<meta name="description" content="API que transmite dados sobre os niveis de um rio">
    		<meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
    		<meta name="author" content="Edson Boldrini">
    		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    		<title>Moppe - Dispositivo 1</title>
<!--		<meta name = "viewport" content = "width = device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=0">
-->
			<style>
				body{
					font-size:20px;
					background-color:#F2F2F2;
					font-family:Arial, Helvetica, sans-serif;
					color:#FFF;
				} 
				
				.centraliza{
					width:94%;
					heigth:auto;
					margin:10px auto 0 auto;
					background-color:#666;
					padding:10px;
					text-align:center;
				} 

				h1{
					font-size:130%;
					margin:0 0 10px
				} 
				
				p{
					font-size:100%;
					margin:10px 0;
				} 
				
				#map{
					width:100%;
					height:600px;
					margin:10px auto 0;
				}
			</style>
		</head>
		<body>
		<div class="centraliza">
		';	

		echo "
		<h1>MOPPE - Dispositivo 1</h1>
		
		<p>ID do Dispositivo:
		$row->id_dispositivo
		</p>
		
		<p>Sensor ICOS Fundo - Leitura:
		$row->valor_icos_fundo
		</p>
		
		<p>Sensor ICOS Superefície - Leitura:
		$row->valor_icos_superficie
		</p>
		";

		echo '<p>Estado:';
		
		if($nivel == "Normal")
			echo '<span style="color:#3CB371"> NORMAL</span>';
	  	if($nivel == "Interm.")
		  	echo '<span style="color:#FFCC33"> INTERMEDIÁRIO (ALERTA)</span>';
	 	if($nivel == "Crítico")
			echo '<span style="color:#FA8072"> CRÍTICO (EMERGENCIAL)</span>';
		
		echo '</p>';	
		
		echo "
		<p>Sensor Ultrassônico - Leitura:
		$row->valor_ultrassonico cm
		</p>

		<p>GPS Latitude:
		$row->latitude
		</p>

		<p>GPS Latitude:
		$row->longitude
		</p>

		<p>GPS Elevação:
		$row->elevacao
		</p>
		
		<p>GPS Data e hora:
		$dia/$mes/$ano
		$hora:$minuto:$segundo
		</p>
		";

		echo '
		<div id="map"></div>
		<script>
		  function initMap() {
		';
		
		echo "
			var uluru = {lat: $row->latitude, lng: $row->longitude};";
		
		echo '
			var map = new google.maps.Map(document.getElementById("map"), {
			  zoom: 16,
			  center: uluru
			});
		';
		
		if ($nivel == "Normal"){
			echo '
			var image = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png";
			var marker = new google.maps.Marker({position: uluru,map: map,icon: image});
		  }
		';			
		}

		if ($nivel == "Interm."){
			echo '
			var image = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png";
			var marker = new google.maps.Marker({position: uluru,map: map,icon: image});
		  }
		';			
		}

		if ($nivel == "Crítico"){
			echo '
			var marker = new google.maps.Marker({
			  position: uluru,
			  map: map
			});
		  }
		';			
		}
		
		echo '
		</script>

		<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDpWk9lPcJbrtpXwaL93gHuCxT0T9mT_Ag&callback=initMap">
		</script>
		
		</body>
		</html>';
	
	});

	$app->get('/get_d2', function($request, $response, $args){
		
		try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=2 ORDER BY id_leitura desc limit 1");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}

		if($response->execute()){	
			if($response->rowCount() > 0){
				$row = $response->fetch(PDO::FETCH_OBJ);
				if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
					$nivel = "Normal";
				}

				if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
					$nivel = "Interm.";
				}

				if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
					$nivel = "Crítico";
				}
			}
			else {
				echo "<br>Sem leituras para esse dispositivo<br>";
			}
		}else{
			echo "<br>Erro SQL<br>";
		}
		
		list ($data, $tempo) = explode(' ', $row->data_hora);
		list ($ano, $mes, $dia) = explode('-', $data);
		list ($hora, $minuto, $segundo) = explode(':', $tempo);

		echo '
		<!DOCTYPE html lang="pt-br">
		<html>
		<head>
    		<meta charset="utf-8">
    		<meta http-equiv="refresh" content="60">
    		<meta name="description" content="API que transmite dados sobre os niveis de um rio">
    		<meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
    		<meta name="author" content="Edson Boldrini">
    		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    		<title>Moppe - Dispositivo 2</title>
<!--		<meta name = "viewport" content = "width = device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=0">
-->
			<style>
				body{
					font-size:20px;
					background-color:#F2F2F2;
					font-family:Arial, Helvetica, sans-serif;
					color:#FFF;
				} 
				
				.centraliza{
					width:94%;
					heigth:auto;
					margin:10px auto 0 auto;
					background-color:#666;
					padding:10px;
					text-align:center;
				} 

				h1{
					font-size:130%;
					margin:0 0 10px
				} 
				
				p{
					font-size:100%;
					margin:10px 0;
				} 
				
				#map{
					width:100%;
					height:600px;
					margin:10px auto 0;
				}

			</style>
		</head>
		<body>
		<div class="centraliza">
		';	

		echo "
		<h1>MOPPE - Dispositivo 2</h1>
		
		<p>ID do Dispositivo:
		$row->id_dispositivo
		</p>
		
		<p>Sensor ICOS Fundo - Leitura:
		$row->valor_icos_fundo
		</p>
		
		<p>Sensor ICOS Superefície - Leitura:
		$row->valor_icos_superficie
		</p>
		";

		echo '<p>Estado:';
		
		if($nivel == "Normal")
			echo '<span style="color:#3CB371"> NORMAL</span>';
	  	if($nivel == "Interm.")
		  	echo '<span style="color:#FFCC33"> INTERMEDIÁRIO (ALERTA)</span>';
	 	if($nivel == "Crítico")
			echo '<span style="color:#FA8072"> CRÍTICO (EMERGENCIAL)</span>';
		
		echo '</p>';		
		
		echo "
		<p>Sensor Ultrassônico - Leitura:
		$row->valor_ultrassonico cm
		</p>

		<p>GPS Latitude:
		$row->latitude
		</p>

		<p>GPS Latitude:
		$row->longitude
		</p>

		<p>GPS Elevação:
		$row->elevacao
		</p>
		
		<p>GPS Data e hora:
		$dia/$mes/$ano
		$hora:$minuto:$segundo
		</p>
		";

		echo '
		<div id="map"></div>
		<script>
			function initMap() {
		';
		
		echo "
			var uluru = {lat: $row->latitude, lng: $row->longitude};";
		
		echo '
			var map = new google.maps.Map(document.getElementById("map"), {
				zoom: 16,
				center: uluru
			});
		';
		
		if ($nivel == "Normal"){
			echo '
			var image = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png";
			var marker = new google.maps.Marker({position: uluru,map: map,icon: image});
			}
		';			
		}

		if ($nivel == "Interm."){
			echo '
			var image = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png";
			var marker = new google.maps.Marker({position: uluru,map: map,icon: image});
			}
		';			
		}

		if ($nivel == "Crítico"){
			echo '
			var marker = new google.maps.Marker({
				position: uluru,
				map: map
			});
			}
		';			
		}
		
		echo '
		</script>

		<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDpWk9lPcJbrtpXwaL93gHuCxT0T9mT_Ag&callback=initMap">
		</script>
		
		</body>
		</html>';

	});

	$app->get('/get_estado', function($request, $response, $args){
	    
	    try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=1 ORDER BY id_leitura desc limit 10");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}

		$contN = 0;
		$contI = 0; 
		$contC = 0;

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
						$nivel = "Normal";
						$contN +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
						$nivel = "Interm.";
						$contI +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
						$nivel = "Crítico";
						$contC +=1;
					}
					  
				}
			}
			else {
				echo "Sem leituras para esse dispositivo";
			}
		}else{
			echo "Erro SQL";
		}

		//echo "Contador normal = $contN<br>Contador intermediário = $contI<br>Contador crítico = $contC<br>";

		if ($contN>7){
			$nivelAnterior1 = "n";
		}

		if ($contI>7){
			$nivelAnterior1 = "i";
		}

		if ($contC>7){
			$nivelAnterior1 = "c";
		}

		//Começo do código para segundo dispositivo

		try{
			$response = getConnection();
			
			$response = $response->query("SELECT * FROM leituras WHERE id_dispositivo=2 ORDER BY id_leitura desc limit 10");	
		}catch(PDOException $e){
			echo '<br>Erro SQL<br>';	 
		}
		
		$contN = 0;
		$contI = 0; 
		$contC = 0;

		if($response->execute()){	
			if($response->rowCount() > 0){
				while($row = $response->fetch(PDO::FETCH_OBJ)){
					if ($row->valor_icos_fundo == 1 && $row->valor_icos_superficie == 0){
						$nivel = "Normal";
						$contN +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 0){
						$nivel = "Interm.";
						$contI +=1;
					}

					if ($row->valor_icos_fundo == 0 && $row->valor_icos_superficie == 1){
						$nivel = "Crítico";
						$contC +=1;
					}
									
				}
			}
			else {
				echo "Sem leituras para esse dispositivo";
			}
		}else{
			echo "Erro SQL";
		}

		//echo "Contador normal = $contN<br>Contador intermediário = $contI<br>Contador crítico = $contC<br>";
		
		if ($contN>7){
			$nivelAnterior2 = "n";
		}

		if ($contI>7){
			$nivelAnterior2 = "i";
		}

		if ($contC>7){
			$nivelAnterior2 = "c";
		}
	    
		echo '
		<!DOCTYPE html lang="pt-br">
		<html>
		<head>
    		<meta charset="utf-8">
    		<meta http-equiv="refresh" content="60">
    		<meta name="description" content="API que transmite dados sobre os niveis de um rio">
    		<meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
    		<meta name="author" content="Edson Boldrini">
    		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    		<title>Moppe - Estados</title>
<!--		<meta name = "viewport" content = "width = device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=0">
-->
			<style>
				body{
					font-size:20px;
					background-color:#F2F2F2;
					font-family:Arial, Helvetica, sans-serif;
					color:#FFF;
				} 
				
				.centraliza{
					width:95%;
					heigth:auto;
					margin:10px auto 0 auto;
					background-color:#666;
					padding:10px;
					text-align:center;
				} 

				h1{
					font-size:130%;
					margin:0 0 10px
				} 
				
				p{
					font-size:100%;
					margin:10px 0;
				} 
				
				#map{
					width:100%;
					height:600px;
					margin:10px auto 0;
				}

			</style>
		</head>
		<body>
		<div class="centraliza">
		';	
		
		echo "
		<h1>MOPPE - Estados</h1>
		";
		
		echo "
		<p>MOPPE - Dispositivo 1:<br>
		";

		if($nivelAnterior1 == "n")
			echo '<span style="color:#3CB371"> NORMAL</span>';
	  	if($nivelAnterior1 == "i")
		  	echo '<span style="color:#FFCC33"> INTERMEDIÁRIO (ALERTA)</span>';
	 	if($nivelAnterior1 == "c")
			echo '<span style="color:#FA8072"> CRÍTICO (EMERGENCIAL)</span>';
		
		echo "
		</p>
		";

		echo "
		<p>MOPPE - Dispositivo 2:<br>
		";

		if($nivelAnterior2 == "n")
			echo '<span style="color:#3CB371"> NORMAL</span>';
		if($nivelAnterior2 == "i")
			echo '<span style="color:#FFCC33"> INTERMEDIÁRIO (ALERTA)</span>';
		if($nivelAnterior2 == "c")
			echo '<span style="color:#FA8072"> CRÍTICO (EMERGENCIAL)</span>';

		echo "
		</p>
		";

		echo "
		<br>
		<p><b>MOPPE - Estado geral:</b><br>
		";

		if($nivelAnterior1 == "n" && $nivelAnterior2 =="n")
			echo '<span style="color:#3CB371"> NORMAL</span>';
		if($nivelAnterior1 == "n" && $nivelAnterior2 =="i")
			echo '<span style="color:#3CB371"> NORMAL</span>';
		if($nivelAnterior1 == "i" && $nivelAnterior2 =="n")
			echo '<span style="color:#3CB371"> NORMAL</span>';

		if($nivelAnterior1 == "i" && $nivelAnterior2 =="i")
			echo '<span style="color:#FFCC33"> INTERMEDIÁRIO (ALERTA)</span>';

		if($nivelAnterior1 == "i" && $nivelAnterior2 =="c")
			echo '<span style="color:#FFCC33"> FIQUE LIGADO! NÍVEL ESTÁ ALTO</span>';
		if($nivelAnterior1 == "c" && $nivelAnterior2 =="i")
			echo '<span style="color:#FFCC33"> FIQUE LIGADO! NÍVEL ESTÁ ALTO</span>';
		
		if($nivelAnterior1 == "c" && $nivelAnterior2 =="c")
			echo '<span style="color:#FA8072"> SAIA JÁ DA SUA CASA! O NÍVEL ESTÁ CRÍTICO!</span>';

		echo "
		</p>
		";
	

		echo'
		</body>
		</html>
		';

		
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

			echo '<br>Leitura adicionada!<br>';	
		}
		catch(PDOException $e){
			echo '<br>Leitura não adicionada!<br>';	 
		}

	});

	<!--
				body{
					font-size:16px;
					background-color:#F2F2F2;
					font-family:Arial, Helvetica, sans-serif;
					color:#FFF;
				} 
				
				.centraliza{
					width:90%;
					heigth:auto;
					margin:20px auto 0 auto;
					background-color:#666;
					padding:15px;
					text-align:center;
				} 

				h1{
					font-size:112.5%;
					margin:0 0 20px
				} 
				
				p{
					font-size:100%;
					margin:5px 0;
				} 
				
				#map{
					width:90%;
					height:300px;
					margin:30px auto 0;
				}
-->
*/