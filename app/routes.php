<?php
/**
 * routes.php
 *
 * Routes requests to the correct controller, any pages must have a route setup here for them to not return a 404
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage View
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */

 /* Our Routes Array */
$routes = array();
 
 /* Every static route gets set here, 
 
 /* Root Directory / */
route::set('/', function() {
	controller::fetch('root');
});

/* Our 404 - All Invalid Routes are sent here, the URL is not used */
route::set(_LINK_404, function() {
	controller::fetch('page-not-found');
});

route::set(_LINK_API, function() {
    controller::fetch('api');
});

route::set(_LINK_DASHBOARD, function() {
    controller::fetch('dashboard');
});

if(!route::isRouteValid()){
	controller::throw404();
}; 
