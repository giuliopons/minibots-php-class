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

echo "| <a href='test.php'>Page 1</a> | <a href='test2.php'>Page 2</a> | <a href='test3.php'>Page 3</a> | <a href='test4.php'>Page 4</a> |<hr>";



// test getExchangeRateFromTo
// -------------------------------------------------------------
echo "<h2>getExchangeRateFromTo method</h2>";
$mb = new Minibots();
echo "1 USD is ".($mb->getExchangeRateFromTo("USD","EUR"))." EUR<br>";
echo "1 EUR is ".($mb->getExchangeRateFromTo("EUR","JPY"))." JPY<br>";
echo "<hr>";

// test getExchangeRateFromTo
// -------------------------------------------------------------
echo "<h2>getImage method</h2>";
echo "<p>Get images for (for example) <b>happy cow</b>, return 30 pics, showing a random one. See interative demo <a href=\"http://www.barattalo.it/demo/minibots.php\">here</a>.</p>";
$pics = $mb->getImage("happy cow");
$x = $pics[rand(0,count($pics)-1)];
echo "<img src=\"".$x."\"/>";
echo "<hr>";

echo "<h2>getImageBig method using the previous results</h2><p>Search for big picture for $x url.</p>";
$bigpic = $mb->getImageBig($x);
echo "<img src=\"".$bigpic."\"/>";
echo "<hr>";


//echo "<h2>notifyNMA method</h2>";
//echo "<p>Method to send push notifications to your mobile devices.</p>";
//echo "<p>Sorry, can't run in this demo, you can find a tutorial with example <a href='http://www.barattalo.it/2013/11/18/push-notifications-android-devices-minibots/'>here</a>.</p>";
//echo "<hr>";

echo "<h2>pingomatic method</h2>";
echo "<p>Ping services to push your url to search engines and indexes.</p>";
echo "<p>Can't run in this demo, sorry. Read this <a href='http://www.barattalo.it/2013/11/18/push-notifications-android-devices-minibots/'>article</a>.</p>";
echo "<hr>";

echo "<h2>twitterInfo method</h2>";
echo "<p>Get informations on a Twitter account without API and oAuth integration. There is also an interactive demo <a href=\"http://www.barattalo.it/demo/minibots.php\">here</a>.</p>";
$mb->use_file_get_contents="yes"; // this works with twitter method for my hosting
$info = $mb->twitterInfo("dailybestnet");
echo "<pre>";
echo "Search: dailybestnet\n";
print_r($info);
echo "</pre>";
echo "<hr>";

echo "<h2>getInstagramPics method</h2>";
echo "<p>Get informations from an Instagram profile, without API and oAuth integration. There is also an interactive demo <a href=\"http://www.barattalo.it/demo/minibots.php\">here</a>. In the example shows informations on my user but the method grabs infos also on last pics.</p>";
$mb = new Minibots();
$info = $mb->getInstagramPics("giuliopons");
if(is_array($info)) {

	echo "<pre>";
	print_r($info);
	echo "</pre>";
}else {
	echo "Sorry this method probably doesn't work on this server.";
}
echo "<hr>";

echo "<h2>getInstagramPicsByTag method</h2>";
echo "<p>Get images with a specified hashtag. There is also an interactive demo <a href=\"http://www.barattalo.it/demo/minibots.php\">here</a>. In the example shows images tagged with #milano.</p>";
$mb = new Minibots();
$info = $mb->getInstagramPicsByTag("milano");
echo "<pre>";
print_r($info["user"]);
print_r($info["pics"]);
echo "</pre>";
echo "<hr>";


if(isset($info["pics"][0]["code"])) {
	echo "<h2>getInstagramPic method</h2>";
	echo "<p>Get informations from this instagram image <b>http://instagram.com/p/".$info["pics"][0]["code"]."/</b>:</p>";
	$mb = new Minibots();
	$info = $mb->getInstagramPic($info["pics"][0]["code"]);
	echo "<pre>";
	print_r($info);
	echo "</pre>";
	echo "<hr>";
}


echo "<h2>getInstagramFollowers method</h2>";
echo "<p>Get instragram followers number for user <b>giuliopons</b>:</p>";
$mb = new Minibots();
$info = $mb->getInstagramFollowers("giuliopons");
echo "<pre>";
print_r($info);
echo "</pre>";
echo "<hr>";



echo "<h2>getPage, makeAbsolute, attr and findLinks method</h2>";
echo "<p>Get all the links in this page <b>http://www.barattalo.it/mini-bots-php-class/</b>:</p>";
$mb = new Minibots();
$target = "http://www.barattalo.it/mini-bots-php-class/";
$page = $mb->getPage($target);
$links = $mb->findLinks($target,$page);
echo "<pre>";
print_r($links);
echo "</pre>";
echo "<hr>";

echo "<h2>findEmails method</h2>";
echo "<p>Get all the emails in this page <b>http://www.barattalo.it/giulio-pons-appunti/</b>:</p>";
$mb = new Minibots();
$target = "http://www.barattalo.it/giulio-pons-appunti/";
$page = $mb->getPage($target);
$emails = $mb->findEmails($page);
echo "<pre>";
print_r($emails);
echo "</pre>";
echo "<hr>";


echo "<h2>attr method</h2>";
echo "<p>Get meta content description attribute from <b>http://www.barattalo.it</b>:</p>";
$web_page = $mb->getPage("http://www.barattalo.it");
preg_match_all('#<meta([^>]*)(.*)>#Uis', $web_page, $meta_array);
for($i=0;$i<count($meta_array[0]);$i++) {
	if (strtolower($mb->attr($meta_array[0][$i],"name"))=='description') {
		$d = trim($mb->attr($meta_array[0][$i],"content"));
		echo "<pre>";
		echo "This is the page description: ".utf8_decode($d);
		echo "</pre>";
	}
}
echo "<hr>";




?>