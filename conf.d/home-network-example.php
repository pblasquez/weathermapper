<?php

//File label, must be unique across all maps
$label = 'home';

// Uncomment to use the filename as the label
$label = str_replace('.php','',basename(__FILE__));

$weathermapper[$label] = [];

// HTML title, doesn't need to be unique
$weathermapper[$label]['title'] = "Network Map (".$label.")";

// Load global grid options
$weathermapper[$label]['grid_opts'] = $grid_opts;

// Redefine grid options by uncommenting individually below

// top, left, or radial
//
// top - Top to bottom orientation
// left - Left to right orientation
// radial - Circular orientation
$weathermapper[$label]['grid_opts']['layout'] = 'radial';

// TOP/LEFT OPTIONS
//$weathermapper[$label]['grid_opts']['colsize'] = 200; // horizontal pixels between devices
//$weathermapper[$label]['grid_opts']['colmargin'] = 100; // horizontal pixels from edge of canvas
//$weathermapper[$label]['grid_opts']['rowsize'] = 100; // vertical pixels between devices
//$weathermapper[$label]['grid_opts']['rowmargin'] = 250; // vertical pixels from edge of canvas

// RADIAL OPTIONS
//$weathermapper[$label]['grid_opts']['radius'] = 300; // pixels between radial sets

// Device matching options
$weathermapper[$label]['search_opts'] = [];
  // Types:
  //   hostname - regex match on hostnames
  //   group - exact match on group id
  //   location - regex match on location strings
$weathermapper[$label]['search_opts']['types'] = ['hostname'];

$weathermapper[$label]['search_opts']['hostnames'] = [];

$weathermapper[$label]['search_opts']['hostnames'][] = [
  'regex' => 'r7000',
  'row' => 10
];
$weathermapper[$label]['search_opts']['hostnames'][] = [
  'regex' => 'nas',
  'row' => 10
];
$weathermapper[$label]['search_opts']['hostnames'][] = [
  'regex' => '.*',
  'row' => 20
];
