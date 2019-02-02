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


class apiWater extends apiPublic {
	
	/* Returns status of the FLOW (IDLE or FLOW)	*/
	public static function getWaterFlowStatus(){
		
		$flow = file_get_contents(_WATER_FLOW_STATUS);
		$response = json_decode($flow);
		
		if($response->response->status == "IDLE"){
			$response->response->icon = '<i class="fas fa-tint"></i> Idle';
		}else{
			$response->response->icon = '<i class="fas fa-circle-notch fa-spin"></i>';	
		}
		
		$response->response->daemon = static::getFlowDaemonStatus();
		
		return $response;
		
		
	}
	
	/* Returns Entire flow log */
	public static function getWaterFlowLog(){
		
		$response = array('response'=>'');
		
		if(file_exists(_WATER_FLOW_LOG)){
		
			$flowLog = file_get_contents(_WATER_FLOW_LOG);
			$response = json_decode($flowLog);
		
		}
		
		return $response;
		
	}
	
	/* Get Flow This Week Per Day and Totals */
	public static function getFlowWeekByDay(){
		
		$log = (array) static::getWaterFlowLog()->response;
		$sow = strtotime('monday this week');
		
		foreach (range(0, 23) as $i) {
			$default[$i] = array('count'=>0,'litres'=>0);
		}
		
		$response = [];
		$response['Monday']=$default;
		$response['Tuesday']=$default;
		$response['Wednesday']=$default;
		$response['Thursday']=$default;
		$response['Friday']=$default;
		$response['Saturday']=$default;
		$response['Sunday']=$default;
		
		foreach($log as $day=>$logData){
			
			$t = strtotime($day);
			if($t >= $sow){
				
				foreach($logData as $hour=>$v){
					$response[getdate(strtotime($day))['weekday']][$hour] = (array) $v;
				}
				
				
			}
			
		}
		
		foreach($response as $day => $v){
			
			$response[$day]["count"] = 0;
			$response[$day]["litres"] = 0;
			
			foreach($v as $c){
				$count = $c['count'];
				$litres = $c['litres'];
				$response[$day]["count"] = $response[$day]["count"] + $count;
				$response[$day]["litres"] = $response[$day]["litres"] + $litres;				
			}
			
		}
		
		return array("response"=>$response);
		
		
	}
	
	/* Todays Water Changes By Hour */
	public static function getFlowTodayByHour(){
		
		$log = (array) static::getWaterFlowLog()->response;
		
		foreach (range(0, 23) as $i) {
			$response[$i] = array('count'=>0,'litres'=>0);
		}
		
		foreach($log as $day=>$logData){
			
			if($day == date("Ymd")){
					foreach($logData as $hour=>$v){
						$response[$hour]['count'] = $v->count;
						$response[$hour]['litres'] = $v->litres;
					}
					
					break;
			}
			
		}
		
		if(!empty($todayLog)){
			$response['response'] = $todayLog;
		}
		
		return array('response'=>$response);
		
	}
	
	/* Status of the flow Daemon Heart Beat */
	public static function getFlowDaemonStatus(){
		
		$daemon = file_get_contents(_WATER_FLOW_DAEMON);
		if(json_decode($daemon)->response->heartbeat < time()-10){
		   $daemonStatus = _COLOUR_RED;
		   $status = false;
		}else{
		   $daemonStatus = _COLOUR_BLUE_SECONDARY;
		   $status = true;
		}	
		
		return array('response'=>array('status'=>$status,'colour'=>$daemonStatus));
		
	}
		
	/* Todays Water Changes Total */
	public static function getFlowTodayTotal(){
		
		$log = static::getFlowTodayByHour()['response'];
		
		$count = 0;
		$litres = 0;
		
		foreach($log as $hour=>$logData){
			
			$count = $count + $logData['count'];
			$litres = $litres + $logData['litres'];
			
		}
		
		$response = array('response'=>array('count'=>$count,'litres'=>$litres));
		
		return $response;
		
	}	
	
	/* Get Water change Schedule as JSON */
	public static function getWaterSchedule(){
	
		$jsonSchedule = file_get_contents(_WATER_SCHEDULE);
		$schedule = json_decode($jsonSchedule);
		
		return array('response'=>$schedule);

	}

	/* Set Water Change Schedule */
	public static function setWaterSchedule(){
				
		if(!empty($_GET['schedule'])){
			
		$schedule = ($_GET['schedule']);
		
			if($schedule){

				$fh = fopen( _WATER_SCHEDULE, 'w' );
				fwrite($fh, $schedule);			
				fclose($fh);
				return array('response'=>'saved');
				
			}
		}
	}
	
	public static function currentLitresCount(){
		
		$litresCount = file_get_contents(_WATER_LITRES_COUNT);
		$litresCount = json_decode($litresCount);

		return $litresCount;
	}
	
	public static function WaterChange($params){
		
		error_reporting(E_ALL ^ E_NOTICE);
			
		$litres = (float) ($params['litres']);
	
		if(empty($litres)){
			return array('response'=>'None or Invalid Litres Given');
		}
		
		$x=0;

		apiSocket::switchOn(array('socket'=>_SOCKET_WATER_CHANGE));
		
		$start = time();
		
		while((time() < $start + _MAX_RUN_WATER_TIME) && ( empty($y) || $y < ($litres - _OVERFLOW))){
			if(static::currentLitresCount()->response->litres){
				$y = (float) static::currentLitresCount()->response->litres;
			}
		}
		
		apiSocket::switchOff(array('socket'=>_SOCKET_WATER_CHANGE));
		apiSocket::switchOff(array('socket'=>_SOCKET_WATER_CHANGE));
		apiSocket::switchOff(array('socket'=>_SOCKET_WATER_CHANGE));		
		
		return array('response'=>array('litres'=>static::currentLitresCount()->response));
		
	}
	
	/* Checks for and dispenses any flow this hour */
	public static function runWaterCron(){
		
		if(static::getFlowDaemonStatus()['response']['status'] == false){
			return false;
		}
	
		$dispensedThisHour = static::getFlowTodayByHour()['response'][date('G')]['litres'];
		
		$thisHour = date("H") . ':00';
		$requiredThisHour = static::getWaterSchedule()['response']->$thisHour;
		
		$remaining = $requiredThisHour - $dispensedThisHour;

		if($remaining < 0) $remaining = 0;
		
		$run = false;
		
		if($remaining > 0){
			
			static::WaterChange(array('litres'=>$remaining));
			$run = true;
			
			/* just to be safe */
			apiSocket::switchOff(array('socket'=>_SOCKET_WATER_CHANGE));
			
		}
		
		$postDispensed = $dispensedThisHour = static::getFlowTodayByHour()['response'][date('G')]['litres'];
		if($remaining > 0){
			$remaining = $requiredThisHour - $postDispensed;
			if($remaining < 0) $remaining = 0;
			
		}
	
		$response = array(
			'atStart' => $dispensedThisHour,
			'atEnd' => $postDispensed,
			'remaining' => $remaining,
			'required' => $requiredThisHour,
			'run' => $run,
		);
		
		return $response;
		
	}
	
	
}
