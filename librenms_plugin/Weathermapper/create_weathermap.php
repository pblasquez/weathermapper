<?php

if (
!empty($_GET['install'])
&& !empty($_GET['wname'])
&& !empty($_GET['design'])
&& !empty($_GET['dev_select'])
&& (
    (
     (
       $_GET['design'] == 'top'
       || $_GET['design'] == 'left'
     )
     && !empty($_GET['colsize'])
     && !empty($_GET['colmargin'])
     && !empty($_GET['rowsize'])
     && !empty($_GET['rowmargin'])
    )
    ||
    (
     $_GET['design'] == 'radial'
     && !empty($_GET['radius'])
    )
   )
&& (
     (
       !empty($_GET['host_regex'])
       && !empty($_GET['host_layer'])
     )
     ||
     (
       !empty($_GET['group_id'])
       && !empty($_GET['group_layer'])
     )
     ||
     (
       !empty($_GET['loc_regex'])
       && !empty($_GET['loc_layer'])
     )
   ) 
) {
  $label = str_replace('.conf','',$_GET['wname']);
  $conf_fn = $_GET['install']."/conf.d/".$label.".php";
  if(!is_writable(dirname($conf_fn))) {
    $thisuser = posix_getpwuid(posix_geteuid());
    $thisgroup = posix_getgrgid($thisuser['gid']);
    echo '<pre>
The weathermapper configuration directory is not writable! You should try running "chown -R '.$thisuser['name'].':'.$thisgroup['name'].' '.dirname($conf_fn).'"
</pre>';
    exit;
  }
  $body = '<?php
$label = "'.$label.'";
';
  $body .= <<<'EOT'
$weathermapper[$label] = [];
$weathermapper[$label]['title'] = "Network Map (".$label.")";
$weathermapper[$label]['grid_opts'] = [];
$weathermapper[$label]['search_opts'] = [];
$weathermapper[$label]['search_opts']['types'] = [];

EOT;
  $body .= '$weathermapper[$label][\'grid_opts\'][\'layout\'] = \''.$_GET['design']."';
";
  if ($_GET['dev_select'] == 'radial') {
    $body .= '$weathermapper[$label][\'grid_opts\'][\'radius\'] = '.$_GET['radius'].';
';
  } else {
    $body .= '$weathermapper[$label][\'grid_opts\'][\'colsize\'] = '.$_GET['colsize'].";
";
    $body .= '$weathermapper[$label][\'grid_opts\'][\'colmargin\'] = '.$_GET['colmargin'].";
";
    $body .= '$weathermapper[$label][\'grid_opts\'][\'rowsize\'] = '.$_GET['rowsize'].";
";
    $body .= '$weathermapper[$label][\'grid_opts\'][\'rowmargin\'] = '.$_GET['rowmargin'].";
";
  }
  if (!empty($_GET['host_regex'])) {
    $body .= '$weathermapper[$label][\'search_opts\'][\'types\'][] = \'hostname\';
';
    $body .= '$weathermapper[$label][\'search_opts\'][\'hostnames\'] = [];
';
    foreach ($_GET['host_regex'] as $k => $v) {
       $body .= '$weathermapper[$label][\'search_opts\'][\'hostnames\'][] = [
    \'regex\' => \''.$_GET['host_regex'][$k].'\',
    \'row\' => '.$_GET['host_layer'][$k].'
];
';
    }
  }
  if (!empty($_GET['group_id'])) {
    $body .= '$weathermapper[$label][\'search_opts\'][\'types\'][] = \'group\';
';
    $body .= '$weathermapper[$label][\'search_opts\'][\'groups\'] = [];
';
    foreach ($_GET['group_id'] as $k => $v) {
       $body .= '$weathermapper[$label][\'search_opts\'][\'groups\'][] = [
    \'group\' => \''.$_GET['group_id'][$k].'\',
    \'row\' => '.$_GET['group_layer'][$k].'
];
';
    }
  }
  if (!empty($_GET['loc_regex'])) {
    $body .= '$weathermapper[$label][\'search_opts\'][\'types\'][] = \'location\';
';
    $body .= '$weathermapper[$label][\'search_opts\'][\'hostnames\'] = [];
';
    foreach ($_GET['loc_regex'] as $k => $v) {
       $body .= '$weathermapper[$label][\'search_opts\'][\'hostnames\'][] = [
    \'regex\' => \''.$_GET['loc_regex'][$k].'\',
    \'row\' => '.$_GET['loc_layer'][$k].'
];
';
    }
  }
  $conf_file = fopen($conf_fn, "w") or die("Could not open $conf_fn for write!");
  fwrite($conf_file, $body);
  fclose($conf_file);
  echo "Success! New config was written to $conf_fn. Please run weathermapper from the CLI to create your new weathermap!<br />
<button onclick=\"window.history.go(-1)\">Go Back</button>
";
} else {
  echo 'Please fill out all fields!';
}
