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


class apiLighting extends apiPublic {
	
	
	public static function doLightingFlash($params){
		
		$channel = _LIGHTING_LIGHTNING_CHANNEL;
		$intensity = $params['intensity'];	
		static::setLightingIntensity(array('intensity'=>$intensity,'channel'=>$channel));
		usleep(50000);
		static::setLightingIntensity(array('intensity'=>0,'channel'=>$channel));
		usleep(50000);
		static::setLightingIntensity(array('intensity'=>$intensity,'channel'=>$channel));
		usleep(50000);
		static::setLightingIntensity(array('intensity'=>0,'channel'=>$channel));
		return array('action'=>'complete');
		
		
	}
	
	public static function getLightingIntensity(){

		$jsonIntensity = file_get_contents(_LIGHTING_INTENSITY_JSON);
		$intensity = json_decode($jsonIntensity);
		if(empty($intensity->response)){

			$fh = fopen( _LIGHTING_INTENSITY_JSON, 'w' );
			$intensity = new stdclass();
			$intensity->response[1] = 0;
			$intensity->response[2] = 0;
			$intensity->response[3] = 0;	
			$intensity->response[4] = 0;		
			fwrite($fh, json_encode($intensity));			
			fclose($fh);
			
		}
		
		foreach($intensity->response as $key=>$v){
			if(($v) <= 1){
				$intensity->response->$key = 0;
			}
		}
		
		return $intensity;
		
	}
	
	public static function setLightingIntensity($params){
		
		$channel = $params['channel'];
		
		$intensity = $params['intensity'];
		
		$piBlasterIntensity = ($intensity / 100);
		
		$pins = json_decode(file_get_contents(_LIGHTING_PINS_JSON));
	
		$pin = $pins->$channel;
	
		if($pin){
			
			shell_exec("echo \"$pin=$piBlasterIntensity\" > /dev/pi-blaster");	
			
			$intensityAll = json_decode(file_get_contents(_LIGHTING_INTENSITY_JSON));
			
			$intensityAll->response->$channel = $intensity;
			
			$intensityAllJSON = json_encode($intensityAll);
			
			file_put_contents(_LIGHTING_INTENSITY_JSON, $intensityAllJSON);
			
			return array('actioned'=>true);
			
		} else {
			
			return array('actioned'=>false);
			
		}
		
	}
	
	public static function getLightingSchedule(
		$parm1=(
			array(
				'name'=>'channel',
				'method'=>'GET',
				'description'=>'The Channel you want the schedule for',
				'required'=>true,
			)
		)
	){

		$file = constant('_LIGHTING_SCHEDULE_' . $_GET['channel'] . '_JSON');
		$jsonSchedule = file_get_contents($file);
		$schedule = json_decode($jsonSchedule);
		
		return array('response'=>$schedule);

	}

	public static function setLightingSchedule(
		$parm1=(
			array(
				'name'=>'channel',
				'method'=>'GET',
				'description'=>'The Channel you want the schedule for',
				'required'=>true,
			)
		)
	){
				
		if(!empty($_GET['schedule'])){
			
		$schedule = ($_GET['schedule']);
		
			if($schedule){
				
				$file = constant('_LIGHTING_SCHEDULE_' . $_GET['channel'] . '_JSON');
				
				$fh = fopen( $file, 'w' );
				fwrite($fh, $schedule);			
				fclose($fh);

				return array('response'=>'saved');
				
			}
		}
	}
	
	public static function setLightingOverride($params){
		
		$channel = $params['channel'];
		$intensity = $params['intensity'];
		
		$h = static::getLightingOverride(false);
		
		$h->$channel->intensity = $intensity;
		$h->$channel->start = time();
		
		$fh = fopen( _LIGHTING_OVERRIDE_JSON, 'w' );
			fwrite($fh, json_encode($h));			
			fclose($fh);
		
		$h = static::getLightingOverride();
		
		return $h;
		
	}	
	
	public static function getLightingOverride($correct=true){

		if(!file_exists(_LIGHTING_OVERRIDE_JSON)){

			$fh = fopen( _LIGHTING_OVERRIDE_JSON, 'w' );
			$override[1] = array('intensity'=>0,'start'=>0);
			$override[2] = array('intensity'=>0,'start'=>0);
			$override[3] = array('intensity'=>0,'start'=>0);
			$override[4] = array('intensity'=>0,'start'=>0);
			fwrite($fh, json_encode($override));			
			fclose($fh);
			
		}
		
		$jsonOverride = file_get_contents(_LIGHTING_OVERRIDE_JSON);
		$override = json_decode($jsonOverride);
		
		foreach($override as $channel => $settings){
			if($correct && time() - $settings->start > 600 && $settings->start != 0 ){
				static::setLightingOverride(array('channel'=>$channel,'intensity'=>0));
				$override->$channel->intensity = 0;
			}
		}
		
		return $override;
	}	
		
	
	public static function runLightingCron(){
		
		$nowHour = date('H');
		
		$nowMinute = date('i');
		
		$nextHour = date('H',strtotime("+1 hours"));
		
		$nowHourString = $nowHour . ':00';
		
		$nextHourString = $nextHour . ':00';
		
		if($nowHourString == '00:00') $nowHourString = '24:00';
		
		if($nextHourString == '00:00') $nextHourString = '24:00';		

		foreach(array(1,2,3,4) as $i){
		
			$file = constant('_LIGHTING_SCHEDULE_' . $i . '_JSON');
			$jsonSchedule = file_get_contents($file);
			$schedule[$i] = json_decode($jsonSchedule);

			/* The Intensity Right Now */
			$intensityNow = self::getLightingIntensity()->response;
			$intensityNow = $intensityNow->$i;
			
			/* Intensity at the start of the current hour */
			$intensityThisHour = $schedule[$i]->$nowHourString;
			
			/* Intensity at the start of the next hour */
			$intensityNextHour = $schedule[$i]->$nextHourString;
			
			/* The Total Change Across the Hour */
			$intensityChange = $intensityNextHour - $intensityThisHour;
			
			/* what change is required per hour */
			$intensityChangePerMinute = $intensityChange / 60;
			
			/* Changed so far this hour (minutes) */
			$intensityChangedSoFar = $intensityChangePerMinute * $nowMinute;
			
			/* What Intensity must be now */
			$intensityNew = $intensityThisHour + $intensityChangedSoFar;
		
			if($intensityNew < 0.1)$intensityNew = 0;
			if($intensityNew > 99.9)$intensityNew = 100;
		
			if($intensityThisHour < $intensityNextHour) $direction="up";
			if($intensityThisHour > $intensityNextHour) $direction="down";	
			if($intensityThisHour == $intensityNextHour) $direction="none";	
			
			if(static::getLightingOverride()->$i->intensity > 0){
				$intensityNew = static::getLightingOverride()->$i->intensity;
				$override = true;
			}else{
				$override = false;
			}

			$intensityToChange = $intensityNow - $intensityNew;
			
			if($intensityToChange > 1 || $intensityToChange < -1){
			
				if($intensityToChange < 0){
					$steps = round(0 - ($intensityToChange / 1),0);
					$stepChange = round($intensityToChange / $steps,2);
				}
				
				if($intensityToChange > 0){
					$steps = round($intensityToChange / 1,0);
					$stepChange = round($intensityToChange / $steps,2);				
				}
			
			} else {
				
				$steps = 1;
				$stepChange = $intensityToChange;
				
			}
						
			$intensityResponse[$i] = array(
				'change'=>round($intensityToChange,2),
				'was'=>round($intensityNow,2),
				'now'=>round($intensityNew,2), 
				'nowMinute'=>$nowMinute,
				'stepsCount'=>$steps,
				'stepChange'=>round($stepChange,2),
				'nextHour'=>$intensityNextHour,
				'thisHour'=>$intensityThisHour,
				'changePerMinite'=>round($intensityChangePerMinute,2),
				'changedSoFar'=>$intensityChangedSoFar,
				'direction'=> $direction,
				'override'=>$override,
			);
			
			for($step = 0 ; $step < $steps; $step++){
			
				$intensityNew = $intensityNow - round($stepChange,2);
				$intensityResponse[$i]['steps'][$step] = $intensityNew;
				self::setLightingIntensity(array('intensity'=>round($intensityNew,2),'channel'=>$i));
				$intensityNow = round($intensityNew,2);
				sleep(0.1);
			
			}
			
		}
		
	return $intensityResponse;
		
	}
	
}