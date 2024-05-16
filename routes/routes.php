<?php

require_once "models/connection.php";
require_once "controllers/get.controller.php";
require_once "controllers/post.controller.php";

$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);


/*========================================== 
Cuando no se hace ninguna petición a la API
============================================*/

if(empty($routesArray)){
	
	$json = array(
		'status' => 404,
		'result' => 'Not found'
	);

	echo json_encode($json, http_response_code($json['status']));

	return;
}


/*====================================== 
Cuando sí se hace una petición a la API
========================================*/

if(count($routesArray)== 1 && ISSET($_SERVER['REQUEST_METHOD'])){

	$table = explode("?", $routesArray[1])[0];

	/*==================================
	Validar llave secreta
	====================================*/
	if(!isset(getallheaders()["Authorization"]) || getallheaders()["Authorization"] !=Connection::apikey()){

		if(in_array($table, Connection::publicAccess())==0){
			$json = array(
				'status' => 404,
				'results' => 'No estás autorizado para realizar esta petición'
			);
		
			echo json_encode($json, http_response_code($json['status']));
		
			return;

		}else{

			
			
			/*==================================
			Acceso público
			====================================*/
			if($table !="usuarios"){

				$response= new GetController();
				$response->getData($table, "*", null, null, null, null);

				return;
			}
		}

		
	}
	
	/*==================================
	Peticiones GET
	====================================*/

	if($_SERVER['REQUEST_METHOD'] == "GET"){
		
		include "services/get.php";
	
	}

	/*==================================
	Peticiones POST
	====================================*/

	if($_SERVER['REQUEST_METHOD'] == "POST"){

		include "services/post.php";
	}

	/*==================================
	Peticiones PUT
	====================================*/

	if($_SERVER['REQUEST_METHOD'] == "PUT"){

		include "services/put.php";
	}

	/*==================================
	Peticiones DELETE
	====================================*/

	if($_SERVER['REQUEST_METHOD'] == "DELETE"){
		include "services/delete.php";

	
	}
}

