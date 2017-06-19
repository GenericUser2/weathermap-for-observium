<?php

// ******************************************
// sensible defaults
$mapdir='configs';
$observium_base = '/opt/observium/';
$observium_url = '/';
$ignore_observium=FALSE;
$config['base_url'] = $observium_url;
$whats_installed = '';

// check if the goalposts have moved
if( is_dir($observium_base) && file_exists($observium_base."/config.php") )
{
	// include the observium-config, so we know about the database
	include_once($observium_base."/config.php");
	include_once($observium_base."/includes/defaults.inc.php");
	// $config['base_url'] = $observium_url;
	$config['base_url'] = (isset($config['url_path'])? $config['url_path'] : $observium_url);
	$observium_found = TRUE;
	// print "global";
	if($config['project_name'] == 'LibreNMS') {
		$whats_installed = 'LibreNMS';
	} else {
		$whats_installed = 'Observium';
	}
}
else
{
	$observium_found = FALSE;
}

$link = mysqli_connect($config['db_host'],$config['db_user'],$config['db_pass'])
                or die('Could not connect: ' . mysqli_error());
mysqli_select_db($link,$config['db_name']) or die('Could not select database: '.mysqli_error());


// ******************************************

function js_escape($str)
{
	$str = str_replace('\\', '\\\\', $str);
	$str = str_replace("'", "\\\'", $str);

	$str = "'".$str."'";

	return($str);
}

if(isset($_REQUEST['command']) && $_REQUEST["command"]=='link_step2')
{
	$dataid = intval($_REQUEST['dataid']);
?>
<html>
<head>
	<script type="text/javascript">
	function update_source_step2(graphid)
	{
		var graph_url, hover_url;

		var base_url = '<?php echo isset($config['base_url'])?$config['base_url']:''; ?>';

		if (typeof window.opener == "object") {

			graph_url = base_url + 'graph.php?type=device_bits&legend=no&height=100&width=250&device=' + graphid;
			info_url = base_url + 'device/device=' + graphid;

			opener.document.forms["frmMain"].node_new_name.value ='test';
			opener.document.forms["frmMain"].node_label.value ='testing';
			opener.document.forms["frmMain"].link_infourl.value = info_url;
			opener.document.forms["frmMain"].link_hover.value = graph_url;
		}
		self.close();
	}

	window.onload = update_source_step2(<?php echo $graphid ?>);

	</script>
</head>
<body>
This window should disappear in a moment.
</body>
</html>
<?php
	// end of link step 2
}

if(isset($_REQUEST['command']) && $_REQUEST["command"]=='link_step1')
{
?>
<html>
<head>
	<script type="text/javascript" src="editor-resources/jquery-latest.min.js"></script>
	<script type="text/javascript">

	function filterlist(previous)
	{
		var filterstring = $('input#filterstring').val();	
		
		if(filterstring=='')
		{
			$('ul#dslist > li').show();
			if($('#ignore_desc').is(':checked')) {
				$("ul#dslist > li:contains('Desc::')").hide();
			}
			return;
		
		} else if(filterstring!=previous)
		{	
				$('ul#dslist > li').hide();
				$("ul#dslist > li:contains('" + filterstring + "')").show();
				if($('#ignore_desc').is(':checked')) {
                         	       $("ul#dslist > li:contains('Desc::')").hide();
                        	}

		} else if(filterstring==previous)
		{
			if($('#ignore_desc').is(':checked')) {
                        	$("ul#dslist > li:contains('Desc::')").hide();
                        } else {
				$('ul#dslist > li').hide();
				$("ul#dslist > li:contains('" + filterstring + "')").show();
			}
		}

	}

	function filterignore()
	{
		if($('#ignore_desc').is(':checked')) {
			$("ul#dslist > li:contains('Desc::')").hide();
		} else {
			//$('ul#dslist > li').hide();
			$("ul#dslist > li:contains('" + previous + "')").show();
		}
	}

	$(document).ready( function() {
		$('span.filter').keyup(function() {
			var previous = $('input#filterstring').val();
			setTimeout(function () {filterlist(previous)}, 500);
		}).show();
		$('span.ignore').click(function() {
			var previous = $('input#filterstring').val();
			setTimeout(function () {filterlist(previous)}, 500);
		});
	});

        function update_source_step2(graphid,name,portid,ifAlias,ifDesc,ifIndex,ifHighSpeed)
        {
                var graph_url, hover_url;

                var base_url = '<?php echo isset($config['base_url'])?$config['base_url']:''; ?>';

                if (typeof window.opener == "object") {

                        graph_url = base_url + 'graph.php?type=port_bits&legend=no&height=100&width=250&id=' + portid;
                        info_url = base_url + 'graphs/type=port_bits/id=' + portid;

                        opener.document.forms["frmMain"].node_new_name.value ='test';
                        opener.document.forms["frmMain"].node_label.value ='testing';
                        opener.document.forms["frmMain"].link_infourl.value = info_url;
                        opener.document.forms["frmMain"].link_hover.value = graph_url;
                        opener.document.forms["frmMain"].link_bandwidth_in.value = ifHighSpeed + 'M';
                }
                self.close();
        }

	function update_source_step1(dataid,hostname,name,portid,label,alias,ifIndex,ifHighSpeed)
	{
		// This must be the section that looks after link properties
		var newlocation;
		var fullpath;

		var rra_path = <?php echo js_escape($observium_base.'rrd/'); ?>+hostname+'/port-';

		if (typeof window.opener == "object") {
			fullpath = rra_path+ifIndex+'.rrd:INOCTETS:OUTOCTETS';
			if(document.forms['mini'].aggregate.checked)
			{
				opener.document.forms["frmMain"].link_target.value = opener.document.forms["frmMain"].link_target.value  + " " + fullpath;
			}
			else
			{
				opener.document.forms["frmMain"].link_target.value = fullpath;
			}
		}
		if(document.forms['mini'].overlib.checked)
		{

        		window.onload = update_source_step2(dataid,name,portid,label,alias,ifIndex,ifHighSpeed);

		}
		else
		{
			self.close();
		}
	}
	
	function applyDSFilterChange(objForm) {
                strURL = '?host_id=' + objForm.host_id.value;
                strURL = strURL + '&command=link_step1';
				if( objForm.overlib.checked)
				{
					strURL = strURL + "&overlib=1";
				}
				else
				{
					strURL = strURL + "&overlib=0";
				}
				// document.frmMain.link_bandwidth_out_cb.checked
				//if( objForm.aggregate.checked)
				//{
				//	strURL = strURL + "&aggregate=1";
				//}
				//else
				//{
				//	strURL = strURL + "&aggregate=0";
				//}
                document.location = strURL;
        }
	
	</script>
<style type="text/css">
	body { font-family: sans-serif; font-size: 10pt; }
	ul { list-style: none;  margin: 0; padding: 0; }
	ul { border: 1px solid black; }
	ul li.row0 { background: #ddd;}
	ul li.row1 { background: #ccc;}
	ul li { border-bottom: 1px solid #aaa; border-top: 1px solid #eee; padding: 2px;}
	ul li a { text-decoration: none; color: black; }
</style>
<title>Pick a data source</title>
</head>
<body>
<?php

	$host_id = -1;
	
	$overlib = true;
	$aggregate = false;
	
	if(isset($_REQUEST['aggregate'])) $aggregate = ( $_REQUEST['aggregate']==0 ? false : true);
	if(isset($_REQUEST['overlib'])) $overlib= ( $_REQUEST['overlib']==0 ? false : true);
	
	
	if(isset($_REQUEST['host_id']))
	{
		$host_id = intval($_REQUEST['host_id']);
	}

	 // Link query
	 $result = mysqli_query($link,"SELECT `device_id`,`sysName` AS `name` FROM `devices` ORDER BY name");

?>

<h3>Pick a data source:</h3>

<form name="mini">
<?php 
if(mysqli_num_rows($result) > 0) {
	print 'Host: <select name="host_id"  onChange="applyDSFilterChange(document.mini)">';

	print '<option '.($host_id==-1 ? 'SELECTED' : '' ).' value="-1">Any</option>';
	print '<option '.($host_id==0 ? 'SELECTED' : '' ).' value="0">None</option>';
	while ($host = mysqli_fetch_assoc($result))
	{
		print '<option ';
		if($host_id==$host['device_id']) print " SELECTED ";
		print 'value="'.$host['device_id'].'">'.$host['name'].'</option>';
	}
	print '</select><br />';
}

	print '<span class="filter" style="display: none;">Filter: <input id="filterstring" name="filterstring" size="20"> (case-sensitive)<br /></span>';
	print '<input id="overlib" name="overlib" type="checkbox" value="yes" '.($overlib ? 'CHECKED' : '' ).'> <label for="overlib">Also set OVERLIBGRAPH and INFOURL.</label><br />';
	print '<input id="aggregate" name="aggregate" type="checkbox" value="yes" '.($aggregate ? 'CHECKED' : '' ).'> <label for="aggregate">Append TARGET to existing one (Aggregate)</label><br />';
	print '<span class="ignore"><input id="ignore_desc" name="ignore_desc" type="checkbox" value="yes"> <label for="ignore_desc">Ignore blank interface descriptions</label></span>';

	print '</form><div class="listcontainer"><ul id="dslist">';

	$query = "SELECT ports.port_id AS port_id, ports.port_label_short AS label, ports.ifAlias AS alias, ports.ifIndex AS ifIndex, ports.ifHighSpeed AS ifHighSpeed, devices.device_id AS device_id, devices.hostname AS hostname, devices.sysName AS device_name, devices.purpose FROM observium.ports LEFT JOIN devices ON ports.device_id=devices.device_id WHERE ports.ignore=0 AND ports.disabled=0 AND ports.deleted=0 AND devices.disabled=0";

	if($host_id > 0) {
		$query .= " AND devices.device_id='$host_id'";
	}
	
	$query .= " ORDER BY devices.sysName,ports.port_label_short";
	$result = mysqli_query($link,$query);

	// print $SQL_picklist;

	$i=0;
	if( mysqli_num_rows($result) > 0 )
	{
			while ($queryrows = mysqli_fetch_assoc($result)) {
			echo "<li class=\"row".($i%2)."\">";
			$key = $queryrows['device_id']."','".$queryrows['hostname']."','".$queryrows['device_name']."','".$queryrows['port_id']."','".$queryrows['label']."','".addslashes($queryrows['alias'])."','".$queryrows['ifIndex']."','".$queryrows['ifHighSpeed'];
			echo "<a href=\"#\" onclick=\"update_source_step1('$key')\">". $queryrows['device_name'] . " [". $queryrows['ifIndex'] . "] " . $queryrows['label'] . " - " . $queryrows['alias'] . "</a>";
			echo "</li>\n";
			
			$i++;
		}
	}
	else
	{
		print "<li>No results...</li>";
		print "<li>SQL query: " . $query . "</li>";
	}

?>
</ul>
</div>
</body>
</html>
<?php
} // end of link step 1

if(isset($_REQUEST['command']) && $_REQUEST["command"]=='node_step1')
{
	$host_id = -1;
	
	$overlib = true;
	$aggregate = false;

	if(isset($_REQUEST['aggregate'])) $aggregate = ( $_REQUEST['aggregate']==0 ? false : true);
	if(isset($_REQUEST['overlib'])) $overlib= ( $_REQUEST['overlib']==0 ? false : true);
	
	
	if(isset($_REQUEST['host_id']))
	{
		$host_id = intval($_REQUEST['host_id']);
	}
	
	// Link query
	$query = mysqli_query($link,"SELECT `device_id`,`sysName` AS `name` FROM `devices` ORDER BY name");

?>
<html>
<head>
<script type="text/javascript" src="editor-resources/jquery-latest.min.js"></script>
<script type="text/javascript">

	function filterlist(previous)
	{
		var filterstring = $('input#filterstring').val();	
		
		if(filterstring=='')
		{
			$('ul#dslist > li').show();
			return;
		}
		
		if(filterstring!=previous)
		{	
				$('ul#dslist > li').hide();
				$("ul#dslist > li:contains('" + filterstring + "')").show();
				//$('ul#dslist > li').contains(filterstring).show();
		}
	}

	$(document).ready( function() {
		$('span.filter').keyup(function() {
			var previous = $('input#filterstring').val();
			setTimeout(function () {filterlist(previous)}, 500);
		}).show();
	});

	function applyDSFilterChange(objForm) {
                strURL = '?host_id=' + objForm.host_id.value;
                strURL = strURL + '&command=node_step1';
				if( objForm.overlib.checked)
				{
					strURL = strURL + "&overlib=1";
				}
				else
				{
					strURL = strURL + "&overlib=0";
				}
				
				//if( objForm.aggregate.checked)
				//{
				//	strURL = strURL + "&aggregate=1";
				//}
				//else
				//{
				//	strURL = strURL + "&aggregate=0";
				//}
                document.location = strURL;
        }
	
	</script>
	<script type="text/javascript">

	function update_source_step1(deviceid,hostname,devicename,graphid,graphname)
	{
		// This is the section that sets the Node Properties
		var graph_url, hover_url;

		var base_url = '<?php echo isset($config['base_url'])?$config['base_url']:''; ?>';

		if (typeof window.opener == "object") {

			graph_url = base_url + 'graph.php?type=device_' + graphname + '&height=100&width=250' + '&device=' + deviceid;
			info_url = base_url + 'device/device=' + deviceid;

			var devicetarget = 'observium:' + deviceid;

			// only set the overlib URL unless the box is checked
			if( document.forms['mini'].overlib.checked)
			{
				opener.document.forms["frmMain"].node_infourl.value = info_url;
			}
			opener.document.forms["frmMain"].node_hover.value = graph_url;
            opener.document.forms["frmMain"].node_new_name.value = hostname;
            opener.document.forms["frmMain"].node_label.value = devicename;
			opener.document.forms["frmMain"].node_target.value = devicetarget;
		}
		self.close();		
	}
	</script>
<style type="text/css">
	body { font-family: sans-serif; font-size: 10pt; }
	ul { list-style: none;  margin: 0; padding: 0; }
	ul { border: 1px solid black; }
	ul li.row0 { background: #ddd;}
	ul li.row1 { background: #ccc;}
	ul li { border-bottom: 1px solid #aaa; border-top: 1px solid #eee; padding: 2px;}
	ul li a { text-decoration: none; color: black; }
</style>
<title>Pick a graph</title>
</head>
<body>

<h3>Pick a graph:</h3>

<form name="mini">
<?php 
if(mysqli_num_rows($query) > 0) {
	print 'Host: <select name="host_id"  onChange="applyDSFilterChange(document.mini)">';

	print '<option '.($host_id==-1 ? 'SELECTED' : '' ).' value="-1">Any</option>';
	print '<option '.($host_id==0 ? 'SELECTED' : '' ).' value="0">None</option>';
	while($host = mysqli_fetch_assoc($query))
	{
		print '<option ';
		if($host_id==$host['device_id']) print " SELECTED ";
		print 'value="'.$host['device_id'].'">'.$host['name'].'</option>';
	}
	print '</select><br />';
}
	print '<span class="filter" style="display: none;">Filter: <input id="filterstring" name="filterstring" size="20"> (case-sensitive)<br /></span>';
	print '<input id="overlib" name="overlib" type="checkbox" value="yes" '.($overlib ? 'CHECKED' : '' ).'> <label for="overlib">Set both OVERLIBGRAPH and INFOURL.</label><br />';

	print '</form><div class="listcontainer"><ul id="dslist">';

	$SQL_picklist = "SELECT device_graphs.device_graph_id AS graph_id, device_graphs.graph AS graph_name, devices.device_id AS device_id, devices.hostname AS hostname, devices.sysName AS device_name, devices.purpose FROM device_graphs LEFT JOIN devices ON device_graphs.device_id=devices.device_id WHERE device_graphs.enabled=1 AND devices.disabled=0";

	if($host_id > 0) {
		$SQL_picklist .= " AND devices.device_id='$host_id'";
	}
	$SQL_picklist .= " ORDER BY device_name,graph_name";
	$result = mysqli_query($link,$SQL_picklist);

	if( mysqli_num_rows($result) > 0)
	{
		$i=0;
		while($queryrows = mysqli_fetch_assoc($result)) {
			echo "<li class=\"row".($i%2)."\">";
			$device_id = $queryrows['device_id'];
			$hostname = $queryrows['hostname'];
			$device_name = $queryrows['device_name'];
			$graph_id = $queryrows['graph_id'];
			$graph_name = $queryrows['graph_name'];
			echo "<a href=\"#\" onclick=\"update_source_step1('$device_id','$hostname','$device_name','$graph_id','$graph_name')\">". $device_name . " - " . $graph_name . "</a>";
			echo "</li>\n";
			$i++;
		}
	}
	else
	{
		print "<li>No results...<li>";
		print "SQL query: " . $SQL_picklist;
	}

?>
</ul>
</body>
</html>
<?php
} // end of node step 1

// vim:ts=4:sw=4:
?>

