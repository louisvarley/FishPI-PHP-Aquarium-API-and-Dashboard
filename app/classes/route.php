<?php
/**
 * route.php
 *
 * route class for building our routes and getting route titles
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Class
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */

class route {

	/*
	What is routing? Routing is run by classes/route.php (a route) and /app/routes/routes.php (the list of routes to generate)
	Routes are essentially pages. They can either be static or dynamic.
	
	Every route needs to run a controller and associate a view to that controller 
	The Controller is set in the routes file and the default view is determined then by the route. 
	Although controller can overide this by changing the controllers view object before it renders
	
	Static Routes as routes that will not change, such as /mypage or /my-subject/my-page. 
	These are routed to Controllers such as mySubjectController or myPageController
	
	Dynamic Routes may contain parametres within the URL, and are defined in the language file with <params>
	
	such as /profile/<profileid> or /do/<entity>/<action>/
	
	These use matching to find the right route the above cases controllerProfile and controllerDo would be resolved with each param being 
	made available to the controller in an object called params 
	
	
	*/
	public static function isRouteValid() {
		
		global $routes;
		
		if(http_response_code() == 404)return true;

		if(!empty($routes['static']) && in_array(self::uri(), $routes['static']))return true;
		
		if(!empty($routes['dynamic'])){
			foreach($routes['dynamic'] as $route){
				if(self::dynRegexMatch($route)){
					return true;
				}
			}
		}			

		return 0;	
	}
	
	/* Extract URL parts from Dynamic Route */
	public static function extractURLparts(){
		global $routes;
		$urlParts = [];
		if(!empty($routes['dynamic'])){
			foreach($routes['dynamic'] as $route){
				if(self::dynRegexMatch($route)){
					
					$map = preg_split('#/#', $route);
					$result = preg_split('#/#', ltrim($_SERVER['REQUEST_URI'],'/'));

					foreach($map as $i=>$key){
						if($key != $result[$i]){
							$urlParts[str_replace(array('>','<'),'',$key)] = explode("?",$result[$i])[0];
						}
					}
					continue;
				}
			}
			
			return $urlParts;
		}			
	}

	/* Register a new static route. */
	private static function registerRoute($route) {

		global $routes;
		$routes['static'][] = $route;
	}
	
	/* Register a new dynamic route */
	private static function registerDynRoute($route) {

		global $routes;
		$routes['dynamic'][] = $route;
	}	

	/* This method creates dynamic routes. */
	/* To Get Dynamic routes we need to extract the <> elements from the URL */
	public static function dyn($route) {
		
		/* Replaces all <variables> and any resultings double // in route */
		$route = preg_replace(array('/<[^>]*>/m','/\/\//m'),'',$route);
		return $route;
	}

	/* Register the route */
	public static function set($route, $closure) {
		
		$route = ltrim($route,'/');	
		$route = rtrim($route,'/');

		if ($_SERVER['REQUEST_URI'] == _URLBASE.$route) {	
			self::registerRoute($route);
			$closure->__invoke();
			
		} else if (explode('?', $_SERVER['REQUEST_URI'])[0] == _URLBASE.$route) {	
			self::registerRoute($route);
			$closure->__invoke();
			
		} else if(self::dynRegexMatch($route)) { /* Matches a Dynamic Route */
				self::registerDynRoute($route);
				$closure->__invoke();
		}
	}
	
	public static function dynRegexMatch($route){
		$route = ltrim($route,'/');
		if(preg_match("/<(.*)>/",$route)){
			$dynMatch = "/" . str_replace('/','\/',preg_replace(array('/<[^>]*>/m','/\/\//m'),'(.*)',$route)) . "/";
			return preg_match($dynMatch,$_SERVER['REQUEST_URI']);
		}
	}
  
	/* This method will check if the URL has a valid route according to the routing array */
	public static function validRequest() {

		// Global Routing Array
		global $routes;
		
		if (!in_array(self::uri(), $routes)) {
			return false;
		} else {
			return true;
		}
	}
	
	/* Displays the Current URI either with or without Params, this strips leading /*/
	public static function uri($includeParam=false,$full=false){
		
		$uri = ltrim($_SERVER['REQUEST_URI'],'/');
		
		if($includeParam){
			return $uri;
		}else{
			return explode('?',$uri)[0];
		}
	}
	
	/* Will Camel Case a route */
	public static function ccRoute($route){
		$route = str_replace('-', '', ucwords($route, '-'));
		return $route;
	} 

	/* Fetch a controller class */
	public static function getControllerClass($route){
		return 'controller'.self::ccRoute($route);
	}

	/* Fetch a view class */
	public static function getViewClass($route){
		return 'view'.self::ccRoute($route);	  
	}

	/* Fetch a controller File */
	public static function getControllerFile($route){
		return _CONTROLLERS.$route.'.php';  
	}

	/* Fetch a view File */
	public static function getViewFile($route){
		return _VIEWS.$route.'.php';
	}

	/* Does the Controller File Exist? */
	public static function controllerExists($route){
		return file_exists(self::getControllerFile($route)) ? true : false;
	}

	/* Does the View File Exist? */
	public static function viewExists($route){
		return file_exists(self::getViewFile($route)) ? true : false;
	}  

	/* Does the Controller Class Exist? */
	public static function controllerClassExist($route){
		return class_exists(self::getControllerClass($route)) ? true : false;
	}

	/* Does the View Class Exist? */
	public static function viewClassExist($route){
		return class_exists(self::getViewClass($route)) ? true : false;
	}  	
	 
}
