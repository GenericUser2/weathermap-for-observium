    <header class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target="#main-nav">
                    <span class="oicon-bar"></span>
                    <span class="oicon-bar"></span>
                    <span class="oicon-bar"></span>
                </button>
                <a class="brand brand-observium" href="/">&nbsp;</a>
                <div class="nav-collapse" id="main-nav">
                    <ul class="nav">
                        <li><a href="weathermap/maps/allmaps.php"><span><i class="menu-icon oicon-globe"></i>All maps</span></a></li>
                        <li class="dropdown">
                            <a href="overview/" class="hidden-sm dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">
                                <i class="oicon-map"></i> Map groups <b class="caret"></b></a>
                            <a href="overview/" class="visible-sm dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">
                                <i class="oicon-map"></i> <b class="caret"></b></a>
                            <ul role="menu" class="dropdown-menu">
<?php

foreach ($GLOBALS['config_weathermap_groups'] as $x => $x_value) {
    echo '<li><a role="menuitem" href="weathermap/maps/allmaps.php?submap='.$x.'"><span><i class="menu-icon oicon-map"></i>'.$x_value.'</span></a></li>';
}

?>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="devices/" class="hidden-sm dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">
                                <i class="oicon-servers"></i> Devices <b class="caret"></b></a>
                            <a href="devices/" class="visible-sm dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">
                                <i class="oicon-servers"></i> <b class="caret"></b></a>
                            <ul role="menu" class="dropdown-menu">
                                <li><a role="menuitem" href="devices/"><span><i class="menu-icon oicon-servers"></i> All Devices</span></a></li>
                                <li class="divider"></li>
                                <li><a role="menuitem" href="addhost/"><span><i class="menu-icon oicon-server--plus"></i> Add Device</span></a></li>
                                <li><a role="menuitem" href="delhost/"><span><i class="menu-icon oicon-server--minus"></i> Delete Device</span></a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="search/" class="hidden-sm dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">
                                <i class="oicon-magnifier-zoom-actual"></i>&nbsp;Search <b class="caret"></b></a>
                            <a href="search/" class="visible-sm dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">
                                <i class="oicon-magnifier-zoom-actual"></i> <b class="caret"></b></a>
                            <ul role="menu" class="dropdown-menu">
                                <li><a role="menuitem" href="search/search=ipv4/"><span><i class="menu-icon oicon-magnifier-zoom-actual"></i> IPv4 Address</span></a></li>
                                <li><a role="menuitem" href="search/search=ipv6/"><span><i class="menu-icon oicon-magnifier-zoom-actual"></i> IPv6 Address</span></a></li>
                                <li><a role="menuitem" href="search/search=mac/"><span><i class="menu-icon oicon-magnifier-zoom-actual"></i> MAC Address</span></a></li>
                                <li><a role="menuitem" href="search/search=arp/"><span><i class="menu-icon oicon-magnifier-zoom-actual"></i> ARP/NDP Tables</span></a></li>
                                <li><a role="menuitem" href="search/search=fdb/"><span><i class="menu-icon oicon-magnifier-zoom-actual"></i> FDB Tables</span></a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav pull-right">
                        <li><a href="weathermap/editor.php"><span><i class="menu-icon oicon-gear"></i>Editor</span></a></li>
                    </ul>
                </div>
                <!-- /.nav-collapse -->
            </div>
        </div>
        <!-- /navbar-inner -->
    </header>
