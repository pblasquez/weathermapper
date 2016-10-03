<?php

/**
 * Weathermapper Library
 *
 * These functions assist in the automatic creation of weathermaps.
 *
 * @package     weathermapper
 * @author      Paul Blasquez <pblasquez@gmail.com>
 * @copyright   2016 Paul Blasquez
 *
 */

// Connect to DB
function pdo_connect($host, $dbname, $user, $pass) {
  try {
    $dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
  }
  catch (PDOException $e) {
    die ($e->getMessage());
  }
  return $dbh;
}

function get_device_list($dbh,$search_opts) {
  // Searches allowed:
  // Hostname regex
  // Group id 
  // Location regex
  $matches=[];
  $layout=[];
  $devices=[];
  $device_arr=[];
  foreach ($search_opts['types'] as $type) {
    switch($type) {
      case 'hostname':
        if (array_key_exists('hostnames',$search_opts)) {
          $device_list=[];
          $sql = 'SELECT device_id, hostname FROM devices';
          $sth = $dbh->query($sql);
          $sth->setFetchMode(PDO::FETCH_ASSOC);
          while($row = $sth->fetch()) {
            $device_list[] = $row['hostname'];
            $device_arr[$row['hostname']] = $row['device_id'];
          }
          foreach($search_opts['hostnames'] as $k => $v) {
            $matches = preg_grep('/'.$v['regex'].'/', $device_list);
            foreach($matches as $match) {
              if(!array_key_exists($match, $devices)) {
                $layout[$v['row']][] = $match;
	        $devices[$match] = $device_arr[$match];
              }
            }
          }
        }
      case 'group':
        if (array_key_exists('groups',$search_opts)) {
          foreach($search_opts['groups'] as $k => $v) {
	    $device_list=[];
	    $sql = '
              SELECT
                device_id,
                hostname
              FROM
                devices
              WHERE
                device_id in (
                  SELECT DISTINCT
                    device_id
                  FROM
                    device_group_device
                  WHERE
                    device_group_id=?
                )';
	    $sth = $dbh->prepare($sql);
            $sth->bindParam(1, $v['group']);
            $sth->execute();
	    $sth->setFetchMode(PDO::FETCH_ASSOC);
            $matches=[];
	    while($row = $sth->fetch()) {
              $matches[] = $row['hostname'];
              $device_arr[$row['hostname']] = $row['device_id'];
            }
	    foreach($matches as $match) {
              $layout[$v['row']][] = $match;
	      $devices[$match] = $device_arr[$match];
	    }
          }
        }
      // UNTESTED!
      case 'location':
        if (array_key_exists('locations',$search_opts)) {
          foreach($search_opts['locations'] as $k => $v) {
            $device_list=[];
            $sql = 'SELECT device_id, hostname, location FROM devices';
            $sth = $dbh->query($sql);
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            while($row = $sth->fetch()) {
              $device_list[$row['location']][] = $row['hostname'];
              $device_arr[$row['hostname']] = $row['device_id'];
            }
            foreach($device_list as $k2 => $v2) {
              if(preg_match('/'.$v['regex'].'/', $k2)) {
                $matches = array_merge($matches, $v2);
              }
            }
	    foreach($matches as $match) {
              $layout[$v['row']][] = $match;
	      $devices[$match] = $device_arr[$match];
	    }
          }
        }
        break;
    }
  }
  return [$devices,$layout];
}

// auto-create a node grid, default size of 1080P
function auto_node_layout($devices, $layout, $grid_opts, $width=1980, $height=1080) {
  $grid_dict=[];
  ksort($layout, SORT_NUMERIC);
  switch($grid_opts['layout']) {
    case 'top':
      $y = $grid_opts['rowmargin'];
      foreach ($layout as $k => $v) {
        $col=0;
	$x = $width/2 + $grid_opts['colmargin']/2;
        $colcount=count($v);
        if($colcount % 2 == 0) {
          $justify=0;
        } else {
          $x -= $grid_opts['colsize']/2;
          $justify=1;
        }
        natcasesort($v);
        foreach($v as $device) {
          $col++;
          $grid_dict[$device]['x'] = round($x);
          $grid_dict[$device]['y'] = round($y);
          if ($justify==0) {
            $x -= $grid_opts['colsize'];
            $justify=1;
          } else {
            $x += $justify * $col * $grid_opts['colsize'];
            $justify *= -1;
          }
        }
        $y += $grid_opts['rowsize'];
      }
      break;
    case 'left':
      $x = $grid_opts['rowmargin'];
      foreach ($layout as $k => $v) {
        $col=0; 
	$y = $height/2 + $grid_opts['colmargin']/2;
        $rowcount=count($v);
        if($rowcount % 2 == 0) {
          $justify=0;
        } else {
          $y -= $grid_opts['colsize']/2;
          $justify=1;
        }
        natcasesort($v);
        foreach($v as $device) {
          $col++;
          $grid_dict[$device]['x'] = round($x);
          $grid_dict[$device]['y'] = round($y);
          if ($justify==0) {
            $y -= $grid_opts['colsize'];
            $justify=1;
          } else {
            $y += $justify * $col * $grid_opts['colsize'];
            $justify *= -1;
          }
        }
        $x += $grid_opts['rowsize'];
      }
      break;
    case 'radial':
      $x_center=round($width/2);
      $y_center=round($height/2);
      $radius=$grid_opts['radius'];
      foreach ($layout as $k => $v) {
        $points = count($v);
        $slice = 2*pi()/$points;
        natcasesort($v);
        if($points >= 2) {
          $angle=0;
          foreach($v as $device) {
            // X is Adjacent, Y is Opposite
            // $radius is Hypotenuse
            $x = $x_center + ($radius * cos($angle));
            $y = $y_center + ($radius * sin($angle));
	    $grid_dict[$device]['x'] = round($x);
	    $grid_dict[$device]['y'] = round($y);
            $angle += $slice;
          }
	} else {
          foreach($v as $device) {
	    $grid_dict[$device]['x'] = round($x_center);
	    $grid_dict[$device]['y'] = round($y_center);
          }
	}
        $radius += $grid_opts['radius'];
      }
      break;
  }
  return $grid_dict;
}
//create node config
function create_node_config($devices,$layout,$grid_opts,$title,$label,$map_dir,$router_image) {
  $max_row = 0;
  foreach($layout as $k => $v) {
    if(count($v) > $max_row) { $max_row = count($v); }
  }
  switch($grid_opts['layout']) {
    case 'top' :
      $width=($max_row-1) * $grid_opts['colsize'] + ($grid_opts['colmargin']);
      $height=(count(array_unique(array_keys($layout)))-1) * $grid_opts['rowsize'] + $grid_opts['rowmargin'] * 2;
      break;
    case 'left':
      $width=(count(array_unique(array_keys($layout)))-1) * $grid_opts['rowsize'] + $grid_opts['rowmargin'] * 2;
      $height=($max_row-1) * $grid_opts['colsize'] + ($grid_opts['colmargin']);
      break;
    case 'radial' :
      $height=count(array_unique(array_keys($layout)))*$grid_opts['radius'] * 2 + $grid_opts['radius']/2;
      $width=$height;
      break;
  }
  $config = "# Automatically generated by weathermapper.

FONTDEFINE 8 /usr/share/fonts/bitstream-vera/Vera.ttf 8
FONTDEFINE 9 /usr/share/fonts/bitstream-vera/VeraBd.ttf 8
FONTDEFINE 10 /usr/share/fonts/bitstream-vera/Vera.ttf 10
FONTDEFINE 11 /usr/share/fonts/bitstream-vera/VeraBd.ttf 11
FONTDEFINE 12 /usr/share/fonts/bitstream-vera/Vera.ttf 12
FONTDEFINE 14 /usr/share/fonts/bitstream-vera/VeraBd.ttf 14

WIDTH ".$width."
HEIGHT ".$height."
HTMLSTYLE overlib
KEYFONT 100
TITLE ".$title."
HTMLOUTPUTFILE ".$map_dir."/".$label.".html
IMAGEOUTPUTFILE ".$map_dir."/".$label.".png

KEYPOS DEFAULT 1 1 Traffic Load
KEYSTYLE  DEFAULT horizontal 250
KEYTEXTCOLOR 0 0 0
KEYOUTLINECOLOR 0 0 0
KEYBGCOLOR 255 255 255
BGCOLOR 255 255 255
TITLECOLOR 0 0 0
TIMECOLOR 0 0 0
SCALE DEFAULT 0    0    211 211 211
SCALE DEFAULT 0    1    135 206 250
SCALE DEFAULT 1    10     0   0 255
SCALE DEFAULT 10   40    34 139  34
SCALE DEFAULT 40   70   252 189   0
SCALE DEFAULT 70   100  255   0   0

SET key_hidezero_DEFAULT 1

# End of global section

# TEMPLATE-only NODEs:
NODE DEFAULT
	LABELFONT 9
	MAXVALUE 100


# TEMPLATE-only LINKs:
LINK DEFAULT
	WIDTH 3
	BWOUTLINECOLOR none
	BWLABEL none
	BANDWIDTH 10G


# regular NODEs:
";
  $grid_dict = auto_node_layout($devices, $layout, $grid_opts, $width, $height);
  foreach($grid_dict as $k => $v) {
    $shortname = shortname($k);
    $config .= "NODE ".$shortname."\n";
    $config .= "     LABEL ".$shortname."\n";
    $config .= "     INFOURL /device/device=".$devices[$k]."/\n";
    $config .= "     OVERLIBGRAPH /graph.php?height=100&width=512&device=".$devices[$k]."&type=device_bits&legend=no\n";
    $config .= "     ICON 80 80 ".$router_image."\n";
    $config .= "     LABELOFFSET 0 10\n";
    $config .= "     POSITION ".$v['x']." ".$v['y']."\n";
    $config .= "\n";
  }
  return $config;
}

// Get LLDP link matrix from data source
function get_link_matrix($dbh, $devices, $link_opts, $match_iftypes) {
  $in_list = implode("','", array_keys($devices));
  $in_list = "'".$in_list."'";
  $id_list = implode(",", array_values($devices));
  $iftype="";
  if(is_array($match_iftypes)) {
    $iftype = " AND p.ifType IN ('".implode("','", $match_iftypes)."')";
    $iftype .= " AND p2.ifType IN ('".implode("','", $match_iftypes)."')";
  }
  $links=[];
  // Get XDP links
  $sql = "
    SELECT
      p.device_id as device_id,
      p.ifDescr as ifDescr,
      p.ifIndex as ifIndex,
      p.ifSpeed as ifSpeed,
      l.local_port_id as local_port_id,
      l.remote_port_id as remote_port_id,
      l.remote_port as remote_port,
      d.hostname as local_hostname,
      d2.hostname as remote_hostname 
    FROM
      links l
      INNER JOIN ports p ON l.local_port_id=p.port_id
      INNER JOIN ports p2 ON l.remote_port_id = p2.port_id
      INNER JOIN devices d ON p.device_id=d.device_id
      INNER JOIN devices d2 ON p2.device_id=d2.device_id
    WHERE
      p.device_id IN ($id_list)
      AND d2.hostname IN ($in_list)
      $iftype
    UNION
    SELECT
      p.device_id as device_id,
      p.ifDescr as ifDescr,
      p.ifIndex as ifIndex,
      p.ifSpeed as ifSpeed,
      m.port_id as local_port_id,
      p2.port_id as remote_port_id,
      p2.ifDescr as remote_port,
      d.hostname as local_hostname,
      d2.hostname as remote_hostname 
    FROM
      ipv4_mac m
      INNER JOIN ports p ON m.port_id=p.port_id
      INNER JOIN ports p2 ON m.mac_address = p2.ifPhysAddress
      INNER JOIN devices d ON p.device_id=d.device_id
      INNER JOIN devices d2 ON p2.device_id=d2.device_id
    WHERE
      m.mac_address NOT IN ('000000000000','ffffffffffff')
      AND p.device_id IN ($id_list)
      AND d2.hostname IN ($in_list)
      $iftype
  ";
  $sth = $dbh->prepare($sql);
  //$sth->bindParam(1, $in_list);
  $sth->execute();
  $sth->setFetchMode(PDO::FETCH_ASSOC);
  while($row = $sth->fetch()) {
    $composite = $row['local_port_id'].':'.$row['remote_port_id'];
    if (empty($links[$row['local_hostname']][$composite])) {
      $links[$row['local_hostname']][$composite] = [
        'device_id' => $row['device_id'],
        'local_port' => str_replace(' ', '', $row['ifDescr']),
        'local_ifIndex' => $row['ifIndex'],
        'bandwidth' => format_bandwidth($row['ifSpeed']),
        'local_port_id' => $row['local_port_id'],
        'remote_hostname' => $row['remote_hostname'],
        'remote_port' => str_replace(' ', '', $row['remote_port']),
        'remote_port_id' => $row['remote_port_id'],
      ];
    }
  }
  return $links;
}

function create_link_config($links,$rrdcached,$rrdcached_dir) {
  $config = "# regular LINKs:\n";
  $prefix = '../../../rrd';
  if(!empty($rrdcached)) {
    if ($rrdcached_dir=='#') {
      $prefix = '.';
    } else {
      $prefix = $rrdcached_dir;
    }
  }
  foreach ($links as $k => $a) {
    $shortname = shortname($k);
    foreach ($a as $i => $v) {
      $remote_shortname = shortname($v['remote_hostname']);
      $config .= "LINK ".$shortname.":".$v['local_port']."-".$remote_shortname.":".$v['remote_port']."\n";
      $config .= "     INFOURL /graphs/type=port_bits/id=".$v['local_port_id']."/\n";
      $config .= "     OVERLIBGRAPH /graph.php?height=100&width=512&id=".$v['local_port_id']."&type=port_bits&legend=no\n";
      $config .= "     TARGET ".$prefix."/".$k."/port-id".$v['local_port_id'].".rrd:INOCTETS:OUTOCTETS\n";
      $config .= "     NODES ".$shortname." ".$remote_shortname."\n";
      $config .= "     BANDWIDTH ".$v['bandwidth']."\n";
      $config .= "\n";
    }
  }
  return $config;
}

function shortname($hostname) {
  $subs = explode('.', $hostname);
  $sub_count=count($subs);
  $suffix = '.'.$subs[$sub_count-2].'.'.$subs[$sub_count-1];
  $shortname = str_replace($suffix,'',$hostname);
  
  return $shortname;
}

function format_bandwidth($speed) {
  $bandwidth='';
  switch (true) {
    case $speed >= 1000000000:
      $bandwidth = ($speed/1000000000)."G";
      break;
    case $speed >= 1000000:
      $bandwidth = ($speed/1000000)."M";
      break;
    case $speed >= 1000:
      $bandwidth = ($speed/1000)."K";
      break;
  }
  return $bandwidth;
}
