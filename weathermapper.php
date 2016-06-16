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
      $link_opts
    );

    // Make sure links are found, otherwise warn
    if (empty($links)) {
      echo "No links found for ".$k.", please ensure LibreNMS auto-discovery is enabled and working.\n";
      continue;
    } else {
      echo "Generating weathermap configuration to ".$output_dir."/".$k.".conf\n";
    }

    // Automatically create config file starting with header and nodes
    $config = create_node_config(
      $devices,
      $layout,
      $v['grid_opts'],
      $v['title'],
      $k,
      $map_dir,
      $router_image
    );

    // Concatenate link configs
    $config .= create_link_config($links);

    // Write to file
    $file = $output_dir."/".$k.".conf";
    $fh = fopen($file, "w") or die("Unable to open ".$file."!");
    fwrite($fh,$config);
    fclose($fh);
}
// Close DB connection
$dbh=null;
?>
