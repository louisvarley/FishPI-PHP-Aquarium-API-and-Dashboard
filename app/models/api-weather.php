<?php
/**
 * api-weather.php
 *
 * Used for checking if a user is logged in
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage models
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


class apiWeather extends apiPublic {
	
	public static $volume = 0;
	
	public static function setWeatherStatus($params){
		
		$status = constant('_WEATHER_' . strtoupper($params['status']));
		
		$fh = fopen( _WEATHER_STATUS, 'w' );
			$response = new stdclass();
			$response->response = array('status'=>$status);
			
			fwrite($fh, json_encode($response));			
			fclose($fh);
			
		return array(true);	
		
	}
	
	public static function getWeatherStatus($params){

		if(date("G")>='0')static::setWeatherStatus(array('status'=>'moon'));
		if(date("G")>='5')static::setWeatherStatus(array('status'=>'sun_rise'));		
		if(date("G")>='10')static::setWeatherStatus(array('status'=>'sun'));		
		if(date("G")>='17')static::setWeatherStatus(array('status'=>'sun_set'));		
		if(date("G")>='20')static::setWeatherStatus(array('status'=>'moon'));		
		
		$response = json_decode(file_get_contents(_WEATHER_STATUS));
		
		if(empty($response->response->status)){
			$response->response->status = '<i class="fas fa-sun"></i>';
		}
		
		return $response;
		
	}	
	
	//sudo usermod -aG audio www-data
	//sudo usermod -aG video www-data
	//sudo usermod -a -G video www-data
	//chmod a+rw /dev/vchiq

	public static function thunder($params){
		
		$files = glob(_FS_BASE . '/static/mp3/thunder/*.mp3');
		$file = array_rand($files);
		$file = $files[$file];
		
		$cmd = 'sh ' . _SCRIPTS . 'play.sh ' . $file . ' > /dev/null 2>&1 &';
		$response = shell_exec($cmd);
		return array('command'=>$cmd,'response'=>$response);
		
	}
	
	public static function rainOn($params=array()){

		apiSocket::switchOn(array('socket'=>_SOCKET_RAIN_BAR));
		$cmd = 'sh ' . _SCRIPTS . 'play.sh ' . _FS_BASE . '/static/mp3/rain/full.mp3 > /dev/null 2>&1 &';
		$response = shell_exec($cmd);
		return array('command'=>$cmd,'response'=>$response);
		
	}
	
	public static function rainOff($params=array()){

		apiSocket::switchOff(array('socket'=>_SOCKET_RAIN_BAR));
		return apiWeather::kill();
		
	}	
	
	public static function volumeReset($params=array()){
		
		if(static::$volume > 0){
			for($volume = 0 ; $volume < static::$volume; $volume++){
				static::volumeDown();
			}
		}
		
		$response['volume'] = static::$volume;
		return array('response'=>$response);
		
	}
	
	public static function volumeSet($params){
		
		$targetVolume = $params['volume'];
		static::volumeReset();
		for($volume = 0 ; $volume < $targetVolume; $volume++){
				static::volumeUp();
		}
		
	}
	
	public static function volumeNow($params=array()){
		
		$response['volume'] = static::$volume;
		return array('response'=>$response);
		
	}
	
	public static function volumeUp($params=array()){
		
		$cmd = 'sh ' . _SCRIPTS . 'volume.sh + > /dev/null 2>&1 &';
		$response = shell_exec($cmd);
		static::$volume = static::$volume + 1;
		$response['volume'] = static::$volume;
		return array('command'=>$cmd,'response'=>$response);
		
	}
	
	public static function volumeDown($params=array()){
		
		$cmd = 'sh ' . _SCRIPTS . 'volume.sh - > /dev/null 2>&1 &';
		$response = shell_exec($cmd);
		static::$volume = static::$volume - 1;
		$response['volume'] = static::$volume;
		return array('command'=>$cmd,'response'=>$response);
		
	}	
	
	public static function playfifo($params){
				
		$cmd = 'echo . > /tmp/cmd >/dev/null 2>/dev/null &';
		$response = shell_exec($cmd);	
		return array('command'=>$cmd,'response'=>$response);
	}
	
	public static function mkfifo($params){
		
		$cmd = "mkfifo /tmp/cmd &"; 	 
		$response = shell_exec($cmd);
		return array('command'=>$cmd,'response'=>$response);
		
	}
	
	public static function kill($params=array()){
		
		$cmd = "ps aux|grep mp3|awk '{print $2}'|xargs -n 1 kill"; 	 
		$response = shell_exec($cmd);
		return array('command'=>$cmd,'response'=>$response);

	}
	
	public static function stormMap($params){
		
		/* Maximum Delay for Sound vs Light */
		$delayM = 7;
		$minutes = $params['minutes'];
		$totalSeconds = $minutes * 60;
		$intensity = $params['intensity'];
		$map = [];
				
		for($second = 0 ; $second < $totalSeconds; $second++){
			
			if($second < ($totalSeconds / 2)) $percentage = round($second / (($totalSeconds / 2) / 100),0);
			if($second > ($totalSeconds / 2)) $percentage = 100 - (round(($second / (($totalSeconds / 2) / 100) ),0) - 100);
					
			$map[$second]['percentage'] = $percentage;
			
			$delay = round($delayM * (( 100 - $percentage ) / 100 ),0) ;
			
			$map[$second]['delay'] = $delay;
			$map[$second]['volume'] = round($percentage / 10,0);
			
			$random = rand(1,400);
			
			if($random <= $intensity && $intensity > 0){		
				$map[$second]['flash'] = 'Y';		
				$map[$second + $delay]['sound'] = "Y";
			}
				
		}
		
		return $map;
		
	}
	
	public static function doStorm($params){
		
		$map = static::stormMap($params);
				
		static::rainOn();
		
		set_time_limit(($minutes*60)+60);
				
		static::setWeatherStatus(array('status'=>'cloud'));		
		apiLighting::setLightingOverride(array('channel'=>'1','intensity'=>1));
		apiLighting::setLightingOverride(array('channel'=>'2','intensity'=>1));
		apiLighting::setLightingOverride(array('channel'=>'3','intensity'=>1));
		apiLighting::runLightingCron();
				
		foreach($map as $mapLine){
			
			static::setWeatherStatus(array('status'=>'storm'));
			
			$r = time();
			
			if(!empty($mapLine['sound']) && $mapLine['sound'] == 'Y'){
				apiWeather::thunder(array('percentage'=>$percentage));
			}
			
			if(!empty($mapLine['flash']) && $mapLine['flash'] == 'Y'){
				apiLighting::doLightingFlash(array('intensity'=>$mapLine['percentage']));
			}			

			while(time() <= $r){
				usleep(1000);
			}
		}
		
		static::setWeatherStatus(array('status'=>'cloud'));
		
		apiLighting::setLightingOverride(array('channel'=>'1','intensity'=>0));
		apiLighting::setLightingOverride(array('channel'=>'2','intensity'=>0));
		apiLighting::setLightingOverride(array('channel'=>'3','intensity'=>0));
		apiLighting::runLightingCron();		
		
		static::getWeatherStatus();
		
		static::rainOff();
		apiWeather::kill();
		
		return(array(
			'map'=>$map,
		));
			
	}	
	
}
