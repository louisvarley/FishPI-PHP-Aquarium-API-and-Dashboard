<?php
/**
 * page-not-found.php
 *
 * the page not found (404) controller class
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Controllers
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


class controllerPageNotFound extends controller {
	
	public function onload(){

		$this->view->render();
		
	}
 
}
