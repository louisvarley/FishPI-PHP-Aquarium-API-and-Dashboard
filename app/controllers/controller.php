<?php
/**
 * autolocontrollerad.php
 *
 * the base controller class
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Controllers
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */

 /* 
	This is the base controller class, all controller classes extend this however, the routes class loads this first before it knows more about the page
	Once the base controller has established the file for the view and controller and its respective class names it will load that. 
	At this point any further action on this page is conducted by that class 
*/

class controller {

  /* Fetch is called by the routing, it will establish the correct view and controller to load and start an instance of that without naming it in routes */
  /* If any classes, files or routes fail here, the user is redirected to a 404 page */    
  /* If all is good and well, once the route is established, $view and $controller will contain the correct view and controller for that route and an instance be available to use */
    
  public function __construct($view = null){

	  if(isset($view)){
		  $this->view = $view;
	  }
	  
  }
    
  public static function fetch($route){

		
	  if(route::isRouteValid() && route::controllerExists($route) && route::viewExists($route)){
			
			require_once(route::getControllerFile($route));
			require_once(route::getViewFile($route));
			
			global $view;
			global $controller;
		
			/* Performs a Honeypot check */
			
			if(route::viewClassExist($route)){
				$viewClass = route::getViewClass($route);

				
			} else {
				/* Route has no View Class */
				self::throw404();
			}			
					
			/* This now sets $controller to the controller we wish to use */
			if(route::controllerClassExist($route)){
				$controllerClass = route::getControllerClass($route);
				$controller = new $controllerClass($view);		
				$controller->route = $route;
				$controller->view = new $viewClass;
				$controller->view->route = $route;
				
				/* Extracts URL Parts if this is a dynamic route */
				$controller->urlParts = route::extractURLparts();

				/* This now sets $view to the view we wish to use */
				$controller->onload();		
			} else {
				/* Route has no Controller Class */
				self::throw404();
			}
			
			

	  } else {
			/* Controller or view file does no exist */
			self::throw404();
	  }
	   
  }

	/* This onload function is called when the controller is actually started */
	/* This base class onLoad does nothing */
	public function onLoad(){

	  if(isset($this->view)){
		 $this->view->render();
	  }
	  
	}
	
 	/* Redirect to 404 route, page not found */
	public static function throw404(){
	
		header("HTTP/1.0 404 Not Found");
		route::set(route::uri(), function() {
			controller::fetch('page-not-found');
		});
	 }
	 
	 /* Language Changed, Find new route for this language */
	 public static function changedLanguageCheck(){
		 
		 if(isset($_POST['locale'])){
			 $foundRoute = findLanguageDefine(route::uri(),_LOCALE_PREV,_LOCALE);
			 if(!empty($foundRoute)){
				 self::route($foundRoute);
			 }
		 }
		 
	 }
	 
	 /* Redirect to another route, can take an array or params to set */
	public static function route($route,$params=null){
		$urlParams = '';
		
		if($params){
			$urlParams = '';
			foreach($params as $key => $param){
				$urlParams = $key . '=' . $param . "&";
				
			}
			$urlParams = '?' . rtrim($urlParams,'&');
		}
		
		header("Location: " . _URL_ . $route . $urlParams);
		exit;
	} 
	
	 /* Redirect to another URL but via the redirect page unless its whitelisted in config */	
	public static function redirect($uri){
		self::route(_LINK_REDIRECT,array('redirect'=>$uri));
		exit;

	}

	 /* Redirect to another URL directly without using redirect and whitelisting */	
	public static function hardRedirect($uri){
		header("Location: " . $uri);
		exit;
	}	
}
