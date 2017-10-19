<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app = new \Slim\App;

	$app->get('/dados', function($request, $response, $args){
		echo 'dados';
	});
	
//	$app->run();