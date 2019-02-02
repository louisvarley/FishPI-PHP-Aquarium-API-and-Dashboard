<?php
/**
 * globals.php
 *
 * Any Global variables, defines and methods we wish to setup and have available across the entire app
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Config
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */
 
/* Get Domain and Protocol of the site */
define('_URL_',stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'].'/');

/* This Sites API */
define('_LINK_API','api/<class>/<action>');

/* 404 */
define('_LINK_404','not-found');

/* Root */
define('_LINK_ROOT','/');

/* Root */
define('_LINK_DASHBOARD','dashboard');

/* Load Autoloader function */
require(_APP . 'autoloader.php');

/* Require the helper functions file */
require(_APP . 'functions.php');
