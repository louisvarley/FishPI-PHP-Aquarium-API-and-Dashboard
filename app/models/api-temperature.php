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


class apiTemperature extends apiPublic {
	
	public static function getTemperature(){

		$str = file_get_contents('/sys/bus/w1/devices/w1_bus_master1/w1_master_slaves');
		$dev_ds18b20 = preg_split("/\\r\\n|\\r|\\n/", $str);

		foreach( $dev_ds18b20 as $val ){
			if( $val!='' ){
				$temp_path = "/sys/bus/w1/devices/$val/w1_slave";
				$str = file_get_contents($temp_path);
				if( preg_match('|t\=([0-9]+)|mi', $str, $m) ){
					$temp = $m[1];
				}
			}
		}	
	        
        $temp_c = round($temp / 1000.0,1);
        $temp_f = round($temp * 9.0 / 5.0 + 32.0,1);
		
		$colour = _COLOUR_BLUE_PRIMARY;
		if($temp_c > 26) $colour = _COLOUR_RED;
		if($temp_c < 24) $colour = _COLOUR_RED;
	
		$temperature = array('temperatures'=>array(
			'temp_c'=>$temp_c,
			'temp_f'=>$temp_f,
			'colour'=>$colour,
		));
		
		return $temperature;
		
	}
	
	public static function logTemperature(){
		
		
		if(!file_exists(_TEMPERATURE_LOG)){

			$fh = fopen( _TEMPERATURE_LOG, 'w' );
			$log = [];
			fwrite($fh, json_encode($log));			
			fclose($fh);
			
		}		
		

		$temperatureLog = file_get_contents(_TEMPERATURE_LOG);
		$temperatureLog = (array) json_decode($temperatureLog);
		$temperatureLog[time()] = static::getTemperature();

		/* Remove any Logs older than 24 hours */
		foreach($temperatureLog as $key=>$epoch){
			
			if(time() - $key > 86400){
				unset($temperatureLog->$key);
			}		
			
		}
		
		$fh = fopen( _TEMPERATURE_LOG, 'w' );
			fwrite($fh, json_encode($temperatureLog));			
			fclose($fh);
			
		
			
		return (array) $temperatureLog;
		
	}
	
	public static function getTemperatureLog(){
		
		$temperatureLog = file_get_contents(_TEMPERATURE_LOG);
		$temperatureLog = (array) json_decode($temperatureLog);
		
		/* Use the raw log to create a useful log of the last week and 24 hours */
		
		$nowEpoch = time();
		$epochWeek = time() - 604800;
		$epouchday = time() - 86400;	
		$weekStartsEpoch = strtotime('monday this week');
		
		
		$results = [];
		$results['weekstart'] = $weekStartsEpoch;
		

		/* Filter just Results from current week */
		foreach($temperatureLog as $epoch=>$response){
			if($epoch >= $weekStartsEpoch){
				
				$thisDate = getdate($epoch);
				$results['week'][$thisDate['weekday']] = (array) $response->temperatures;
				
			}	
		}

		/* Used to build a graph of just the previous 24 hours */
		foreach($temperatureLog as $epoch=>$response){
			if($epoch >= $epouchday){
				
				$thisDate = getdate($epoch);
				$results['day'][$thisDate['hours']] = (array) $response->temperatures;
				
			}	
		}
		
		ksort($results['day']);
		ksort($results['week']);		
		
		return (array) $results;
	}
	
}