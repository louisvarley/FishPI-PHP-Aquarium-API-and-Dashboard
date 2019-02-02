<?php
/**
 * api-permissions.php
 *
 * Get list array of ID's and names of available permissions to call using the can endpoint
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage models
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */


class apiDefinition extends apiPublic {
	
	/* list all available site permissions which can be passed to the can endpoint */
	public static function list(){

		$definition = [];
	
		foreach (glob(_MODELS . "*api*.php") as $filename)
		{
			require_once $filename;
		}		


		foreach(get_declared_classes() as $class){
			if(is_subclass_of($class, 'apiPrivate')) $definition[$class] = array();
			if(is_subclass_of($class, 'apiPublic')) $definition[$class] = array();			
		}	
		
		foreach($definition as $class => $value){
			$methods = get_class_methods($class);		
			foreach($methods as $method){
				if($method == 'call')continue;
				if($method == 'apiKeyCheck')continue;				
				$definition[$class][$method]['url'] = _URL_ . 'api/' . str_replace('api','',$class) . '/' . $method;
				$reflect = new ReflectionMethod($class, $method);
				$comments = $reflect->getDocComment();
				$params = $reflect->getParameters();
				$definition[$class][$method]['params'] = [];
				foreach($params as $param){
					$definition[$class][$method]['params'][] = $param->getDefaultValue();
				}
				
				if(is_subclass_of($class, 'apiPrivate')){
					$definition[$class][$method]['params'][] = array('name'=>'key','method'=>'GET','description'=>'Your API Key','required'=>true);
				}
				
				
				
		
			}
		}		
			
		return $definition;
		
	}
	
	
}