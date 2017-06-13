# weathermap-for-observium
Upgraded tobzsc version on my Ubuntu 16.04

1. Install GIT:

```
su -
apt-get update
apt-get install git
```

2. Install the Weathermap in the /opt/observium/html folder:

```
cd /opt/observium/html
git clone https://github.com/akender/weathermap-for-observium.git weathermap
cd weathermap
```

3. Within editor.php, make sure you set $ENABLED=true:
```
...
$ENABLED=true
...
```

4. Make some directory's writeable or executable by your web server and make folder for maps:

```
chown www-data:www-data configs/
mkdir maps/
chown www-data:www-data maps/
chmod +x map-poller.php
```

5. Point your browser to your install /weathermap/editor.php (i.e http://localhost/weathermap/editor.php)

6. Create your maps, please note when you create a MAP, please click Map Style, ensure Overlib is selected for HTML Style and click submit. Example for test.conf:

```
Map Title: Test
Output Image Filename to: test.png
Output HTML Filename to: maps/test.html
```

7. Enable the cron process:

```
# For Weathermap
*/1 * * * * root /opt/observium/html/weathermap/map-poller.php >> /dev/null 2>&1
```

8. You can use the navbar-custom.inc.php by putting it into /opt/observium/html/includes/.

```
cp navbar-custom.inc.php /opt/observium/html/includes/navbar-custom.inc.php
```

**** IMPORTANT SECURITY *****

It is highly recommended that you set $ENABLED=false in editor.php when you are not editing maps as this is accessible by anyone unless you secure it via .htaccess or your web server config.
