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


class apiSocket extends apiPublic {
	
	
	static $socket1_ipAddress = '10.25.0.204';
	static $socket2_ipAddress = '10.25.0.149';
	static $socket3_ipAddress = '10.25.0.126';	
	static $socket4_ipAddress = '10.25.0.104';		
		
	static $socket1_macAddress = 'ac-cf-23-44-cc-2c';
	static $socket2_macAddress = 'ac-cf-23-97-6c-b2';
	static $socket3_macAddress = 'ac-cf-23-8d-4d-ae';
	static $socket4_macAddress = 'ac-cf-23-8d-0b-56';	
	
	public static function switchOn($params){

		$localIPAddress = static::${'socket' . $params['socket'] . '_ipAddress'};
		$macAddress = static::${'socket' . $params['socket'] . '_macAddress'};
	
		//$macAddress = static::macAddress; //Keep hyphenated
		//$localIPAddress = static::localIPAddress;

		$port = 10000;
		$twenties = '202020202020';
		$ma = implode('', explode('-', $macAddress));
		$maReverse = implode('', array_reverse(explode('-', $macAddress)));
		$subscribe = pack('H*', '6864001e636c' . $ma . $twenties . $maReverse . $twenties);
		$on = pack('H*', '686400176463' . $ma . $twenties . '0000000001');
		$off = pack('H*', '686400176463' . $ma . $twenties . '0000000000');
		$socket = socket_create(AF_INET, SOCK_DGRAM, 0);
		socket_sendto($socket, $subscribe, strlen($subscribe), 0, $localIPAddress, $port);
		sleep(1);
		socket_sendto($socket, $on, strlen($on), 0, $localIPAddress, $port);
		sleep(1);
		socket_sendto($socket, $on, strlen($on), 0, $localIPAddress, $port);
		sleep(1);
		socket_sendto($socket, $on, strlen($on), 0, $localIPAddress, $port);		
		socket_close($socket);		
		
		return array('status'=>'on');
		
	}

	public static function switchOff($params){

		$localIPAddress = static::${'socket' . $params['socket'] . '_ipAddress'};
		$macAddress = static::${'socket' . $params['socket'] . '_macAddress'};
	
		$port = 10000;
		$twenties = '202020202020';
		$ma = implode('', explode('-', $macAddress));
		$maReverse = implode('', array_reverse(explode('-', $macAddress)));
		$subscribe = pack('H*', '6864001e636c' . $ma . $twenties . $maReverse . $twenties);
		$on = pack('H*', '686400176463' . $ma . $twenties . '0000000001');
		$off = pack('H*', '686400176463' . $ma . $twenties . '0000000000');
		$socket = socket_create(AF_INET, SOCK_DGRAM, 0);
		socket_sendto($socket, $subscribe, strlen($subscribe), 0, $localIPAddress, $port);
		sleep(1);
		socket_sendto($socket, $off, strlen($off), 0, $localIPAddress, $port);
		sleep(1);
		socket_sendto($socket, $off, strlen($off), 0, $localIPAddress, $port);
		sleep(1);
		socket_sendto($socket, $off, strlen($off), 0, $localIPAddress, $port);		
		socket_close($socket);

		return array('status'=>'off');		
		
	}
	
}