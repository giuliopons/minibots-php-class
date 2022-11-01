<html>
<head>
<style>
body { font-family:arial; font-size:12px;line-height:15px;padding:10px}
hr { border-top:1px dotted gray;}
h2 { padding:10px 0 5px 0;margin:0;}
b { background-color:#ffffcc;font-weight:normal;padding:0 3px;}
</style>
</head>
<body>
<?php
include("minibots.class.php");

$mb = new Minibots();
echo "| <a href='test.php'>Page 1</a> | <a href='test2.php'>Page 2</a> | <a href='test3.php'>Page 3</a> | <a href='test4.php'>Page 4</a> |<hr>";

echo "<h2>readFacebookPageCounters method</h2>";
echo "<p>Get counters for a Facebook Fan Page <b>https://www.facebook.com/cocacolait/</b>:</p>";
$mb = new Minibots();
$mb->use_file_get_contents = "no"; // this setting works with my hosting!
$info = $mb->readFacebookPageCounters("https://www.facebook.com/cocacolait/");
echo "<pre>";
print_r($info);
echo "</pre>";
echo "<hr>";



?>