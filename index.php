<?php
/**
 * index.php
 *
 * Main Index file, begins load
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage View
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */
 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
 
/* Set BASE DIR */
define('_BASE',dirname(__FILE__) . '/');

/* Include configuration file */
require_once( _BASE . 'config/config.php' );

/* Includes globals File */
require_once( _APP . 'globals.php' );

/* Include Routes File */
require_once( _APP . 'routes.php' );

