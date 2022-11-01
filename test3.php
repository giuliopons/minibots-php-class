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
<?
include("minibots.class.php");

$mb = new Minibots();
echo "| <a href='test.php'>Page 1</a> | <a href='test2.php'>Page 2</a> | <a href='test3.php'>Page 3</a> | <a href='test4.php'>Page 4</a> |<hr>";


echo "<h2>getGravatar method</h2>";
echo "<p>Get avatar pic from gravatar, test with <b>pons@rockit.it</b>:</p>";
echo "<img src='".$mb->getGravatar("pons@rockit.it")."'>";
echo "<hr>";


echo "<h2>getLinkedinCounter method</h2>";
echo "<p>Shares on linkedin for <b>http://www.barattalo.it/mini-bots-php-class/</b>:</p>";
echo $mb->getLinkedinCounter("http://www.barattalo.it/mini-bots-php-class/");
echo "<hr>";




echo "<h2>getPinterestCounter method</h2>";
echo "<p>Shares on getPinterestCounter for <b>https://www.dailybest.it/art/shines-a-light-e-uninstallazione-che-descrive-la-nostra-ossessione-per-la-tecnologia/</b>:</p>";
echo $mb->getPinterestCounter("https://www.dailybest.it/art/shines-a-light-e-uninstallazione-che-descrive-la-nostra-ossessione-per-la-tecnologia/");
echo "<hr>";



/*

No longer works


echo "<h2>getGoogleBackLinks method</h2>";
echo "<p>Google back links for <b>www.rockit.it</b>:</p>";
echo $mb->getGoogleBackLinks("www.rockit.it");
echo "<hr>";



echo "<h2>getGoogleIndexedPages method</h2>";
echo "<p>Google indexed pages for <b>www.barattalo.it</b>:</p>";
echo $mb->getGoogleIndexedPages("www.barattalo.it");
echo "<hr>";
*/


echo "<h2>getImageGoogle method</h2>";
echo "<p>Get Google images for keyword <b>funny face</b>:</p>";
echo "<pre>";
print_r( $mb->getImageGoogle("funny face") );
echo "</pre>";
echo "<hr>";



?>