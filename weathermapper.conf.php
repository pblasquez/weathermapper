<?php

/**
 * Weathermapper configuration file
 *
 * This file contains all global variables
 *
 * @package     weathermapper
 * @author      Paul Blasquez <pblasquez@gmail.com>
 * @copyright   2016 Paul Blasquez
 *
 */

// LibreNMS Section

// LibreNMS directory
$install_dir = '/opt/librenms';

// LibreNMS config
require_once($install_dir.'/config.php');

// LibreNMS Weathermap plugin directory
$weathermap_dir  = $install_dir.'/html/plugins/Weathermap';

// Where to write weathermap .conf files
$output_dir = $weathermap_dir.'/configs';

// Weathermap HTML and PNG file location, relative to the weathermap plugin directory
$map_dir = 'output';

// Router image file, relative to the weathermap plugin directory
$router_image = 'images/Router.png';


// Weathermapper section

// Weathermapper base directory
$weathermapper_dir = __DIR__; 

// Weathermapper library
require_once($weathermapper_dir.'/includes/weathermapper.inc.php');

// Default Grid Options
// These options will be used unless you specify new ones

$grid_opts =  [
  // top, left, or radial
  //
  // top - Top to bottom orientation
  // left - Left to right orientation
  // radial - Circular orientation
  'layout' => 'radial',

  // Only used for top/left layouts
  'colsize' => 200, // horizontal pixels between devices
  'colmargin' => 100, // horizontal pixels from edge of canvas
  'rowsize' => 100, // vertical pixels between devices
  'rowmargin' => 250, // vertical pixels from edge of canvas

  // Only used for radial layouts
  'radius' => 400 // pixels between radial sets
];

// ifTypes to allow (can be overriden in conf.d)
$match_iftypes = ['ethernetCsmacd','ieee8023adLag','pppMultilinkBundle','gigabitEthernet'];

// Load conf.d files
if (is_dir($weathermapper_dir.'/conf.d')) {
  foreach (glob($weathermapper_dir.'/conf.d/*.php') as $inc) {
    include($inc);
  }
}
