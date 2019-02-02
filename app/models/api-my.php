<?php
/**
 * api-my.php
 *
 * api my class, actions are jobs this site can run, ie by a cron, my API is this sites API, not the dynamics API this site uses to collect data
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage models
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


class apiMy extends model {
	
	/* You can add additional API Functions by adding a new model that extends either API Private (requires key) or API Public (No Key)
	
	Added automaticly to the definitions endpoint, you can add the parametres required by adding into the params for the method, for example, 
	
	$parm1=(
	array(
		'name'=>'sessionId',
		'method'=>'GET',
		'description'=>'The Session you wish to check',
		'required'=>true,
	)),
	$param2=
	array(
		'name'=>'permission',
		'method'=>'GET',
		'description'=>'The Permission you wish to check against',
		'required'=>true,
	
	These arguments for methods are "dumb" and not required but merely used by reflection class to generate the correct definitions. doc Comments didnt seem to work
	
	*/
	
	/* Call the Requested API Endpoint */
	static function call($class,$action){
		
		$apiClass = dashedtoCamel('api-' . $class);
		$apiMethod = dashedtoCamel($action);
	
		try{
		
			/* Class does not need a key or key was passed */
			if(!method_exists($apiClass,'apiKeyCheck') OR ( method_exists($apiClass,'apiKeyCheck') && $apiClass::apiKeyCheck() )){
				
				$response = $apiClass::$apiMethod($_GET);
				
				if(empty($response)){
					header('Content-Type: application/json');
					$response = array('Error'=>'There was an error with your request'); 
					http_response_code(500);
				}
								
			} else {
				header('Content-Type: application/json');
				$response = array('response'=>'Unauthorised request'); 
				http_response_code(401 );
			}
			header('Content-Type: application/json');
			return $response;
		
		}
		catch(Exception $e){
			
			$response = array('Error'=>$e);
			return $response;
			
		}

	}
	
}