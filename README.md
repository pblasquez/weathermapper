# weathermapper
Automatically generate weathermaps from a LibreNMS database

Requirements
------------
- A working [LibreNMS][1] installation
- A working [LibreNMS-Weathermap Plugin][2]
- PHP 5.4+

Installation
------------
- Clone to a directory such as /opt/weathermapper:
- ```cd /opt; git clone https://github.com/pblasquez/weathermapper.git```
- Verify settings are correct in weathermapper.conf.php
- Create a map file in conf.d using the examples provided within that directory (you will need to provide data from your environment)
- Run weathermapper.php: ```$ php weathermapper.php```

Optional: LibreNMS Plugin
------------
Assuming you have installed both weathermapper and librenms in /opt:
- ```cp -R /opt/weathermapper/librenms_plugin/Weathermapper /opt/librenms/html/plugins/```
- Make sure this directory is owned by the webserver user
- Go to your LibreNMS web UI -> Overview -> Plugins -> Plugin Admin
- Click Enable for Weathermapper
- Go to Overview -> Plugins -> Weathermapper
- Fill out form and submit (Create Weathermap)
- Run weathermapper.php: ```$ php weathermapper.php```

Examples
--------
Examples of map designs created can be found in the examples directory or viewed on the [wiki][3].

Tips:
----
- Play with the values in grid_opts to see the effects on the auto-layout.
- Target different hierarchy members using the search_opts and assign them to separate rows.
- Use the auto-layout as a fast-forward then manually edit the resulting map.

Examples are included in the conf.d directory. The comments may be a bit overwhelming, so here is what the configuration looks like when stripped of documentation:

HOSTNAMES
---------
```php
$label = 'mylabel';
$weathermapper[$label] = [];
$weathermapper[$label]['title'] = "Network Map (".$label.")";
$weathermapper[$label]['grid_opts'] = $grid_opts;
$weathermapper[$label]['search_opts'] = [];
$weathermapper[$label]['search_opts']['types'] = ['hostname'];
$weathermapper[$label]['search_opts']['hostnames'] = [];
$weathermapper[$label]['search_opts']['hostnames'][] = [
  'regex' => "myhost[0-9]+.*",
  'row' => 10
];
```

GROUPS
------
```php
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
```

LOCATIONS
---------
```php
$label = 'mylabel';
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
```

[1]: https://github.com/librenms/librenms "LibreNMS GitHub repo"
[2]: https://github.com/librenms-plugins/Weathermap "LibreNMS Weathermap Plugin Github repo"
[3]: https://github.com/pblasquez/weathermapper/wiki
