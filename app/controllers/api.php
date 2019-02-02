<?php
/**
 * apiMy.php
 *
 * A controller for the internal My. API
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Controllers
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


class controllerApi extends controller {


	private $keyRequired = true;
	
	  
	public function onload(){
		
		if(isset($this->urlParts['class']) && isset($this->urlParts['action'])){

			$response = apiMy::call($this->urlParts['class'],$this->urlParts['action']);

			$this->view->response = $response; 
			$this->view->render();		
		}
		  
	}
	  
	 

	public static function call(){
			
			
	}
  
}
