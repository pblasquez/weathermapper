<?php

/**
 * Weathermapper
 *
 * This tools automatically creates weathermaps
 *
 * @package     weathermapper
 * @author      Paul Blasquez <pblasquez@gmail.com>
 * @copyright   2016 Paul Blasquez
 *
 */


// Load config
chdir(dirname($argv[0]));
require_once("./weathermapper.conf.php");

// Connect to DB using LibreNMS credentials
$dbh = pdo_connect(
  $config['db_host'],
  $config['db_name'],
  $config['db_user'],
  $config['db_pass']
);

// Create weathermaps
foreach ($weathermapper as $k => $v) {
    // Evaluate search options and return devices matched + layout grid
    list($devices,$layout) = get_device_list($dbh,$v['search_opts']);

    // Generate link information between devices
    $link_opts=[];
    $links = get_link_matrix(
      $dbh,
      $devices,
      $link_opts,
      $match_iftypes
    );

    // Make sure links are found, otherwise warn
    if (empty($links)) {
      echo "No links found for ".$k.", please ensure LibreNMS auto-discovery is enabled and working.\n";
      continue;
    } else {
      echo "Generating weathermap configuration to ".$output_dir."/".$k.".conf\n";
    }

    // Automatically create config file starting with header and nodes
    $map_config = create_node_config(
      $devices,
      $layout,
      $v['grid_opts'],
      $v['title'],
      $k,
      $map_dir,
      $router_image
    );

    // Concatenate link configs
    $map_rrd_dir = '.';
    if(!empty($config['rrd_dir'])) {
      $map_rrd_dir = $config['rrd_dir'];
    }
    $map_config .= create_link_config($links,$map_rrd_dir);

    // Write to file
    $file = $output_dir."/".$k.".conf";
    $fh = fopen($file, "w") or die("Unable to open ".$file."!");
    fwrite($fh,$map_config);
    fclose($fh);
}
// Close DB connection
$dbh=null;
?>
