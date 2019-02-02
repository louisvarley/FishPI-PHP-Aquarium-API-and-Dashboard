<?php
/**
 * apiMy.php
 *
 * a Empty View for use by the GO Action Engine Controller
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Views
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


class viewAPI extends view {
	
	public function render(){
		
		if(!empty($this->response)){
			echo json_encode($this->response);
		}

	}
	
}
