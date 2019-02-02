<?php
/**
 * api-session.php
 *
 * Used for checking if a user is logged in
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage models
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


class apiWidgets extends apiPublic {
	
	/* Returns the state of authentication plus the login URL, this login action will return the session ID to the return URL  */
	public static function getWidgets(
		$parm1=(
			array(
				'name'=>'sessionId',
				'method'=>'GET',
				'description'=>'The Session ID You wish to verify',
				'required'=>true,
			)
		)
	){

		$jsonWidgets = file_get_contents(_WIDGETS_JSON);
		
		$widgets = json_decode($jsonWidgets);
		
		return $widgets;
		
	}
	
}