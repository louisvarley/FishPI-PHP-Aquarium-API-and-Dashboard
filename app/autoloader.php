<?php
/**
 * autoloader.php
 *
 * Function which auto loads classes 
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Autoloader
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


spl_autoload_register(function($className) {
	
	  $className = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $className));
	  $className = str_replace(array('controller-','view-','model-'),'',$className);
	  
	  if(file_exists(_CLASSES.$className.'.php')){require_once(_CLASSES.$className.'.php');}
	  if(file_exists(_CONTROLLERS.$className.'.php')){require_once(_CONTROLLERS.$className.'.php');}
	  if(file_exists(_MODELS.$className.'.php')){require_once(_MODELS.$className.'.php');}	  
	  if(file_exists(_VIEWS.$className.'.php')){require_once(_VIEWS.$className.'.php');}	  
	   
});