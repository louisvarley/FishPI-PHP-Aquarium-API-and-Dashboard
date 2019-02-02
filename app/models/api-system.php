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


class apiSystem extends apiPublic {
	
	public static function getCpuTemperature(){

		$output = shell_exec("cat /sys/class/thermal/thermal_zone0/temp");	
		
		$temp = $output / 1000;
		$temp = round($temp,1);
			
		return array('response'=>array('temperature'=>$temp));
	}

	public static function getCpuMemoryUsage(){

		$cpu = shell_exec("top -bn1 | awk '/Mem/ { mem = \"Memory in Use: \" $5 / $3 * 100 \"%\" }; /Cpu/ { cpu = \"\" 100 - $8 \"\" };END   { print cpu }'");	
		$memory = shell_exec("top -bn1 | awk '/Mem/ { mem = \"\" $5 / $3 * 100 \"\" }; /Cpu/ { cpu = \"\" 100 - $8 \"\" };END   { print mem }'");	
				
		$cpu = rtrim($cpu,"\n");		
		$memory = rtrim($memory," \n");	
		
		return array('response'=>array('cpu'=>$cpu, 'memory'=>$memory));
	}
	
}

