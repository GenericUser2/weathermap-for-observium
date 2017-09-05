<?php

require_once '../editor-config.php';

$observium_base = '../../../';

require_once $observium_base.'includes/sql-config.inc.php';
require_once $observium_base.'html/includes/functions.inc.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php print $GLOBALS['config_weathermap_observiumbase']; ?>" />
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
    <!-- JS END -->
    <!--[if lt IE 9]><script src="js/html5shiv.min.js"></script><![endif]-->
    <?php include($observium_base."html/includes/authenticate.inc.php"); ?>
    <title>Maps - Weathermap - Observium</title>
    <link rel="shortcut icon" href="images/observium-icon.png" />
    <link href="<?php print $GLOBALS['config_weathermap_observiumbase']; ?>feed.php?id=2&amp;hash=65rbgk1hUU815MrOvnr4gjEyJDJMb5n6-mSoHJDo24Y&amp;size=15&amp;feed=eventlog" rel="alternate" title="Observium :: Eventlog Feed" type="application/atom+xml" />
</head>

<body>

<?php

if ($_SESSION['authenticated']) {
    include '../lib/weathermap.header.inc.php';
    print '    <div class="container-fluid">';
    
    if ($_SESSION['userlevel'] < 7) {
        print '
        <div class="container">
            <div class="row">
                <div class="alert">
                    <b>WARNING</b> - Userlevel is below 7 ('.$_SESSION['userlevel'].')
                </div><!-- alert -->
            </div><!-- row -->
        </div><!-- container -->
    </div><!-- container-fluid -->
        ';
    } else {
        if(isset($_GET["submap"])) {
            echo '        <h1>PHP Weathermap "'.$GLOBALS['config_weathermap_groups'][$_GET["submap"]].'" maps:</h1>';
        } else {
            echo '        <h1>PHP Weathermap all maps:</h1>';
        }
        
        function getFiles($val = '.') {
            $pngfiles = array();

            if ($handle = opendir('..')) {
                $i = 0;
                while (false !== ($entry = readdir($handle))) {
                    if (strpos($entry, '.png') !== false && strpos($entry, $val) !== false) {
                        $pngfiles[$i] = explode(".", $entry)[0];
                        $i++;
                    }
                }
                closedir($handle);
            }

            sort($pngfiles);
            return $pngfiles;
        }

        if(isset($_GET["submap"])) {
            $pngfiles = getFiles($_GET["submap"]);
            echo '<div class="box box-solid">
                    <div class="box-header">
                        <i class="oicon-map"></i>
                        <a href="weathermap/maps/allmaps.php?submap='.$_GET["submap"].'"><h3 class="box-title">'.$GLOBALS['config_weathermap_groups'][$_GET["submap"]].' </h3></a>
                        <span class="label">'.count($pngfiles).'</span>
                    </div>
                    <div class="box-body spacing">
                       <div class = "row">';
            foreach ($pngfiles as $pngfile) {
                echo '<div class = "col-xs-6 col-sm-4 col-md-3 col-lg-2">
                             <a href = "weathermap/maps/'.$pngfile.'.html" class = "thumbnail">
                             <img src = "weathermap/'.$pngfile.'.png" alt = "'.$pngfile.'.png">
                             <span>'.$pngfile.'</span>
                             </a>
                          </div>';
            }
            echo '</div></div></div>';
        } else {
            $pngfiles = getFiles();
            echo '
            <div class="row">';
            foreach ($pngfiles as $pngfile) {
                echo '
                <div class = "col-xs-6 col-sm-4 col-md-3 col-lg-2">
                             <a href = "weathermap/maps/'.$pngfile.'.html" class = "thumbnail">
                             <img src = "weathermap/'.$pngfile.'.png" alt = "'.$pngfile.'.png">
                             <span>'.$pngfile.'</span>
                             </a>
                          </div>';
            }
            echo '
            </div><!-- row -->
            ';
        }
        include '../lib/weathermap.footer.inc.php';
        echo '
        </div><!-- container-fluid -->';
    }
} else {
    include($observium_base."html/pages/logon.inc.php");
}

?>

   </body>
</html>
