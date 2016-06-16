# weathermapper
Automatically generate weathermaps from a LibreNMS database

Tips:
----
- Play with the values in grid_opts to see the effects on the auto-layout.
- Target different hierarchy members using the search_opts and assign them to separate rows to auto-layout in hierarchy format.
- Use the auto-layout as a fast-forward then manually edit the resulting map.

Examples are included in the conf.d directory. The comments may be a bit overwhelming, so here is what the configuration looks like when stripped of documentation:

HOSTNAMES
---------
$label = 'mylabel';
$weathermapper[$label] = [];
$weathermapper[$label]['title'] = "Network Map (".$label.")";
$weathermapper[$label]['grid_opts'] = $grid_opts;
$weathermapper[$label]['search_opts'] = [];
$weathermapper[$label]['search_opts']['types'] = ['hostname'];
$weathermapper[$label]['search_opts']['hostnames'] = [];
$weathermapper[$label]['search_opts']['hsotnames'][] = [
  'regex' => "myhost[0-9]+.*",
  'row' => 10
];

GROUPS
------
$label = 'mylabel';
$weathermapper[$label] = [];
$weathermapper[$label]['title'] = "Network Map (".$label.")";
$weathermapper[$label]['grid_opts'] = $grid_opts;
$weathermapper[$label]['search_opts'] = [];
$weathermapper[$label]['search_opts']['types'] = ['group'];
$weathermapper[$label]['search_opts']['groups'] = [];
$weathermapper[$label]['search_opts']['groups'][] = [
  'group' => 6,
  'row' => 10
];

LOCATIONS
---------
$label = 'DOTCLOUD-AZ1';
$weathermapper[$label] = [];
$weathermapper[$label]['title'] = "Network Map (".$label.")";
$weathermapper[$label]['grid_opts'] = $grid_opts;
$weathermapper[$label]['search_opts'] = [];
$weathermapper[$label]['search_opts']['types'] = ['location'];
$weathermapper[$label]['search_opts']['locations'] = [];
$weathermapper[$label]['search_opts']['locations'][] = [
  'regex' => "MYSITE[0-9}+",
  'row' => 10
];
