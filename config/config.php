<?php
/**
 * configuration.php
 *
 * this file contains all configuration defines
 *
 * @author     Louis Varley <louisvarley@googlemail.com>
 * @copyright  2018 Landscape Institute
 * @subpackage Config
 * @license    http://www.php.net/license/3_1.txt  PHP License 3.1
 * @link       https://github.com/landscapeInstitute/my-landscapeinstitute-org
 */

 
/******************************************************************************************************************
* General - General Settings
/******************************************************************************************************************/
 
/******************************************************************************************************************
* Directories - Locations of libaries and directories classes can be found etc
/******************************************************************************************************************/

/* Root URL */
define('_URLBASE','/');

/* FS Root */
define('_FS_BASE','/var/www/fishpi/');

/* Scripts */
define('_SCRIPTS',_BASE . 'scripts/');

/* App DIR */
define('_APP',_BASE . 'app/');

/* Cache DIR */
define('_CACHE',_BASE . 'cache/');

/* Static DIR */
define('_STATIC',_BASE . 'static/');

/* Static URL */
define('_STATIC_URL',_URLBASE . 'static/');

/* MODELS DIR */
define('_MODELS',_APP . 'models/');

define('_DEFAULT_PAGE_TITLE','FishPI');

/* CONTROLLERS DIR */
define('_CONTROLLERS',_APP . 'controllers/');

/* CLASSES DIR */
define('_CLASSES',_APP . 'classes/');

/* VIEWS DIR */
define('_VIEWS',_APP . 'views/');

/* WIDGETS */
define('_WIDGETS_JSON',_BASE . 'config/widgets.json');

/* COLOURS */
define('_COLOUR_BLUE_PRIMARY','#4267b2');
define('_COLOUR_BLUE_SECONDARY','#0078d7');
define('_COLOUR_GREEN','');
define('_COLOUR_RED','#a5503b');

/* Overflow from value off and still dispencing */
define('_OVERFLOW',0.3);

/* Max time water will flow before auto shut off */
define('_MAX_RUN_WATER_TIME',60);

/* WIDGETS */
define('_LIGHTING_INTENSITY_JSON',_BASE . 'config/lighting.intensity.json');

define('_LIGHTING_SCHEDULE_1_JSON',_BASE . 'config/channel.1.schedule.json');

define('_LIGHTING_SCHEDULE_2_JSON',_BASE . 'config/channel.2.schedule.json');

define('_LIGHTING_SCHEDULE_3_JSON',_BASE . 'config/channel.3.schedule.json');

define('_LIGHTING_SCHEDULE_4_JSON',_BASE . 'config/channel.4.schedule.json');

define('_LIGHTING_PINS_JSON',_BASE . 'config/pins.json');

define('_LIGHTING_OVERRIDE_JSON',_BASE . 'config/lighting.override.json');

define('_WEATHER_STATUS',_BASE . 'config/weather.status.json');

define('_TEMPERATURE_LOG',_BASE . 'config/temperature.log.json');

define('_WATER_FLOW',_BASE . 'config/flow.json');

define('_WATER_CHANGE_LOG',_BASE . 'config/water.change.log.json');

define('_WATER_SCHEDULE',_BASE . 'config/water.change.schedule.json');

define('_WATER_FLOW_DAEMON',_BASE . 'config/flow.daemon.json');

define('_WATER_LITRES_COUNT',_BASE . 'config/flow.litres.json');

define('_WATER_FLOW_STATUS',_BASE . 'config/flow.status.json');

define('_WATER_FLOW_LOG',_BASE . 'config/flow.log.json');

define('_LIGHTING_LIGHTNING_CHANNEL','4');

define('_WEATHER_SUN','<i class="fas fa-sun"></i> Sunny');

define('_WEATHER_SUN_RISE','<i class="fas fa-sun-haze"></i> Sun Rising');

define('_WEATHER_SUN_SET','<i class="fas fa-sun-haze"></i> Sun Setting');

define('_WEATHER_MOON','<i class="fas fa-moon"></i> Night');

define('_WEATHER_STORM','<i class="fas fa-thunderstorm"></i> Storm');

define('_WEATHER_CLOUD','<i class="fas fa-cloud"></i> Cloudy');

define('_WEATHER_RAIN','<i class="fas fa-cloud-rain"></i> Raining');

/* Sockets */

define('_SOCKET_WATER_CHANGE',1);
define('_SOCKET_RAIN_BAR',2);
define('_SOCKET_HEATER',3);




