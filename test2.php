<html>
<head>
<style>
* { font-family:monospace }
a,a:visited { color:#bbb!important;}
body,html {background:#222; color:#aaa}
body { font-family:arial; font-size:12px;line-height:15px;padding:10px}
hr { border-top:1px dotted #777;height:1px;margin:15px 0;}
h2 { padding:10px 0 5px 0;margin:0;}
b { background-color:#ffffcc; color:#222;font-weight:normal;padding:0 3px;}
</style>
</head>
<body>
<?
include("minibots.class.php");

$mb = new Minibots();

echo "| <a href='test.php'>Page 1</a> | <a href='test2.php'><b>Page 2</b></a> | <a href='test3.php'>Page 3</a> | <a href='test4.php'>Page 4</a> |<hr>";

// test getIp
// -------------------------------------------------------------
echo "<h2>getIP method</h2>";
echo "Check getIP function, <b>your ip is</b>: <br><pre>".($mb->getIP())."</pre>";
echo "<hr>";

// test ipToGeo
// -------------------------------------------------------------
echo "<h2>ipToGeo method</h2>";
echo "Check ipToGeo function, data from your IP address <b>".($mb->getIP())."</b>: <br><pre>".print_r($mb->ipToGeo(),true)."</pre>";
echo "<hr>";

// test copyFile
// -------------------------------------------------------------
echo "<h2>copyFile method</h2>";
echo "Check copyFile function, copy my avatar from codecanyon on my server: <br>";
unlink("avatar.jpg");
if($mb->copyFile("http://1.s3.envato.com/files/68503809/pons-80x80.jpeg","avatar.jpg")) {
	echo "ok <img src='avatar.jpg'/> (local file on barattalo.it)";
} else {
	echo "fails";
}
echo "<hr>";


// test checkMp3
// -------------------------------------------------------------
$temp = $mb->checkMp3("http://www.artintent.it/Kalimba.mp3");
echo "<h2>checkMp3 method</h2>";
echo "Check if url <b>http://www.artintent.it/Kalimba.mp3</b> is an mp3: <pre>".($temp ? "true" : "false")."</pre>";
$temp = $mb->checkMp3("https://www.barattalo.it/file_example_MP3_700KB.mp3");
echo "Check if url <b>https://www.barattalo.it/file_example_MP3_700KB.mp3</b> is an mp3: <pre>".($temp ? "true" : "false")."</pre>";
echo "<hr>";


// test doShortUrl and doShortURLDecode
// -------------------------------------------------------------
echo "<h2>doShortUrl method</h2>";
echo "Try to make a short url from <b>http://www.dailybest.it/2013/03/05/vita-programmatore-gif-animate/</b><br>";
$a = $mb->doShortUrl("http://www.dailybest.it/2013/03/05/vita-programmatore-gif-animate/");
echo "Short: <pre>".$a."</pre><hr>";
echo "<h2>doShortURLDecode method</h2>";
echo "Short url decode from <b>$a</b>: <pre>".$mb->doShortURLDecode($a)."</pre>";
echo "<hr>";

// test doSpelling
// -------------------------------------------------------------
echo "<h2>doSpelling method</h2>";
echo "Make spell check for word <b>wikipezia</b>: <pre>";
echo $mb->doSpelling("wikipezia")."</pre>";
echo "<hr>";

// test getLatLong
// -------------------------------------------------------------
echo "<h2>getLatLong method</h2>";
echo "Retrieve coordinates for address: <b>piazza cadorna, milano, italy</b><br/>";
echo "<pre>".print_r($mb->getLatLong("piazza cadorna, milano, italy",""),true)."</pre>";
echo "Please use interactive demo <a href='https://www.barattalo.it/demo/minibots.php'>here</a>";
echo "<hr>";

// test getLatLongBis
// -------------------------------------------------------------
echo "<h2>getLatLongBis method</h2>";
echo "Retrieve coordinates for address: <b>piazza cadorna, milano, italy</b><br/>";
echo "<pre>".print_r($mb->getLatLongBis("piazza cadorna, milano, italy",""),true)."</pre>";
echo "Please use interactive demo <a href='https://www.barattalo.it/demo/minibots.php'>here</a>";
echo "<hr>";

// test getUrlInfo
// -------------------------------------------------------------
echo "<h2>getUrlInfo method</h2>";
$url = "https://www.dailybest.it/ambiente/come-adottare-una-mucca-a-distanza-e-vivere-felici/";
echo "Get info on <b>".$url."</b><br>";
echo "<pre>".print_r($mb->getUrlInfo($url,2,10),true)."</pre>";
echo "<hr>";

// test getVideoUrlInfo
// -------------------------------------------------------------
echo "<h2>getVideoUrlInfo method</h2>";
echo "Get info on Vimeo video <b>https://vimeo.com/75976293</b><br>";
echo "<pre>".print_r($mb->getVideoUrlInfo("https://vimeo.com/75976293"),true)."</pre>";
echo "Get info on Youtube video <b>https://www.youtube.com/watch?v=KUVlrdfKowk</b><br>";
echo "<pre>".print_r($mb->getVideoUrlInfo("https://www.youtube.com/watch?v=KUVlrdfKowk"),true)."</pre>";
echo "<hr>";

// test getVimeoInfo
// -------------------------------------------------------------
echo "<h2>getVimeoInfo method</h2>";
echo "Get extended info on Vimeo video ID <b>75976293</b><br>";
echo "<pre>".print_r($mb->getVimeoInfo("75976293"),true)."</pre>";
echo "<hr>";

// test googleSuggestKeywords
// -------------------------------------------------------------
echo "<h2>googleSuggestKeywords method</h2>";
echo "Get keyword suggesta from Google with <b>berlusconi</b><br>";
echo "<pre>".print_r($mb->googleSuggestKeywords("berlusconi"),true)."</pre>";
echo "<hr>";

// test readFacebookCounters
// -------------------------------------------------------------
echo "<h2>readFacebookCounters method</h2>";
$url = "https://www.dailybest.it/society/vita-programmatore-gif-animate/";
echo "Get Facebook counters for <b>".$url."</b><br>";
echo "<pre>".print_r($mb->readFacebookCounters($url),true)."</pre>";
echo "(Should be greater than 1)";
echo "<hr>";

// test readFacebookCounter
// -------------------------------------------------------------
echo "<h2>readTwitterCounter method</h2>";
echo "Get number of times the url <b>".$url."</b> has been twitted<br>";
echo "<pre>".print_r($mb->readTwitterCounter($url),true)."</pre>";
echo "<hr>";

// test wikiDefinition
// -------------------------------------------------------------
echo "<h2>wikiDefinition method</h2>";
echo "Get Wikipedia definition for <b>Barack Obama</b><br>";
echo "<pre>".print_r($mb->wikiDefinition("Barack Obama"),true)."</pre>";
echo "<hr>";

// test url_exists
// -------------------------------------------------------------
echo "<h2>url_exists method</h2>";
$u = "https://en.wikipedia.org/wiki/Barack_Obama";
echo "Check if a remote url exists for <b>".$u."</b><br>";
echo "<pre>".($mb->url_exists($u) ? "true" : "false")."</pre>";
echo "Check <b>".$u."2</b> (wrong)<br>";
echo "<pre>".($mb->url_exists($u."2") ? "true" : "false")."</pre>";
echo "<hr>";

// test doSMTPValidation
// -------------------------------------------------------------
echo "<h2>doSMTPValidation method</h2>";
echo "Check if an email exists <b>pons@rockit.it</b><br>";
echo "<pre>".print_r($mb->doSMTPValidation("pons@rockit.it","info@barattalo.it",true),true)."</pre>";
echo "<hr>";



?>
</body>
</html>