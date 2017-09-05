# weathermap-for-observium
Upgraded tobzsc version on my Ubuntu 16.04

1. Install GIT:

```
su -
apt-get update
apt-get install git
```

2. Install the Weathermap in the /opt/observium/html folder and move the overlib.js file one level up:

```
cd /opt/observium/html
git clone https://github.com/akender/weathermap-for-observium.git weathermap
cd weathermap
mv /opt/observium/html/weathermap/overlib.js /opt/observium/html/overlib.js
```

3. Make some directory's writeable or executable by your web server and make folder for maps:

```
chown -R www-data:www-data configs/
chown -R www-data:www-data maps/
chmod +x map-poller.php
```

4. Remove "#" from the beginning of $config['install_dir']   = "/opt/observium"; in /opt/observium/includes/defaults.inc.php file:

```
$config['install_dir']   = "/opt/observium";
```

5. Change mysql connection parameters in /opt/observium/html/weathermap/lib/datasources/WeatherMapDataSource_observium.php:

```
$con = mysqli_connect("localhost","obs_db_user","obs_db_password","obs_db");
```

6. Enable the cron process at /etc/cron.d/observium:

```
# For Weathermap
*/1 * * * * root /opt/observium/html/weathermap/map-poller.php >> /dev/null 2>&1
```

7. You can use the navbar-custom.inc.php by putting it into /opt/observium/html/includes/.

```
cp navbar-custom.inc.php /opt/observium/html/includes/navbar-custom.inc.php
```

8. Edit the /opt/observium/html/weathermap/editor-config.php file and make sure that observiumbase have right value (i.e http://localhost/).

```
$config_weathermap_observiumbase    = 'https://yoursite.com:443/';
```

9. Point your browser to your install /weathermap/editor.php (i.e http://localhost/weathermap/editor.php)

10. Create your maps, please note when you create a MAP, please click Map Style, ensure Overlib is selected for HTML Style and click submit. Example map properties for test.conf:

```
Map Title: Test
Output Image Filename: test.png
Output HTML Filename: maps/test.html
```

11. You may edit "editor-config.php" file to define groups for maps. Grouping based on the key occurrences in png filenames of maps. 
For group with key "gr1" match all png files with pattern gr1*.png, for group with key "gr2" match all png files with pattern gr2*.png and so on.

**** IMPORTANT SECURITY *****

It is highly recommended that you set $ENABLED=false in editor.php when you are not editing maps as this is accessible by anyone unless you secure it via .htaccess or your web server config.
