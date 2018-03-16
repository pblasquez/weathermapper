<?php

echo '
<div class="container">
  <form method="get" action="plugins/Weathermapper/create_weathermap.php">
    <div class="form-group">
      <label for="install">Weathermapper Installation Directory (Must be writable by web server user)</label>
      <input name="install" type="text" value="/opt/weathermapper" class="form-control">
    </div>
    <div class="form-group">
      <label for="wname">Weathermap Name (must be unique)</label>
      <input name="wname" type="text" class="form-control">
    </div>
    <div class="form-group">
      <label for="design">Map design</label>
      <select name="design" id="design" class="form-control">
        <option value="top">Top to Bottom</option>
        <option value="left">Left to Right</option>
        <option value="radial">Radial</option>
      </select>
    </div>

    <!-- Top to bottom -->
    <div class="well designCollapse design_top">
      <div class="form-group designCollapse design_top row">
	<label class="col-xs-4" for="colsize">Horizontal pixels between devices</label>
        <input class="col-xs-1" name="colsize" type="number" value=200 class="form-control">
      </div>
      <div class="form-group designCollapse design_top row">
        <label class="col-xs-4" for="colmargin">Horizontal pixels from edge of canvas</label>
        <input class="col-xs-1" name="colmargin" type="number" value=100 class="form-control">
      </div>
      <div class="form-group designCollapse design_top row">
        <label class="col-xs-4" for="rowsize">Vertical pixels between devices</label>
        <input class="col-xs-1" name="rowsize" type="number" value=100 class="form-control">
      </div>
      <div class="form-group designCollapse design_top row">
        <label class="col-xs-4" for="rowmargin">Vertical pixels from edge of canvas</label>
        <input class="col-xs-1" name="rowmargin" type="number" value=250 class="form-control">
      </div>
    </div>

    <!-- Left to Right -->

    <div class="well designCollapse design_left">
      <div class="form-group designCollapse design_left row">
        <label class="col-xs-4" for="colsize">Horizontal pixels between devices</label>
        <input class="col-xs-1" name="colsize" type="number" value=200 class="form-control">
      </div>
      <div class="form-group designCollapse design_left row">
        <label class="col-xs-4" for="colmargin">Horizontal pixels from edge of canvas</label>
        <input class="col-xs-1" name="colmargin" type="number" value=100 class="form-control">
      </div>
      <div class="form-group designCollapse design_left row">
        <label class="col-xs-4" for="rowsize">Vertical pixels between devices</label>
        <input class="col-xs-1" name="rowsize" type="number" value=100 class="form-control">
      </div>
      <div class="form-group designCollapse design_left row">
        <label class="col-xs-4" for="rowmargin">Vertical pixels from edge of canvas</label>
        <input class="col-xs-1" name="rowmargin" type="number" value=250 class="form-control">
      </div>
    </div>

    <!-- Radial -->

    <div class="well designCollapse design_radial">
      <div class="form-group designCollapse design_radial row">
        <label class="col-xs-4" for="radius">Pixels between radial sets</label>
        <input class="col-xs-1" name="radius" type="number" value=300 class="form-control">
      </div>
    </div>

    <!-- Device Selection -->

    <div class="input_fields_wrap">
      <label for="dev_select">Device Selection Method</label>
      <div class="form-group row">
        <div class="col-md-6">
          <select name="dev_select" id="dev_select" class="form-control">
            <option value="hostname">Hostname Regex</option>
            <option value="group">Group</option>
            <option value="location">Location Regex</option>
          </select>
        </div>
        <div class="col-md-6" style=>
          <button type="button" class="add_field_button">Add device selection method</button>
        </div>
      </div>
    </div>
    <hr />
    <div class="form-group text-center">
      <button type="submit">Create Weathermap</button>
    </div>
  </form>
</div>
';
echo "
<script>
$('.designCollapse').addClass('collapse');
$('.design_top').collapse('show');
$('#design').change(function(){
    var design = '.design_' + $(this).val();
    $('.designCollapse').collapse('hide');
    $(design).collapse({ 'toggle': false }).collapse('show');
});
$('.deviceCollapse').addClass('collapse');
$('.dev_select_hostname').collapse('show');
$('#dev_select').change(function(){
    var device = '.dev_select_' + $(this).val();
    $('.deviceCollapse').collapse('hide');
    $(device).collapse('show');
});
$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $('.input_fields_wrap'); //Fields wrapper
    var add_button      = $('.add_field_button'); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            var v_layer = x*10;
            x++; //text box increment
            var dev_select =  document.getElementById('dev_select');
            if (dev_select.value == 'hostname') {
              $(wrapper).append('";
echo '\
    <div class="form-group row">\
      <div class="col-md-4">\
        <label for="host_regex">Hostname Regex</label>\
        <input name="host_regex[]" type="text" class="form-control">\
      </div>\
      <div class="col-md-4">\
        <label for="host_layer">Layer</label>\
        <input name="host_layer[]" type="number" value=\'+v_layer+\' class="form-control">\
      </div>\
      <div class="col-md-4">\
        <a href="#" class="remove_field">Remove</a>\
      </div>\
    </div>\
';
echo "');
            }
            if (dev_select.value == 'group') {
              $(wrapper).append('";
echo '\
    <div class="form-group row">\
      <div class="col-md-4">\
        <label for="group_id">Group ID</label>\
        <input name="group_id[]" type="number" value=1 class="form-control">\
      </div>\
      <div class="col-md-4">\
        <label for="group_layer">Layer</label>\
        <input name="group_layer[]" type="number" value=\'+v_layer+\' class="form-control">\
      </div>\
      <div class="col-md-4">\
        <a href="#" class="remove_field">Remove</a>\
      </div>\
    </div>\
';
echo "');
            }
            if (dev_select.value == 'location') {
              $(wrapper).append('";
echo '\
    <div class="form-group row">\
      <div class="col-md-4">\
        <label for="loc_regex">Location Regex</label>\
        <input name="loc_regex[]" type="text" class="form-control">\
      </div>\
      <div class="col-md-4">\
        <label for="loc_layer">Layer</label>\
        <input name="loc_layer[]" type="number" value=\'+v_layer+\' class="form-control">\
      </div>\
      <div class="col-md-4">\
        <a href="#" class="remove_field">Remove</a>\
      </div>\
    </div>\
';
echo "');
            }
        }
    });
    
    $(wrapper).on('click','.remove_field', function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').parent('div').remove(); x--;
    })
});
</script>
";
