<?php

/* Uncomment to use this file to declare a weathermap

//File label, must be unique across all maps
$label = 'myuniquelabel';

// Uncomment to use the filename as the label
//$label = str_replace('.php','',basename(__FILE__));

$weathermapper[$label] = [];

// HTML title, doesn't need to be unique
$weathermapper[$label]['title'] = "Network Map (".$label.")";

// Load global grid options
$weathermapper[$label]['grid_opts'] = $grid_opts;

// Redefine grid options by uncommenting individually below

// linear or radial
//$weathermapper[$label]['grid_opts']['layout'] = 'radial';

// LINEAR OPTIONS
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
  // Replace with your hostname pattern
  'regex' => 'myhost[0-9].dc1.*',

  // Row: Defines the order in which this grouping of devices is displayed
  //      Lower number means
  //        Linear: higher on the page
  //          Radial: closer to the center
  'row' => 10
];

// Add another row:
$weathermapper[$label]['search_opts']['hostnames'][] = [
  'regex' => 'myotherhost[0-9].dc1.*',
  'row' => 20
];
*/
?>
