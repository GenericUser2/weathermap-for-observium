<?php

/** editor.inc.php
  *
  * All the functions used by the editor.
  */

/** @function fix_gpc_string 
  *
  * Take a string (that we got from $_REQUEST) and make it back to how the
  * user TYPED it, regardless of whether magic_quotes_gpc is turned on or off.
  *
  * @param string $input String to fix
  *
  * @returns string Fixed string
  *
  */

function fix_gpc_string($input) 
{
    if (true == function_exists('get_magic_quotes_gpc') && 1 == get_magic_quotes_gpc()) {
        $input = stripslashes($input);
    }
    return ($input);
}

/**
 * Clean up URI (function taken from Cacti) to protect against XSS
 */
function wm_editor_sanitize_uri($str) {
        static $drop_char_match =   array(' ','^', '$', '<', '>', '`', '\'', '"', '|', '+', '[', ']', '{', '}', ';', '!', '%');
        static $drop_char_replace = array('', '', '',  '',  '',  '',  '',   '',  '',  '',  '',  '',  '',  '',  '',  '', '');

        return str_replace($drop_char_match, $drop_char_replace, urldecode($str));
}

// much looser sanitise for general strings that shouldn't have HTML in them
function wm_editor_sanitize_string($str) {
        static $drop_char_match =   array('<', '>' );
        static $drop_char_replace = array('', '');

        return str_replace($drop_char_match, $drop_char_replace, urldecode($str));
}

function wm_editor_validate_bandwidth($bw) {
  
    if(preg_match( "/^(\d+\.?\d*[KMGT]?)$/", $bw) ) {
	return true;
    }
    return false;
}

function wm_editor_validate_one_of($input,$valid=array(),$case_sensitive=false) {
    if(! $case_sensitive ) $input = strtolower($input);
    
    foreach ($valid as $v) {
	if(! $case_sensitive ) $v = strtolower($v);
	if($v == $input) return true;
    }
    
    return false;
}

// Labels for Nodes, Links and Scales shouldn't have spaces in
function wm_editor_sanitize_name($str) {
    return str_replace( array(" "), "", $str);
}

function wm_editor_sanitize_selected($str) {        
	$res = urldecode($str);
	
	if( ! preg_match("/^(LINK|NODE):/",$res)) {
	    return "";
	}
	return wm_editor_sanitize_name($res);
}

function wm_editor_sanitize_file($filename,$allowed_exts=array()) {
    
    $filename = wm_editor_sanitize_uri($filename);
    
    if ($filename == "") return "";
        
    $ok = false;
    foreach ($allowed_exts as $ext) {
	$match = ".".$ext;
	
	if( substr($filename, -strlen($match),strlen($match)) == $match) {
	    $ok = true;
	}
    }    
    if(! $ok ) return "";
    return $filename;
}

function wm_editor_sanitize_conffile($filename) {
    
    $filename = wm_editor_sanitize_uri($filename);
    
    # If we've been fed something other than a .conf filename, just pretend it didn't happen
    if ( substr($filename,-5,5) != ".conf" ) {
	$filename = ""; 
     }
    return $filename;
}

function show_editor_startpage()
{
	global $mapdir, $WEATHERMAP_VERSION, $config_loaded, $cacti_found, $ignore_cacti,$configerror;

	$fromplug = false;
	if (isset($_REQUEST['plug']) && (intval($_REQUEST['plug'])==1) ) { 
	    $fromplug = true; 
	}

	$matches=0;
    
    print '
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="https://observium.just.sise:443/" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- META BEGIN -->
    <meta http-equiv="refresh" content="300" />
    <!-- META END -->
    <!-- CSS BEGIN -->
    <link href="css/bootstrap.css?v=0.16.10.8128" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-select.css?v=0.16.10.8128" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-switch.css?v=0.16.10.8128" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-hacks.css?v=0.16.10.8128" rel="stylesheet" type="text/css" />
    <link href="css/jquery.qtip.min.css?v=0.16.10.8128" rel="stylesheet" type="text/css" />
    <link href="css/sprite.css?v=0.16.10.8128" rel="stylesheet" type="text/css" />
    <link href="css/flags.css?v=0.16.10.8128" rel="stylesheet" type="text/css" />
    <link href="css/c3.min.css?v=0.16.10.8128" rel="stylesheet" type="text/css" />
    <link href="css/leaflet.css?v=0.16.10.8128" rel="stylesheet" type="text/css" />
    <!-- CSS END -->
    <!-- JS BEGIN -->
    <script type="text/javascript" src="js/jquery.min.js?v=0.16.10.8128"></script>
    <script type="text/javascript" src="js/bootstrap.min.js?v=0.16.10.8128"></script>
    <script type="text/javascript" src="js/observium.js?v=0.16.10.8128"></script>
    <script type="text/javascript" src="js/d3.min.js?v=0.16.10.8128"></script>
    <script type="text/javascript" src="js/c3.min.js?v=0.16.10.8128"></script>
    <script type="text/javascript" src="js/observium-screen.js?v=0.16.10.8128"></script>
    <script type="text/javascript" src="js/jquery.qtip.min.js?v=0.16.10.8128"></script>
    <script type="text/javascript" src="js/leaflet.js?v=0.16.10.8128"></script>
    <script type="text/javascript" src="editor-resources/editor.js"></script>
    <!-- JS END -->
    <!--[if lt IE 9]><script src="js/html5shiv.min.js"></script><![endif]-->
    <!--<title>Kõik kaardid</title>-->
    <title>Editor - Weathermap - Observium</title>    <link rel="shortcut icon" href="images/observium-icon.png" />
    <link href="https://observium.just.sise:443/feed.php?id=2&amp;hash=65rbgk1hUU815MrOvnr4gjEyJDJMb5n6-mSoHJDo24Y&amp;size=15&amp;feed=eventlog" rel="alternate" title="Observium :: Eventlog Feed" type="application/atom+xml" />
</head>

<body>
    ';
    
    if ($_SESSION['authenticated']) {
        include 'weathermap.header.inc.php';
        print '    <div class="container-fluid">';
        
        if ($_SESSION['userlevel'] < 10) {
            print '
            <div class="container">
                <div class="row">
                    <div class="alert">
                        <b>WARNING</b> - Userlevel is below 10 ('.$_SESSION['userlevel'].')
                    </div><!-- alert -->
                </div><!-- row -->
            </div><!-- container -->
        </div><!-- container-fluid -->
            ';
        } else {
            /*print '
            <div class="container">
                <div class="row">
                    <div id="nojs" class="alert">
                        <b>WARNING</b> - Sorry, it\'s partly laziness on my part, but you really need JavaScript enabled and DOM support in your browser to use this editor. It\'s a visual tool, so accessibility is already an issue, if it is, and from a security viewpoint, you\'re already running my code on your <i>server</i> so either you trust it all having read it, or you\'re already screwed.
                        <P>If it\'s a major issue for you, please feel free to complain. It\'s mainly laziness as I said, and there could be a fallback (not so smooth) mode for non-javascript browsers if it was seen to be worthwhile (I would take a bit of convincing, because I don\'t see a benefit, personally).</P>
                    </div><!-- alert -->
                </div><!-- row -->
            </div><!-- container -->
            ';*/

            $errormessage = "";

            if ($configerror!='') {
                $errormessage .= $configerror.'<p>';
            }

            if ( !$observium_found && !$ignore_observium) {
                //$errormessage .= '$cacti_base is not set correctly. Cacti integration will be disabled in the editor.';
                //$errormessage .= "$observium_found and $ignore_observium";
                //if ($config_loaded != 1) { 
                        //$errormessage .= " You might need to copy editor-config.php-dist to editor-config.php and edit it."; 
                    //}
            }

            if ($errormessage != '') {
                print '<div class="alert" id="nocacti">'.htmlspecialchars($errormessage).'</div>';
            }

            print'
                <div class="row">
                    <div id="withjs">
                        <h1>PHP Weathermap '.$WEATHERMAP_VERSION.' editor</h1>
                        <div class="col-md-8">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Edit</h3>
                                </div>
                                <div class="box-body no-padding">
                                    <div style="padding 10px;">
                                       Create A New Map:<br>
                                       <form method="GET" class="form form-horizontal">Named:
                                           <input type="text" name="mapname" placeholder="example.conf" size="20">
                                           <input name="action" type="hidden" value="newmap">
                                           <input name="plug" type="hidden" value="'.$fromplug.'">
                                           <button id="submit" name="submit" type="submit" class="btn btn-primary text-nowrap" value="Create"><i class="icon-ok icon-white"></i>&nbsp;Create</button>
                                           OR
                                       </form>
            ';

            $titles = array();

            $errorstring="";

            if (is_dir($mapdir)) {
                $n=0;
                $dh=opendir($mapdir);

                if ($dh) {
                    while (false !== ($file = readdir($dh))) {
                    $realfile=$mapdir . DIRECTORY_SEPARATOR . $file;
                    $note = "";

                    // skip directories, unreadable files, .files and anything that doesn't come through the sanitiser unchanged
                    if ( (is_file($realfile)) && (is_readable($realfile)) && (!preg_match("/^\./",$file) )  && ( wm_editor_sanitize_conffile($file) == $file ) ) {
                        if (!is_writable($realfile)) {
                            $note .= "(read-only)";
                        }
                        $title='(no title)';
                        $fd=fopen($realfile, "r");
                        if ($fd) {
                            while (!feof($fd)) {
                                $buffer=fgets($fd, 4096);

                                if (preg_match("/^\s*TITLE\s+(.*)/i", $buffer, $matches)) { 
                                    $title= wm_editor_sanitize_string($matches[1]); 
                                }
                            }

                            fclose ($fd);
                            $titles[$file] = $title;
                            $notes[$file] = $note;
                            $n++;
                        }
                    }
                    }

                    closedir ($dh);
                } else { 
                    $errorstring = "Can't open mapdir to read."; 
                }

                ksort($titles);

                if ($n == 0) { 
                    $errorstring = "No files in mapdir"; 
                }
            } else { 
                $errorstring = "NO DIRECTORY named $mapdir"; 
            }

            print '
                                       Create A New Map as a copy of an existing map:
                                       <form method="GET" class="form form-horizontal">Named:
                                           <input type="text" name="mapname" placeholder="example.conf" size="20"> based on
                                           <input name="action" type="hidden" value="newmapcopy">
                                           <input name="plug" type="hidden" value="'.$fromplug.'">
                                           <select name="sourcemap">
            ';

            if ($errorstring == '') {
                foreach ($titles as $file=>$title) {
                    $nicefile = htmlspecialchars($file);
                    print "<option value=\"$nicefile\">$nicefile</option>\n";
                }
            } else {
                print '<option value="">'.htmlspecialchars($errorstring).'</option>';
            }

            print '
                                           </select>
                                           <button id="submit" name="submit" type="submit" class="btn btn-primary text-nowrap" value="Create Copy"><i class="icon-ok icon-white"></i>&nbsp;Create Copy</button>
                                           OR
                                       </form>
                                       Open An Existing Map (looking in '.htmlspecialchars($mapdir).'):
                                    </div>
                                    <table class="table table-hover table-striped  table-condensed ">
                                        <thead>
                                            <tr>
                                                <th>Note</th>
                                                <th>File name</th>
                                                <th>Map name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
            ';

            if ($errorstring == '') {
                foreach ($titles as $file=>$title) {			
                    # $title = $titles[$file];
                    $note = $notes[$file];
                    $nicefile = htmlspecialchars($file);
                    $nicetitle = htmlspecialchars($title);
                    print '
                                            <tr onclick="openLink(\'weathermap/editor.php?mapname='.$nicefile.'&plug='.$fromplug.'\')" style="cursor: pointer;">
                                                <td>'.$note.'</td>
                                                <td><span class="entity-title"><a href="weathermap/editor.php?mapname='.$nicefile.'&plug='.$fromplug.'" class="entity-popup">'.$nicefile.'</a></span></td>
                                                <td>'.$nicetitle.'</td>
                                            </tr>
                    ';
                }
            } else {
                print '<tr><td colspan="2">'.htmlspecialchars($errorstring).'</td></tr>';
            }

            print '
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- box-solid -->
                        </div><!-- col-md-8 -->
            ';

            $htmlfiles = array();
            if ($handle = opendir('./maps')) {
                $i = 0;
                while (false !== ($entry = readdir($handle))) {
                    if (strpos($entry, '.html') !== false) {
                        $htmlfiles[$i] = $entry;
                        $i++;
                    }
                }
                closedir($handle);
            }
            sort($htmlfiles);

            print '
                        <div class="col-md-4">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">View</h3>
                                    <span class="label">'.count($htmlfiles).'</span>
                                </div>
                                <div class="box-body no-padding">
                                   <table class="table table-hover table-striped  table-condensed ">
                                        <thead>
                                            <tr>
                                                <th>File name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
            ';

            if ($errorstring == '') {
                foreach ($htmlfiles as $htmlfile) {
                    $nicefile = htmlspecialchars($htmlfile);
                    print '
                                            <tr onclick="openLink(\'weathermap/maps/'.$nicefile.'\')" style="cursor: pointer;">
                                                <td><span class="entity-title"><a href="weathermap/maps/'.$nicefile.'" class="entity-popup">'.$nicefile.'</a></span></td>
                                            </tr>
                    ';
                }
            } else {
                print '<tr><td>'.htmlspecialchars($errorstring).'</td></tr>';
            }

            print '
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- box-solid -->
                        </div><!-- col-md-4 -->
                    </div><!-- withjs -->
                </div><!-- row -->
                ';
            print '
                <div class="navbar navbar-fixed-bottom">
                    <div class="navbar-inner">
                        <div class="container">
                            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                                <span class="oicon-bar"></span>
                                <span class="oicon-bar"></span>
                                <span class="oicon-bar"></span>
                            </a>
                            <div class="nav-collapse">
                                <ul class="nav">
                                    <li class="dropdown">PHP Weathermap Editor '.$WEATHERMAP_VERSION.'</li>
                                </ul>
                            </div><!-- nav-collapse -->
                        </div><!-- container -->
                    </div><!-- navbar-inner -->
                </div><!-- navbar-fixed-bottom -->
                ';
            print '
            </div><!-- container -->
            ';
        }
    } else {
        include("../../html/pages/logon.inc.php");
    }
    
    print '
</body>
</html>
';
}

function snap($coord, $gridsnap = 0)
{
    if ($gridsnap == 0) {
        return ($coord);
    } else {        
        $rest = $coord % $gridsnap;
        return ($coord - $rest + round($rest/$gridsnap) * $gridsnap );
    }
}


function extract_with_validation($array, $paramarray)
{
	$all_present=true;
	$candidates=array( );

	foreach ($paramarray as $var) {
		$varname=$var[0];
		$vartype=$var[1];
		$varreqd=$var[2];

		if ($varreqd == 'req' && !array_key_exists($varname, $array)) { 
	            $all_present=false; 
	        }

		if (array_key_exists($varname, $array)) {
			$varvalue=$array[$varname];

			$waspresent=$all_present;

			switch ($vartype)
			{
			case 'int':
				if (!preg_match('/^\-*\d+$/', $varvalue)) { 
                    $all_present=false; 
                }

				break;

			case 'float':
				if (!preg_match('/^\d+\.\d+$/', $varvalue)) { 
                    $all_present=false; 
                }

				break;

			case 'yesno':
				if (!preg_match('/^(y|n|yes|no)$/i', $varvalue)) { 
                    $all_present=false; 
                }

				break;

			case 'sqldate':
				if (!preg_match('/^\d\d\d\d\-\d\d\-\d\d$/i', $varvalue)) { 
                    $all_present=false; 
                }

				break;

			case 'any':
				// we don't care at all
				break;

			case 'ip':
				if (!preg_match( '/^((\d|[1-9]\d|2[0-4]\d|25[0-5]|1\d\d)(?:\.(\d|[1-9]\d|2[0-4]\d|25[0-5]|1\d\d)){3})$/', $varvalue)) { 
                    $all_present=false; 
                }

				break;

			case 'alpha':
				if (!preg_match('/^[A-Za-z]+$/', $varvalue)) { 
                    $all_present=false; 
                }

				break;

			case 'alphanum':
				if (!preg_match('/^[A-Za-z0-9]+$/', $varvalue)) { 
                    $all_present=false; 
                }

				break;

			case 'bandwidth':
				if (!preg_match('/^\d+\.?\d*[KMGT]*$/i', $varvalue)) { 
                    $all_present=false; 
                }
				break;

			default:
				// an unknown type counts as an error, really
				$all_present=false;

				break;
			}
			
			if ($all_present) {
				$candidates["{$prefix}{$varname}"]=$varvalue;
			}
		}
	}

	if ($all_present) {
	    foreach ($candidates as $key => $value) { 
		$GLOBALS[$key]=$value; 
	    }
	}

	return array($all_present,$candidates);
}

function get_imagelist($imagedir)
{
	$imagelist = array();

	if (is_dir($imagedir)) {
		$n=0;
		$dh=opendir($imagedir);

		if ($dh) {
			while ($file=readdir($dh)) {
				$realfile=$imagedir . DIRECTORY_SEPARATOR . $file;
				$uri = $imagedir . "/" . $file;

				if (is_readable($realfile) && ( preg_match('/\.(gif|jpg|png)$/i',$file) )) {
					$imagelist[] = $uri;
					$n++;
				}
			}

			closedir ($dh);
		}
	}
	return ($imagelist);
}

function handle_inheritance(&$map, &$inheritables)
{
	foreach ($inheritables as $inheritable) {		
		$fieldname = $inheritable[1];
		$formname = $inheritable[2];
		$validation = $inheritable[3];
		
		$new = $_REQUEST[$formname];
		if($validation != "") {
		    switch($validation) {
			case "int":
			    $new = intval($new);
			    break;
			case "float":
			    $new = floatval($new);
			    break;
		    }
		}
		
		$old = ($inheritable[0]=='node' ? $map->nodes['DEFAULT']->$fieldname : $map->links['DEFAULT']->$fieldname);	
		
		if ($old != $new) {
			if ($inheritable[0]=='node') {
				$map->nodes['DEFAULT']->$fieldname = $new;
				foreach ($map->nodes as $node) {
					if ($node->name != ":: DEFAULT ::" && $node->$fieldname == $old) {
						$map->nodes[$node->name]->$fieldname = $new;
					}
				}
			}
			
			if ($inheritable[0]=='link') {
				$map->links['DEFAULT']->$fieldname = $new;
				foreach ($map->links as $link) {
					
					if ($link->name != ":: DEFAULT ::" && $link->$fieldname == $old) {
						$map->links[$link->name]->$fieldname = $new;
					}
				}
			}
		}
	}
}

function get_fontlist(&$map,$name,$current)
{
    $output = '<select class="fontcombo" name="'.$name.'">';
        
    ksort($map->fonts);

    foreach ($map->fonts as $fontnumber => $font) {		
        $output .= '<option ';
        if ($current == $fontnumber) {
            $output .= 'SELECTED';
        }
        $output .= ' value="'.$fontnumber.'">'.$fontnumber.' ('.$font->type.')</option>';
    }
        
    $output .= "</select>";

    return($output);
}

function editor_log($str)
{
    // $f = fopen("editor.log","a");
    // fputs($f, $str);
    // fclose($f);
}

// vim:ts=4:sw=4:
?>
