<?php
        require('../Plans.php');
        $key = GEO_GMAPS_API;
        $markers = Doctrine_Query::create()
                                                ->select("l.latitude, l.longitude, l.country, l.region, l.city, group_concat(a.username) as usernames, count(*) as count")
                                                ->from('Location l')
                                                ->where('latitude != 0 and longitude != 0')
                                                ->innerJoin('l.Accounts a')
                                                ->groupBy('l.country, l.region, l.city')
                                                ->fetchArray();
        $userid = $i;
        $total = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Grinnellians of the World</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=$key?>" type="text/javascript"></script>
        <style>
                .n { width: 400px; }
                body { text-align: center; }
        </style>
    <script type="text/javascript">
    //<![CDATA[
    function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
        map.setCenter(new GLatLng(41.721700, -92.717600), 2);
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
                map.enableScrollWheelZoom();
<?php
        $i = 0;
        foreach ($markers as $m) {
                $total += $m['count'];
                $text = "<p class='n'><b>" . $m['count'] . "</b> Grinnellian" . ($m['count'] > 1 ? 's' : '') . ' in ' . $m['city'] . ', ' . $m['region'] . ', ' . $m['country'] . '. ';
                if (isset($_GET['x'])) {
                        $text .= str_replace(',', ', ', $m['Accounts'][0]['usernames']);
                }
                $text .= "</p>";
?>
        var marker<?=$i?> = new GMarker(new GLatLng(<?=$m['latitude']?>, <?=$m['longitude']?>));
        map.addOverlay(marker<?=$i?>);
        GEvent.addListener(marker<?=$i?>, "click", function() {
                marker<?=$i?>.openInfoWindowHtml("<?=$text?>");
        });
<?php
                $i++;
        }
?>
                }
        }
    //]]>
    </script>
</head>
<body onload="load()" onunload="GUnload()">
        <div id="map" style="width: 1000px; height: 600px"></div>
        <p><?=$total?> Grinnellians have been geolocated.</p>
        <p>yet another product of <i>[athanasa] Is Insomniac Labs</i>. <b>comments more than welcome</b>.</p>
        <p>Everytime somebody logs in on Plans, the software geolocates their IP address, id est it traces their IP address to a city, region (state, province, et cetera), and country. The
map shows the last known location of everybody who has logged in since Monday, September 29, 2008 (midnight CST). Locations are generally accurate within the given region, depending on the
Intrnet Service Provider. If you are being routed through a proxy, a Tor circuit, or a VPN, the location of the endpoint will be recorded.</p>
</body>
</html>
<?php
        $i = $userid;
?>
