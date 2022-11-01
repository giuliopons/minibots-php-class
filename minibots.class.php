<?php
/* ------------------------------------------------------------------------- */
/* minibots.class.php Ver.4                                                  */
/* ------------------------------------------------------------------------- */
/* Mini Bots class is a small php class that allows you to                   */
/* use some free web seriveces online to retrive usefull data                */
/* and infos.                                                                */
/* ------------------------------------------------------------------------- */

Class Minibots 
{
	private $file_size = 0;
	private $max_file_size = 5000;
	private $file_downloaded = "";
	public  $use_file_get_contents = "no" ;   //   [ yes | no | https ]
	                                             //   yes = aways, no = always cURL, https = only for https calls

	public function __construct () {
		
	}

	/*
	get the IP address of the connected user
	*/
	public function getIP() {
		$ip="";
		if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
		else if(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
		else if(getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
		else $ip = "";
		return $ip;
	}



	/*
		add days to a date
	*/
	public function dayadd($days,$date=null , $format="d/m/Y"){
		return date($format,strtotime($days." days",strtotime( $date ? $date : date($format) )));
	}




	/*
		this function return the html attribute of a given tag
		(use for scraping data)
	*/
	public function attr($s,$attrname) {
		preg_match_all('#\s*('.$attrname.')\s*=\s*["]([^"]*)["]\s*#i', $s, $x); 
		if (count($x)>=3 && isset($x[2][0])) return isset($x[2][0]) ? $x[2][0] : "";
		preg_match_all('#\s*('.$attrname.')\s*=\s*[\']([^\']*)[\']\s*#i', $s, $x); 
		if (count($x)>=3 && isset($x[2][0])) return isset($x[2][0]) ? $x[2][0] : "";
		return "";
	}



	/*
		return the array of matches when searching for a 
		tag serie while scraping html
		$return can be "ALL" | "INNER" | "OUTER"
	*/
	public function getTags($tagname,$text,$return="ALL") {
		$tagname = strtolower($tagname);
		if($tagname=="img" || $tagname=="br" || $tagname=="input") {
			// autoclose
			preg_match_all('#<'.$tagname.'[^>]*?>#Uis', $text, $s);
		} else {
			preg_match_all('#<'.$tagname.'[^>]*?>(.*)</'.$tagname.'>#Uis', $text, $s);
		}
		if($return=="ALL") return $s;
		if($return=="INNER") return $s[1];
		if($return=="OUTER") return $s[0];
		return $s;
	}




	/* 
		this function makes a relative url an absolute merging
		properly the url and the link
	*/
	public function makeabsolute($url,$link) {
		$p = parse_url($url);
		if (strpos( $link,"http://")===0 ) return trim($link);
		if (strpos( $link,"https://")===0 ) return trim($link);
		if($p['scheme']."://".$p['host']==$url && $link[0]!="/" && $link!=$url) return trim($p['scheme']."://".$p['host']."/".$link);
		if (strpos( $link, "/")===0) return trim($p['scheme']."://".$p['host'].$link);
		return trim(str_replace(substr(strrchr($url, "/"), 1),"",$url).$link);
	}



	/*
		Retrieves a page with some parameters in POST.
		The parameters should be passed like this:
		$vars = array("name"=>value, "name2"=>value2);
	*/
	public function getPagePost($url,$vars) {
		if (!function_exists("curl_init")) die("getPagePost needs CURL module, please install CURL on your php.");
		$s = "";
		foreach($vars as $k=>$v) $s.= ($s?"&":"") . $k."=".rawurlencode($v);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $s);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curl_results = curl_exec ($curl);
		curl_close ($curl);
		return $curl_results;
	}






	/*
		this method gets a page, use this to build your crawler, it calls curl.
		on some servers file_get_contents works better, so it uses 
		the parameter "use_file_get_contents" to switch from curl to file_get_contents
		doesn't handle POST data.
	*/
	public function getPage($url, $max_file_size=0) {

		$VERBOSE = false;

		if (!function_exists("curl_init")) die("getPage needs CURL module, please install CURL on your php.");
		$ch = curl_init();

		$https = preg_match("/^https/i",$url);
		//echo $url;
		if($this->use_file_get_contents=="yes") return file_get_contents($url);

		if($https && $this->use_file_get_contents=="https") {
			return file_get_contents($url);
		}

		/*
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'authority: www.instagram.com',
			'pragma: no-cache',
			'cache-control: no-cache',
			'upgrade-insecure-requests: 1',
			'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36',
			'sec-fetch-mode: navigate',
			'sec-fetch-user: ?1',
			'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*'.'/*;q=0.8,application/signed-exchange;v=b3',
			'sec-fetch-site: none',
			'accept-encoding: gzip, deflate',
			'accept-language: en-US,en;q=0.9,it;q=0.8',
		));
		*/
		//$VERBOSE = true;

		if($VERBOSE) {
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			$verbose = fopen('./tmp/verbose.txt', 'w+');
			curl_setopt($ch, CURLOPT_STDERR, $verbose);
		}

		preg_match("/:([0-9]+)/i", $url, $matches);
		if(isset($matches[1]) && $matches[1] > 1) {
			$port = $matches[1];
			//$url = str_replace( $matches[0],"",$url);
			curl_setopt($ch, CURLOPT_PORT, $port);           //Set the port number
			//echo $port;
			//echo "<br>".$url."<br>";
		}

		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);       // Fail on errors
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects (abilitato per wikipedia)
		if($https) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			// curl_setopt($ch, CURLOPT_CERTINFO, true);
			// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
		}
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate,sdch');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);     // return into a variable
		//curl_setopt($ch, CURLOPT_PORT, 80);           //Set the port number
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);          // times out after 15s
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36");   // Webbot name
		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
			'accept-language:en-US,en;q=0.8' 
		));
		curl_setopt($ch , CURLOPT_COOKIEJAR, './tmp/cookies.txt');
		curl_setopt($ch , CURLOPT_COOKIEFILE, './tmp/cookies.txt');
		
		if($max_file_size>0) {
			// if you want to reduce download size, set the byte size limit
			$this->max_file_size = $max_file_size;
			curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'on_curl_header'));
			curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, 'on_curl_write'));
		}
		$web_page = curl_exec($ch);
		if(strlen($web_page) <= 1 && $max_file_size>0) {
			$web_page = $this->file_downloaded;
		}
		if(curl_error($ch)) echo (curl_error($ch));

		if($VERBOSE) {
			/* devug verbose */
			rewind($verbose);
			$verboseLog = stream_get_contents($verbose);
			echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
		}

		return $web_page;
	}




	/*
		this method returns all the links inside a given url
		skip bad urls (javascript, mailto...), make all urls absolute
		flags to skip some links to particular extensions (pdf,zip,jpg...) 
		and to follow external urls.
	*/
	public function findLinks($url, $web_page, $FOLLOW_EXTERNAL=false, $SKIP_EXTENSIONS="") {
		$stop_host = "";
		$exts = array();
		if($FOLLOW_EXTERNAL==false) {
			$temp = parse_url($url);
			if(!$temp['host']) {
					("Can't determine host, plaese check starting url: ".$url);
			} else {
				$stop_host = $temp['host'];
			}
		}
		if($SKIP_EXTENSIONS){
			$exts = explode(",",$SKIP_EXTENSIONS);
			if(empty($exts)) $SKIP_EXTENSION="";
		}

		//search links
		preg_match_all('#<a([^>]*)?>(.*)</a>#Uis', $web_page, $a_array);
		$outAr = array();
		if(isset($a_array[1])) {
			foreach($a_array[1] as $link) {
				$href = $this->attr($link,"href");
				if($href!="" 
					&& !preg_match("/^javascript:/",$href) 
					&& !preg_match("/^#/",$href) 
					&& !preg_match("/^mailto:/",$href)
				) {
					$temp = $this->makeabsolute($url,str_replace(" ","%20",$href));
					if($FOLLOW_EXTERNAL==false && $stop_host) {
						$temp2 = parse_url($temp);
						if($temp2['host']!=$stop_host) $temp="";
					}
					if($SKIP_EXTENSIONS){
						foreach($exts as $e){
							if(preg_match("/(\.".$e.")$/",$temp)) { $temp=""; break;}
						}
					}
					if($temp) $outAr[] = $temp;
				}
			}
		}
		return array_unique($outAr);
	}




	/*
		this method returns all the emails contained
		in the page.
	*/
	public function findEmails($page) {
		preg_match_all(
			'/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}\b/i',
			$page,
			$matches
		);
		$outAr = array();
		foreach(array_unique($matches[0]) as $email) {
			//echo "<code>".$email."</code><br/>";
			$outAr[] = $email;
		}
		return $outAr;
	}




	/*
		remove all html and tags from a url and get only the text
		TO DO: could be improved to use only useful tags (headings and paragraphs)
	*/
	public function justText($text) {
		$text = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $text);
		$text = preg_replace("/[\n\r\t]/"," ",strip_tags($text));
		$text = preg_replace("/(  +)/"," ",strip_tags($text));
		return trim($text);
	}


	/*
		private function to handle file size check and prevent downloading too much
	*/
	private function on_curl_header($ch, $header) {
		$trimmed = rtrim($header);   
		if (preg_match('/^Content-Length: (\d+)$/i', $trimmed, $matches)) {
			$file_size = (float)$matches[1];
			if ($file_size > $this->max_file_size) {
				// stop if bigger
				return -1;
			}
		}
		return strlen($header);
	}




	/*
		like the previous one, private function to handle file size check and prevent downloading too much
	*/
	private function on_curl_write($ch, $data) {
		$bytes = strlen($data);
		$this->file_size += $bytes;
		$this->file_downloaded .= $data;
		if ($this->file_size > $this->max_file_size) {
			// stop if bigger
			return -1;
		}
		return $bytes;
	}




	/*
		private function to get remote file size
		TO DO: Does it work with https?
	*/
	private function getRemoteFileSize($url) {
		if (substr($url,0,4)=='http') {
			$x = array_change_key_case(get_headers($url, 1),CASE_LOWER);
			if ( strcasecmp($x[0], 'HTTP/1.1 200 OK') != 0 ) { $x = $x['content-length'][1]; }
			else { $x = $x['content-length']; }
		}
		else { $x = @filesize($url); }
		return $x;
	} 




	/*
		private function to get the http response code for a url
		TO DO: Does it work with https?
	*/
	private function getHttpResponseCode($url) {
		if (!function_exists("curl_init")) die("getHttpResponseCode needs CURL module, please install CURL on your php.");
		// 404 not found, 403 forbidden...
		$ch = @curl_init($url);
		@curl_setopt($ch, CURLOPT_HEADER, TRUE);
		@curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$status = array();
		preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($ch) , $status);
		return isset($status[1]) ? $status[1] : null;
	}




	/*
		Copy a remote url to your local server
	*/
	public function copyFile($url,$filename){
		// copy remote file to server
		$file = fopen ($url, "rb");
		if (!$file) return false; else {
			$fc = fopen($filename, "wb");
			while (!feof ($file)) {
				$line = fread ($file, 1028);
				fwrite($fc,$line);
			}
			fclose($fc);
			return true;
		}
	}




	/*
		Google spell suggest.
		Usage example:
		$obj = New Minibots();
		$word = $obj->doSpelling("wikipezia"); 
		--> wikipedia
	*/
	public function doSpelling($q) {
		// grab google page with search
		$web_page = file_get_contents( "https://www.google.it/search?q=" . urlencode($q) );
		// put anchors tag in an array
		preg_match_all('#<a([^>]*)?>(.*)</a>#Us', $web_page, $a_array);
		for($j=0;$j<count($a_array[0]);$j++) {
			// find link with spell suggestion and return it
			if(stristr($a_array[0][$j],"spell=1")) return strip_tags($a_array[0][$j]);
			//if(stristr($a_array[0][$j],"class=\"spell\"")) return strip_tags($a_array[0][$j]);
		}
		return $q;	//if no results returns the q value
	}




	/*
		Make a tiny url with tinyurl.com free service.
		Usage example:
		$obj = New Minibots();
		$short_url = $obj->doShortURL("http://www.this.is.a.long.url/words-words-words"); 
		--> http://tinyurl.com/aiIAa (fake values)
	*/
	public function doShortURL($longUrl) {
		$short_url= file_get_contents('http://tinyurl.com/api-create.php?url=' . $longUrl);
		return $short_url;
	}




	/*
		Convert back from a tiny url to a long url, work also with urls of other services
		like goo.gl, bit.ly and others. This method works to handle all redirects, not only
		the ones from shorten url services.
		Usage example:
		$obj = New Minibots();
		$long_url = $obj->doShortURLDecode("http://tinyurl.com/aiIAa"); 
		--> http://www.this.is.a.long.url/words-words-words (fake values)
	*/
	public function doShortURLDecode($url) {
		if (!function_exists("curl_init")) die("doShortURLDecode needs CURL module, please install CURL on your php.");
		$ch = @curl_init($url);
		@curl_setopt($ch, CURLOPT_HEADER, TRUE);
		@curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$out = @curl_exec($ch);
		preg_match('/Location: (.*)\n/i', $out, $a);
		if (!isset($a[1])) return $url;
		return trim($a[1]);
	}

	/*
		Check if an mp3 URL is an mp3.
		Usage example:
		$obj = New Minibots();
		$check = $obj->checkMp3("http://www.artintent.it/Kalimba.mp3"); 
		--> true
	*/
	public function checkMp3($url) {
		if (!function_exists("curl_init")) die("checkMp3 needs CURL module, please install CURL on your php.");
		$a = parse_url($url);
		if(checkdnsrr(str_replace("www.","",$a['host']),"A") || checkdnsrr(str_replace("www.","",$a['host']))) {
			$ch = @curl_init();
			@curl_setopt($ch, CURLOPT_URL, $url);
			@curl_setopt($ch, CURLOPT_HEADER, 1);
			@curl_setopt($ch, CURLOPT_NOBODY, 1);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			@curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			$results = explode("\n", trim(curl_exec($ch)));
			$mime = "";
			foreach($results as $line) {
				if (strtok($line, ':') == 'Content-Type') {
					$parts = explode(":", $line);
					$mime = trim($parts[1]);
				}
			}
			return $mime=="audio/mpeg";
		} else {
			return false;
		}
	}




	/*
		Check if a URL exists, like file_exists, but for remote urls.
		Usage example:
		$obj = new Minibots();
		$check = $obj->url_exists("http://en.wikipedia.org/wiki/Barack_Obama"); 
		--> true
	*/
	public function url_exists($url) {
		return ($this->getHttpResponseCode($url) == 200);
	}




	/*
		Check if an email is correct, this function try to validate email address by connecting to the SMTP server.
		It returns true when email is ok or returns an array(msg, error code) when fails.
		The second parameter, $from_address should be an email with permission to send mail from your domain.
		Usage example:
		$obj = new Minibots();
		$check = $obj->doSMTPValidation("pons@rockit.it","info@barattalo.com");
		--> true
	*/
	function doSMTPValidation($email, $from_address="", $debug=false) {
		if (!function_exists('checkdnsrr')) die("This function requires checkdnsrr function, check your Php version.");
		$output = "";
		// --------------------------------
		// Check email syntax with regular expression, for both destination and sender
		// --------------------------------
		if (!$from_address) $from_address = $_SERVER["SERVER_ADMIN"];
		if (!preg_match('/^([a-zA-Z0-9\._\+-]+)\@((\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?))$/', $from_address)) {
			$error = "From email is wrong.";
		} elseif (preg_match('/^([a-zA-Z0-9\._\+-]+)\@((\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?))$/', $email, $matches)) {
			$domain = $matches[2];
			// --------------------------------
			// get DNS MX records
			// --------------------------------
			if(getmxrr($domain, $mxhosts, $mxweight)) {
				for($i=0;$i<count($mxhosts);$i++){
					$mxs[$mxhosts[$i]] = $mxweight[$i];
				}
				asort($mxs);
				$mailers = array_keys($mxs);
			} elseif(checkdnsrr($domain, 'A')) {
				$mailers[0] = gethostbyname($domain);
			} else {
				$mailers=array();
			}
			$total = count($mailers);
			if($total > 0) {
				// --------------------------------
				// Check if mail servers accept email
				// --------------------------------
				for($n=0; $n < $total; $n++) {
					if($debug) { $output .= "Checking server $mailers[$n]...\n";}
					$connect_timeout = 2;
					$errno = 0;
					$errstr = 0;
					//$from_address = str_replace("@","",strstr($from_address, '@'));

					// --------------------------------
					// Open socket
					// --------------------------------
					if($sock = @fsockopen($mailers[$n], 25, $errno , $errstr, $connect_timeout)) {
						$response = fgets($sock);
						if($debug) {$output .= "Opening up socket to $mailers[$n]... Success!\n";}
						stream_set_timeout($sock, 5);
						$meta = stream_get_meta_data($sock);
						if($debug) { $output .= "$mailers[$n] replied: $response\n";}
						// --------------------------------
						// Errors or time out
						// --------------------------------
						if(!$meta['timed_out'] && !preg_match('/^2\d\d[ -]/', $response)) {
							$code = trim(substr(trim($response),0,3));
							if ($code=="421") {
								// 421 #4.4.5 Too many connections to this host.
								$error = $response;
								break;
							} else {
								if($response=="" || $code=="") {
									// There was an error, but not clear
									$code = "0";
								}
								$error = "Error: $mailers[$n] said: $response\n";
								break;
							}
							break;
						}
						// talk to smtp server with its language
						// try to ask for recipient but don't send email
						$cmds = array(
							"HELO $from_address",
							"MAIL FROM: <{$from_address}>",
							"RCPT TO: <$email>",
							"QUIT",
						);
						foreach($cmds as $cmd) {
							$before = microtime(true);
							fputs($sock, "$cmd\r\n");
							$response = fgets($sock, 4096);
							$t = round(1000 * (microtime(true)-$before));
							if($debug) {$output .= $cmd."\n". "($t ms) ". $response;}
							if(!$meta['timed_out'] && preg_match('/^5\d\d[ -]/', $response)) {
								$code = trim(substr(trim($response),0,3));
								if ($code<>"552") {
									$error = "Unverified address: $mailers[$n] said: $response";
									break 2;
								} else {
									$error = $response;
									break 2;
								}
								// --------------------------------
								// Errors 554 and 552 are over quota, so the email is ok, but the full.
								// 554 Recipient address rejected: mailbox overquota
								// 552 RCPT TO: Mailbox disk quota exceeded
								// --------------------------------
							}
						}
						fclose($sock);
						if($debug) { $output .= "Succesful communication with $mailers[$n], no hard errors, assuming OK\n";}
						break;
					} elseif($n == $total-1) {
						$error = "None of the mailservers listed for $domain could be contacted";
						$code = "0";
					}
				}
			} elseif($total <= 0) {
				$error = "No usable DNS records found for domain '$domain'";
			}
			
		} else {
			$error = 'Email is wrong.';
		}
		if($debug) {
			print nl2br(htmlentities($output));
		}
		if(!isset($code)) $code="n.a.";
		if(isset($error)) return array($error,$code); else return true;
	}


function betweenTags($s,$a,$b) {
	$s1  =  str_replace($a,"",stristr($s,$a));
	if($s1) {
		$s2 = str_replace(stristr($s1,$b), "", $s1);
	}
	return $s2;
}


	/*
		Fetch info for a specified URL, maximages and maxkbimg are usefull to get useful images,
		so if there is a small icon this image will be skipped, to find an image bigger.
		Usage example:
		$obj = new Minibots();
		$infos = $obj->getUrlInfo("http://piccsy.com/2013/10/cute-dog"); 
		--> array(
			[keywords] => Piccsy, images, beautiful images, creative images, image discovery, discovery, browse, galleries, piccs
			[description] => Beautiful, inspirational and creative images from Piccsy. Thousands of Piccs from all our streams, for you to browse, enjoy and share with a friend.
			[title] => Piccsy :: cute dog
			[favicon] => http://piccsy.com/favicon.ico
			[images] => Array
				(
					[0] => http://img1.piccsy.com/cache/images/03/f1/69269c21__4500deb6_0ec40_cb4-post.jpg
					[1] => http://piccsy.com/piccsy/images/layout/logo/e02f43.200x200.jpg
				)
		)
	*/
	public function getUrlInfo($url,$maximages=5,$maxkbimg=10,$DECODESHORT=false) {
		//echo $url;
		if($DECODESHORT) {
		if (!function_exists("curl_init")) die("getUrlInfo needs CURL module, please install CURL on your php.");
			$url = $this->makeabsolute($url, $this->doShortURLDecode($url));
			$web_page = $this->getPage($url, $maximages == 0 ? 5000 : 0);
		} else {
			$web_page = file_get_contents($url);
		}
		$data['keywords']="";
		$data['description']="";
		$data['title']="";
		$data['favicon']="";
		$data['images']=array();
		//search title
		preg_match_all('#<title([^>]*)?>(.*)</title>#Uis', $web_page, $title_array);
		$data['title'] = trim($title_array[2][0]);
		//search keywords and description
		preg_match_all('#<meta([^>]*)(.*)>#Uis', $web_page, $meta_array);
		//print_r($meta_array);
		for($i=0;$i<count($meta_array[0]);$i++) {
			if (strtolower($this->attr($meta_array[0][$i],"name"))=='description') 
				$data['description'] = trim($this->attr($meta_array[0][$i],"content"));
			if (strtolower($this->attr($meta_array[0][$i],"name"))=='keywords') 
				$data['keywords'] = trim($this->attr($meta_array[0][$i],"content"));
		}
		//search favicon
		preg_match_all('#<link([^>]*)(.*)>#Uis', $web_page, $link_array);
		for($i=0;$i<count($link_array[0]);$i++) {
			$rel = strtolower($this->attr($link_array[0][$i],"rel"));
			if ($rel=='shortcut icon' || $rel =="icon") 
				$data['favicon'] = $this->makeabsolute($url,$this->attr($link_array[0][$i],"href"));
		}

		// search images on open graph and schema org
		preg_match_all('#<meta([^>]*)(.*)/?>#Uis', $web_page, $imgs_array);
		$imgs = array();
		for($i=0;$i<count($imgs_array[0]);$i++) {
			$att1 = $this->attr($imgs_array[0][$i],"property");
			$att2 = $this->attr($imgs_array[0][$i],"itemprop");
			if ($att1 == "og:image" || $att2=="image") {
				$src = trim($this->attr($imgs_array[0][$i],"content"));
				array_push($imgs,$src);
				break;
			}
		}

		if(stristr($url,"amazon")) {

			// description
			$desc = trim($this->betweenTags($web_page,"bookDescEncodedData =",'",'));
			$desc = trim( urldecode(str_replace("\"","",$desc)) );

			if(strlen($desc)<5) {
				$desc = trim($this->justText($this->betweenTags($web_page,"<div id=\"feature-bullets\" class=\"a-section a-spacing-medium a-spacing-top-small\">",'</div>')));
				

			}

			if(strlen($desc)<5) {
				$d = "";
				$ar = $this->getTags("noscript",$web_page,"INNER");
				if(!empty($ar)) {
					$d="";
					foreach($ar as $o) {
						$o = trim($this->justText($o));
						if ($o!="") { $d = $o; break;}
					}
				}
			}

			$data['description']= $desc;

			
			// IL PRIMO PREZZO
			// <span id="priceblock_ourprice" class="a-size-medium a-color-price">EUR 12,59</span>
			preg_match_all("/<td class=\"a-text-right dp-price-col\">(.*)<\/td>/imsU",$web_page,$price); //price
			if(isset($price[1][0]) && !empty($price[1][0])) {
				$data["price"] = $this->justText($price[1][0]);
			}

			preg_match_all("/<img ([^>]*)>/i",$web_page,$images2); // images
			if(isset($images2[1]) && !empty($images2[1])) {
				foreach($images2[0] as $pic) {
					if(stristr( $this->attr($pic,"class"), "frontImage")) {
						array_push($imgs,$this->attr($pic,"src"));
						$data['title2'] = $this->attr($pic,"alt");
					}
				}
			}
			/*
			<img alt="Temporary Road: (una) Vita di Franco Battiato di [Franco Battiato]" src="https://m.media-amazon.com/images/I/41nLJ+swD4L._SY346_.jpg" onload="this.onload='';setCSMReq('af');" data-a-image-name="ebooksImageBlockFront" class="a-dynamic-image frontImage" id="ebooksImgBlkFront" width="222px" data-a-dynamic-image="{&quot;https://m.media-amazon.com/images/I/41nLJ+swD4L._SY346_.jpg&quot;:[222,346],&quot;https://m.media-amazon.com/images/I/41nLJ+swD4L.jpg&quot;:[321,500]}" data-a-manual-replacement="true">
			*/



		} else {


			// search images big enough
			preg_match_all('#<img([^>]*)(.*)/?>#Uis', $web_page, $imgs_array);
			for($i=0;$i<count($imgs_array[0]);$i++) {
				if ($src = $this->attr($imgs_array[0][$i],"src")) {
					$src = $this->makeabsolute($url,$src);
					$kb = 1;
					if($maxkbimg>0) {
						$kb = $this->getRemoteFileSize($src);
					}
					if(!in_array($src,$imgs) && $kb>$maxkbimg*1000) array_push($imgs,$src);
				}
				if (count($imgs)>$maximages-1) break;
			}

		}
		$data['images']=$imgs;


		$h = get_headers($url, 1);
		if (!($h || strstr($h[0], '200') === FALSE)) $data['lastmodified'] = $h['Last-Modified'];
			else $data['lastmodified']="";

		return $data;
	}




	/*
		Get info for video on Youtube or on Vimeo
		return an array with title, descriptiom, thumb
		$obj = new Minibots();
		$infos = $obj->getVideoUrlInfo("http://www.youtube.com/watch?v=KUVlrdfKowk");
		---> Array
		(
			[title] => Lavoratooooooooori - YouTube
			[description] => Non sapete che mettere di carino nell 'out of office quando andate in ferie?? Ecco...
			[thumb] => http://img.youtube.com/vi/KUVlrdfKowk/1.jpg
		)
	*/
	public function getVideoUrlInfo($url) {
		if (!function_exists("curl_init")) die("getVideoUrlInfo needs CURL module, please install CURL on your php.");
		$url = $this->makeabsolute($url, $this->doShortURLDecode($url));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);       // Fail on errors
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);     // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);          // times out after 15s
		//curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'on_curl_header'));
		$web_page = curl_exec($ch);


		//search title
		preg_match_all('#<title([^>]*)?>(.*)</title>#Uis', $web_page, $title_array);
		$title = isset($title_array[2][0]) ? trim(preg_replace('/ +/', ' ', $title_array[2][0])) : "";





		//search keywords and description
		preg_match_all('#<meta([^>]*)(.*)>#Uis', $web_page, $meta_array);
		//print_r($meta_array);
		$description="";
		for($i=0;$i<count($meta_array[0]);$i++) 
			if (strtolower($this->attr($meta_array[0][$i],"name"))=='description') 
				$description = $this->attr($meta_array[0][$i],"content");


		$viewCount = null;
		$like = null;
		$dislike = null;

		//URL examples (Youtube):
		// http://www.youtube.com/v/Md1E_Rg4MGQ&hl=en&fs=1&
		// http://www.youtube.com/watch?v=Md1E_Rg4MGQ&feature=aso
		preg_match_all('/^https?:\/\/www.youtube.com\/(v\/|watch\?v=)([^&]*)(.*)$/', $url, $yarr);
		if(isset($yarr[2][0])) {
			$thumb = "https://img.youtube.com/vi/".$yarr[2][0]."/1.jpg";
			preg_match_all("#\\\?\"viewCount\\\?\":\\\?\"([0-9]*)\\\?\"#", $web_page, $countArray);
			$viewCount = isset($countArray[1][0]) ? $countArray[1][0] : "";





		}
	
		// Check vor Vimeo urls:
		preg_match_all('/^https?:\/\/vimeo.com\/([0-9]*)$/', $url, $varr);
		if(isset($varr[1][0])) {
			$vimeoInfo = $this->getVimeoInfo($varr[1][0]);
			$thumb = $vimeoInfo["thumbnail_small"];
		}
		
		return array("title"=>$title,"description"=>$description,"thumb"=>$thumb,"viewCount"=>$viewCount);
	}



	/*
		Get Facebook counters for a url using Facebook Apis.
		return an array with title, descriptiom, thumb
		$obj = new Minibots();
		$infos = $obj->readFacebookCounters("http://www.dailybest.it/2013/03/05/vita-programmatore-gif-animate/","xxxxx","xxxxx");
		---> Array
		(
			[total] => 7109
			[likes] => 3438
			[shares] => 1937
			[clicks] => 0
			[comments] => 1734
			...
		)
	*/
	public function readFacebookCounters($url,$appid="",$secret="") {
		// facebook counter are no longer public since august 2016
		// you need an appid and appsecret to get data

		if($appid=="" || $secret=="") {
			return array("error"=>"You need an appid and appsecret to get data");
		}

		// returns the counters of facebook likes + shares + comments...
		$fbtoken = $appid."|".$secret;
		$s = file_get_contents("https://graph.facebook.com/v2.4/?access_token=".$fbtoken."&id=".urlencode($url));
		$ar = json_decode($s);
		if(isset($ar)) {
			return array(
				"total"=>$ar->share->share_count + $ar->share->comment_count,
				"likes"=>"",
				"shares"=>$ar->share->comment_count,
				"clicks"=>"",
				"comments"=>$ar->share->share_count,
				"description"=>$ar->og_object->description,
				"title"=>$ar->og_object->title,
				"updated_time"=>$ar->og_object->updated_time,
				"id"=>$ar->og_object->id,

			);
		}
		return false;
	}




	/*
		Read Facebook Page counters using the informations in the meta
		description tags:
		<meta name="description" content="Dailybest. 104,829 likes &#xb7; 4,469 talking about this. Dailybest &#xe8; un magazine online dedicato al meglio della cultura digitale e della creativit&#xe0; italiana..." />
	*/
	public function readFacebookPageCounters($url) {
		$s = $this->getPage($url);
		return $s;
		preg_match_all("#<meta ([^>]*)>#",$s,$matches);
		foreach($matches[0] as $m) {
			//print_r($m);
			$name = trim($this->attr($m,"name"));
			if($name=="description") {
				$content = $this->attr($m,"content");
				$content = preg_replace("/\&\#x([0-9a-f]*);/","",$content);
				while(stristr($content,"  ")) $content = preg_replace("/  /"," ",$content);
				$content = preg_replace("/ /"," xxx ",$content);
				preg_match_all("/ ([0-9,M]*) /U",$content,$counters);
				preg_match_all("/PagesLikesCountDOMID\"><span([^>]*)>([0-9\.,]*) /Uis",$s,$counterpreciso);
				//print_r($counterpreciso);
				return array(
					"likes" => preg_replace("/[^0-9M]/","",$counters[0][0]),
					"talking" => preg_replace("/[^0-9M]/","",$counters[0][1]),
					"here" => preg_replace("/[^0-9M]/","",$counters[0][2]),
					"exact" =>  preg_replace("/[^0-9]/","",$counterpreciso[2][0]),
				);
			}
		}

		return false;
	}




	/*
		Get number of tweets with the specified url counters for a url using Facebook Apis.
		return a number
		$obj = new Minibots();
		$infos = $obj->readTwitterCounters("http://www.dailybest.it/2013/03/05/vita-programmatore-gif-animate/");
		---> 175
	*/
	public function readTwitterCounter($url) {
		// since november 2015 counters are no longer public
		// to get them you have to register your site on http://opensharecount.com/
		// read here for more info: http://www.barattalo.it/other/how-to-bring-back-the-twitter-count/

		$s = file_get_contents("https://opensharecount.com/count.json?url=".urlencode($url));
		$ar = json_decode($s);
		if(isset($ar->count)) return $ar->count; else return 0;
	}



	/*
		Get number of Google +1s with the specified url using hidden Google Apis.
		return a number
		$obj = new Minibots();
		$infos = $obj->readGooglePlusCounter("http://www.dailybest.it/2013/03/05/vita-programmatore-gif-animate/");
		---> 175
	*/
	public function readGooglePlusCounter($url) {
		if (!function_exists("curl_init")) die("readGooglePlusCounter needs CURL module, please install CURL on your php.");
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		$curl_results = curl_exec ($curl);
		curl_close ($curl);
		$json = json_decode($curl_results, true);
		return intval( $json[0]['result']['metadata']['globalCounts']['count'] );
	}





	/*
		Get the keyword suggestion from google for a word and return an array with suggested keywords.
		$obj = new Minibots();
		$infos = $obj->googleSuggestKeywords("berlusconi");
		---> Array
		(
			[0] => berlusconi
			[1] => berlusconi news
			[2] => berlusconi bunga bunga
			[3] => berlusconi bunga bunga party
			[4] => berlusconi net worth
			[5] => berlusconi quotes
			[6] => berlusconi ruby
			[7] => berlusconi hump
			[8] => berlusconi trial
			[9] => berlusconi jail
		)
	*/
	public function googleSuggestKeywords($k) {
		if (!function_exists("curl_init")) die("googleSuggestKeywords needs CURL module, please install CURL on your php.");
		$k = explode(" ",$k); $k = $k[0];
		$u = "http://google.com/complete/search?output=toolbar&q=" . $k;
		$xml = simplexml_load_string(utf8_encode(file_get_contents($u)));

		/*
		<toplevel><CompleteSuggestion><suggestion data="berlusconi"/></CompleteSuggestion><CompleteSuggestion><suggestion data="berlusconi monza"/></CompleteSuggestion><CompleteSuggestion><suggestion data="berlusconi et�"/></CompleteSuggestion><CompleteSuggestion><suggestion data="berlusconi news"/></CompleteSuggestion><CompleteSuggestion><suggestion data="berlusconi salvini"/></CompleteSuggestion><CompleteSuggestion><suggestion data="berlusconi milan"/></CompleteSuggestion><CompleteSuggestion><suggestion data="berlusconi salute"/></CompleteSuggestion><CompleteSuggestion><suggestion data="berlusconi oggi"/></CompleteSuggestion><CompleteSuggestion><suggestion data="berlusconi malato"/></CompleteSuggestion><CompleteSuggestion><suggestion data="berlusconi patrimonio"/></CompleteSuggestion></toplevel>
		*/

		// Parse the keywords 
		$result = $xml->xpath('//@data');
		$ar = array();
		while (list($key, $value) = each($result)) $ar[] = (string)$value;
		return $ar;
	}





	/*
		Get the latitude and longitude from an address using Geocode.xyz. If succeed returns an array,
		else return false.
		$obj = new Minibots();
		$poi = $obj->getLatLong("milan, italy");
		---> Array
		(
			[lat] => 45.465454000000001
			[long] => 9.1865159999999992
			[complete] => ... more data
		)

		10/02/2019
		https://geocode.xyz/[request]&auth=APIKEY

	*/
	public function getLatLong($address, $key=""){
		if($key=="") return "needed auth key, register for free on geocode.xyz";
		$_url = "https://geocode.xyz/".rawurlencode($address)."?geoit=xml&auth=".$key;

		$_result = $this->getPage($_url);

		if($_result) {
			$obj = simplexml_load_string($_result);
			if(isset($obj->latt)) {
				$coords['lat'] = (float)$obj->latt[0];
				$coords['long'] = (float)$obj->longt[0];
				$coords['complete']=$obj;
				return $coords;
			}
		}
		return false;
	}

	public function getLatLongBis($address, $key=""){
		if($key=="") return "needed auth token, register for free on locationiq.com";
		$_url = "eu1.locationiq.com/v1/search.php?key=".$key."&q=".rawurlencode($address)."&format=json";

		$_result = $this->getPage($_url);
		if($_result) {
			$obj = json_decode($_result);
			if(isset($obj[0]->lat)) {
				$coords['lat'] = (float)$obj[0]->lat;
				$coords['long'] = (float)$obj[0]->lon;
				$coords['complete']=$obj;
				return $coords;
			}
		}
		return false;
	}





	/*
		Get the WikiPedia definition for a search string. If succeeds returns an object,
		else return false.
	*/
	public function wikiDefinition($s,$wikilang="en",$imagewidth=600) {

		$url = "https://".$wikilang.".wikipedia.org/w/api.php?action=opensearch&search=".urlencode($s)."&format=xml&limit=1";
		$page = $this->getPage($url);
		$xml = simplexml_load_string($page);
		
		if((string)$xml->Section->Item->Description) {
			$url2="";
			$image = "";
			if((string)$xml->Section->Item->Url !="") {
				$url2 = (string)$xml->Section->Item->Url;
				$ar = explode("/",$url2);
				$last = array_pop($ar);
				$page = $this->getPage("https://".$wikilang.".wikipedia.org/api/rest_v1/page/summary/".urlencode($last));
				$data = json_decode($page);
				$data->url = "https://".$wikilang.".wikipedia.org/wiki/".urlencode($last);
				return $data;
			}
		} else {
			return false;
		}

	}
	




	/*
		Get vimeo video info using the id of the video if success return an array with many infos
		else return false.
		$obj = new Minibots();
		$poi = $obj->getVimeoInfo("75976293");
		---> Array
	(
		[id] => 75976293
		[title] => AWAKEN
		[description] => Fort Myers and Sanibel [...LONG TEXT...]
		[url] => http://vimeo.com/75976293
		[upload_date] => 2013-10-02 12:13:39
		[mobile_url] => http://vimeo.com/m/75976293
		[thumbnail_small] => http://b.vimeocdn.com/ts/450/665/450665474_100.jpg
		[thumbnail_medium] => http://b.vimeocdn.com/ts/450/665/450665474_200.jpg
		[thumbnail_large] => http://b.vimeocdn.com/ts/450/665/450665474_640.jpg
		[user_id] => 9973169
		[user_name] => Cameron Michael
		[user_url] => http://vimeo.com/user9973169
		[user_portrait_small] => http://b.vimeocdn.com/ps/377/229/3772290_30.jpg
		[user_portrait_medium] => http://b.vimeocdn.com/ps/377/229/3772290_75.jpg
		[user_portrait_large] => http://b.vimeocdn.com/ps/377/229/3772290_100.jpg
		[user_portrait_huge] => http://b.vimeocdn.com/ps/377/229/3772290_300.jpg
		[stats_number_of_likes] => 2503
		[stats_number_of_plays] => 60265
		[stats_number_of_comments] => 88
		[duration] => 313
		[width] => 1920
		[height] => 1080
		[tags] => florida, timelapse, nature, birds, dolphin, thunder, lightning, 4k, stars, skies
		[embed_privacy] => anywhere
	)
	*/
	public function getVimeoInfo($id) {
		if (!function_exists('curl_init')) die('CURL is not installed!');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://vimeo.com/api/v2/video/$id.php");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$output = unserialize(curl_exec($ch));
		curl_close($ch);
		return isset($output[0]) && is_array($output[0]) ? $output[0] : false;
	}





	/*
		function get geographic informations from an ip address.
		uses the freegeoip service.
	*/
	public function ipToGeo($ip="") {
		return $this->doGeoIp($ip);
		/* 03/11/2019 not working anymore, must register
		if(!$ip) $ip = $this->getIP();
		$ar = file_get_contents("https://ipstack.com/ipstack_api.php?ip=".$ip);
		return json_decode($ar); */
	}




	/*
		another function to get geographic information from an ip address.
		this one scrapes data from html, thanks to geoiptool.com service
		(use with moderation)
	*/
	public function doGeoIp($ip="") {
		// -----------------------------------------------------------------------------------
		if (!$ip) $ip = $this->getIP();
		$ar = array();
		//$web_page = file_get_contents( "http://www.geoiptool.com/en/?IP=".$ip );
		$web_page = $this->getPage("https://www.geoiptool.com/en/?IP=".$ip);
		preg_match_all('#<div class="data-item">(.*)</div>#Us', $web_page, $t_array);
		//print_r($t_array);
		for($j=0;$j<count($t_array[1]);$j++) {
			preg_match_all('#<span class="bold">(.*)</span>#Us', $t_array[1][$j], $m);
			if(isset($m[1][0])) {
				$label = str_replace(":","",trim(strip_tags($m[1][0])));
				preg_match_all('#<span>(.*)</span>#Us', $t_array[1][$j], $m);
				if(isset($m[1][0])) {
					$val = trim(strip_tags($m[1][0]));
					$ar[$label]=$val;
				}
			}
		}
		return $ar;
	}





	/*
		function to convert from euro to any currency, you must
		use the standard currency codes, list of codes and more informations
		here: http://www.ecb.europa.eu/stats/exchange/eurofxref/html/index.en.html
		echo $mb->getExchangeRateFromEurTo("USD"); ---> 1.3432
	*/
	public function getExchangeRateFromTo($from,$to) {
		$XMLContent=file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
		if(!function_exists("ge")) {
			function ge($currency,$XMLContent) {
				foreach($XMLContent as $line){
					if(preg_match("/currency='([[:alpha:]]+)'/",$line,$currencyCode)){
						if(preg_match("/rate='([[:graph:]]+)'/",$line,$rate)){
							if($currencyCode[1]==$currency) return $rate[1];
						}
					}
				}
				return "";
			}
		}
		$FROM = $TO = 1;
		if($from!="EUR") {
			$temp = ge($from,$XMLContent);
			if($temp=="") return "";
			$FROM = 1/$temp;
		}
		if($to!="EUR") {
			$TO = ge($to,$XMLContent);
			if($TO=="") return "";
		}
		return $FROM*$TO;
	}





	/*
		Function to search for images with a key or a phrase
		scraping contents from www.picsearch.com
		$pics = $mb->getImage("apple fruit");
		echo "<img src=\"".$pics[rand(0,count($pics)-1)]."\"/>";
	*/
	public function getImage($key) {
		//
		// scraping content from picsearch
		$temp = file_get_contents("http://www.picsearch.com/index.cgi?q=".urlencode($key));
		preg_match_all("/<img class=\"thumbnail\" src=\"([^\"]*)\"/",$temp,$ar);
		if(is_array($ar[1])) return $ar[1];
		return false;
	}
	/*
		use the previous functions results to get a bigger picture 
		of the result.
	*/
	public function getImageBig($pic) {
		// this service doesn't work always since 04/01/2019
		$ar = preg_split("/[\?\&]/",str_replace("&amp;","&",$pic));

		if(isset($ar[1])) {
			//echo "http://www.picsearch.com/imageDetail.cgi?id=".$ar[1]."&amp;start=1&amp;q=";
			$temp = file_get_contents("http://www.picsearch.com/imageDetail.cgi?id=".$ar[1]."&amp;start=1&amp;q=");
			preg_match_all("/<a( rel=\"nofollow\")? href=\"([^\"]*)\">Full-size image<\/a>/i",$temp,$ar);
			if(isset($ar[2][0])) {
				return $ar[2][0];
			}
		}
		return "";
	}


	/*
	function to send notification to a device ios or android or windows phone using
	the NotifyMyDevice app.
	*/
	public function notifyMyDevice($api,$subj,$text) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://www.notifymydevice.com/push?ApiKey=".urlencode($api)."&PushTitle=".urlencode($subj)."&PushText=".urlencode($text)."");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curl_results = curl_exec ($curl);
		curl_close ($curl);
		if(trim($curl_results)=="") return true; else return trim($curl_results);
	}





	/*
		send push notification with pushover service.
		TO DO: not tested from a long time.
	*/
	public function notifyPushover($token,$user,$message) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.pushover.net/1/messages.json"); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
			"token" => $token,
			"user" => $user,
			"message" => $message));
		$web_page = json_decode(curl_exec($ch));
		return $web_page->status; // returns 1 if ok
	}

		



	/*
		send a ping to pingomatic services to help bloggers
		to index their posts in search engines;
		TO DO: not tested from a long time.
	*/
	public function pingomatic($title,$url,$feed="") {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://pingomatic.com/ping/?title=".urlencode($title)."&blogurl=".urlencode($url)."&rssurl=".urlencode($feed)."&chk_weblogscom=on&chk_blogs=on&chk_feedburner=on&chk_newsgator=on&chk_myyahoo=on&chk_pubsubcom=on&chk_blogdigger=on&chk_weblogalot=on&chk_newsisfree=on&chk_topicexchange=on&chk_google=on&chk_tailrank=on&chk_skygrid=on&chk_collecta=on&chk_superfeedr=on");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; he; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8");   // Webbot name
		$curl_results = curl_exec ($curl);
		curl_close ($curl);
		//echo $curl_results;
		return preg_match("/(Pinging complete!)/",$curl_results);
	}




	/*
		get instagram follower for a specified user
	*/
	function getInstagramFollowers($nick) {
		$p = file_get_contents("https://www.instagram.com/".$nick);
		preg_match("/window\._sharedData ?= (.*)<\/script>/Uis",$p,$a);
		$c = trim(preg_replace("/;$/","",trim($a[1])));
		$c = trim(preg_replace("/\n/","",trim($c)));
		$b = json_decode($c);
		if(isset($b->entry_data->ProfilePage[0]->graphql->user->edge_followed_by->count)) return
			$b->entry_data->ProfilePage[0]->graphql->user->edge_followed_by->count;
		return 0;
	}



	/*
		get all the info for a specific Instagram picture
	*/
	public function getInstagramPic($url) {
		if(preg_match("/^http/",$url)) $code = explode("/",preg_replace("#/(\?(.*))?$#","",$url));
			else $code = array($url,$url);

		$p = $this->getPage("https://www.instagram.com/p/".$code[count($code)-1]."/");

		$o = json_decode($p);

		$thumb = str_replace("/s640x640/","/s150x150/",$o->graphql->shortcode_media->display_url); // not working
		$low = str_replace("/s640x640/","/s320x320/",$o->graphql->shortcode_media->display_url); // not working
		$out = array(
				"low_resolution"=>$low,
				"thumbnail"=>$thumb,
				"full"=>$o->graphql->shortcode_media->display_url,
				"standard"=>$o->graphql->shortcode_media->display_url,
				"date"=>date("Y-m-d H:i:s", $o->graphql->shortcode_media->taken_at_timestamp),
				"caption"=> $o->graphql->shortcode_media->edge_media_to_caption->edges[0]->node->text,
				"likes"=>$o->graphql->shortcode_media->edge_media_preview_like,
				"comments"=> $o->graphql->shortcode_media->edge_media_to_comment,
				"owner"=>$o->graphql->shortcode_media->owner,
				"is_video"=>$o->graphql->shortcode_media->is_video
				);
		return $out;
	}




	/*
		get last Instagram pics and user data from instagram
		without the official api
	*/
	public function getInstagramPics($user) {
		$p = $this->getPage("https://www.instagram.com/".$user."/");

		// get a big json data
		preg_match("/window\._sharedData ?= (.*)<\/script>/Uis",$p,$a);

		$c = trim(preg_replace("/;$/","",trim($a[1])));
		$c = trim(preg_replace("/\n/","",trim($c)));
		$b = json_decode($c);

		if(isset($b->entry_data->ProfilePage[0]->graphql->user) && isset(
			$b->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges
		)) {
			$a = array();
			if(isset($b->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges)) {
				foreach($b->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges as $pic) {


					$a[] = array(
						"link"=>"https://www.instagram.com/p/".$pic->node->shortcode."/", 
						"code"=>$pic->node->shortcode, 
						"likes"=>isset($pic->node->edge_liked_by->count) ? $pic->node->edge_liked_by->count : 0, 
						"preview"=>isset($pic->node->edge_media_preview_like->count) ? $pic->node->edge_media_preview_like->count : 0, 
						"comments"=>isset($pic->node->edge_media_to_comment->count) ? $pic->node->edge_media_to_comment->count : 0, 
						"created"=>date("Y-m-d H:i:s",$pic->node->taken_at_timestamp), 
						"text"=>isset($pic->node->edge_media_to_caption->edges[0]->node->text) ? $pic->node->edge_media_to_caption->edges[0]->node->text : "", 
						"low_resolution"=>$pic->node->thumbnail_src,
						"standard_resolution"=>$pic->node->thumbnail_src,
						"full_resolution"=>$pic->node->display_url,
						"thumbnail"=>$pic->node->thumbnail_src,
						"width"=>$pic->node->dimensions->width,
						"height"=>$pic->node->dimensions->height,
						);
				}
			} else {
				$a="private user";
			}

			$q=0;




			if(isset($b->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->count)) $q=$b->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->count;

			//$b->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media = null;
			return array(
				"user"=>$b->entry_data->ProfilePage[0]->graphql->user,
				"pics"=>$a,
				"totalcount"=>$q
			);
		}
		return false;
	}





	/*
		get last Instagram pics data by an hashtag
		without the official api
	*/
	public function getInstagramPicsByTag($tag) {
		$p = $this->getPage("https://www.instagram.com/explore/tags/".$tag."/");



		// get big json data
		preg_match("/window\._sharedData ?= (.*)<\/script>/Uis",$p,$a);
		$c = trim(preg_replace("/;$/","",trim($a[1])));
		$c = trim(preg_replace("/\n/","",trim($c)));
		$b = json_decode($c);
		if(isset($b->entry_data->TagPage[0]->graphql->hashtag->edge_hashtag_to_media->edges)) {
			$a = array(); // all
			$bb = array(); // top posts
			$cc = array(); // ultime



			
				$conto = $b->entry_data->TagPage[0]->graphql->hashtag->edge_hashtag_to_media->count;
				foreach($b->entry_data->TagPage[0]->graphql->hashtag->edge_hashtag_to_media->edges as $pic) {
					$thumb = $pic->node->thumbnail_resources[0]->src;
					$low = $pic->node->thumbnail_resources[2]->src;
					// tutti
					$a[] = array(
						"link"=>"https://www.instagram.com/p/".$pic->node->shortcode."/", 
						"code"=>$pic->node->shortcode, 
						"likes"=>$pic->node->edge_liked_by->count, 
						"comments"=>isset($pic->node->edge_media_to_comment->count) ? $pic->node->edge_media_to_comment->count : 0, 
						"created"=>date("Y-m-d H:i:s",$pic->date), 
						"text"=>"", 
						"low_resolution"=>$low,
						"standard_resolution"=>$pic->node->thumbnail_src,
						"full_resolution"=>$pic->node->display_src,
						"thumbnail"=>$thumb,
						"width"=>$pic->node->dimensions->width,
						"height"=>$pic->node->dimensions->height,
						);
					// top
					$bb[] = array(
						"link"=>"https://www.instagram.com/p/".$pic->node->shortcode."/", 
						"code"=>$pic->node->shortcode, 
						"likes"=>$pic->node->edge_liked_by->count, 
						"comments"=>isset($pic->node->edge_media_to_comment->count) ? $pic->node->edge_media_to_comment->count : 0, 
						"created"=>date("Y-m-d H:i:s",$pic->date), 
						"text"=>"", 
						"low_resolution"=>$low,
						"standard_resolution"=>$pic->node->thumbnail_src,
						"full_resolution"=>$pic->node->display_src,
						"thumbnail"=>$thumb,
						"width"=>$pic->node->dimensions->width,
						"height"=>$pic->node->dimensions->height,
						);
				}

				foreach($b->entry_data->TagPage[0]->graphql->hashtag->edge_hashtag_to_top_posts->edges as $pic) {
					$found = false;
					for($i=0;$i<count($a);$i++) {
						if($a[$i]["code"] == $pic->node->shortcode) {
							$found = true;
							break;
						}
					}
					if(!$found){
						$thumb = $pic->node->thumbnail_resources[0]->src;
						$low = $pic->node->thumbnail_resources[2]->src;
						$a[] = array(
								"link"=>"https://www.instagram.com/p/".$pic->node->shortcode."/", 
								"code"=>$pic->node->shortcode, 
								"likes"=>$pic->node->edge_liked_by->count, 
								"comments"=>isset($pic->node->edge_media_to_comment->count) ? $pic->node->edge_media_to_comment->count : 0, 
								"created"=>date("Y-m-d H:i:s",$pic->date), 
								"text"=>"", 
								"low_resolution"=>$low,
								"standard_resolution"=>$pic->node->thumbnail_src,
								"full_resolution"=>$pic->node->display_src,
								"thumbnail"=>$thumb,
								"width"=>$pic->node->dimensions->width,
								"height"=>$pic->node->dimensions->height,
							);

						$cc[] = array(
								"link"=>"https://www.instagram.com/p/".$pic->node->shortcode."/", 
								"code"=>$pic->node->shortcode, 
								"likes"=>$pic->node->edge_liked_by->count, 
								"comments"=>isset($pic->node->edge_media_to_comment->count) ? $pic->node->edge_media_to_comment->count : 0, 
								"created"=>date("Y-m-d H:i:s",$pic->date), 
								"text"=>"", 
								"low_resolution"=>$low,
								"standard_resolution"=>$pic->node->thumbnail_src,
								"full_resolution"=>$pic->node->display_src,
								"thumbnail"=>$thumb,
								"width"=>$pic->node->dimensions->width,
								"height"=>$pic->node->dimensions->height,


							);

					}
				}
			
			//$b->entry_data->TagPage[0]->tag->media = null;
			return array("user"=>null,"pics"=>$a,"top"=>$bb,"last"=>$cc,"count"=>$conto);
		}
		return false;
	}




	/*
		get twitter informations from a twitter nickname
		and returns also the avatar url. Contents are parsed from page
		without official api
	*/
	public function twitterInfo($nick) {

		$document = $this->getPage("https://twitter.com/$nick");

		preg_match_all('#<ul class="ProfileNav-list">(.*)</ul>#Uis', $document, $stats);

		if(isset($stats[1][0])) {
			$stats[1][0] = str_replace("\n"," ",$stats[1][0]);
			$a = $this->getTags("a", $stats[1][0],"ALL" );

			$o = array();
			for ($i=0;$i<count($a[0]);$i++) {
				if (stristr($this->attr($a[0][$i],"href"),"following")) {

					$span = $this->getTags("span",$a[0][$i], "INNER");
					$o['following'] = isset($span[2]) ? preg_replace("/[^0-9]/","",$span[2]) : 0;
				}
				if (stristr($this->attr($a[0][$i],"data-nav"),"follower") && !isset($o['followers'])) {
					$o['followers'] = preg_replace("/[^0-9]/","",$this->attr($a[0][$i],"title"));
				}
				if (strtolower($this->attr($a[0][$i],"data-nav"))=="tweets") {
					$o['tweets'] = preg_replace("/[^0-9]/","",$this->attr($a[0][$i],"title"));
				}
			}
			if(strstr($document,"ProtectedTimeline-heading")) {
				$o['private'] = 1;
			} else {
				$o['private'] = 0;
			}


			$o['avatar'] = "";
			preg_match_all('#<img [^>]*?>#Uis', $document, $t);
			for ($i=0;$i<count($t[0]);$i++) {
				if (stristr($this->attr($t[0][$i],"class"),"avatar") && $this->attr($t[0][$i],"src")) { 
					$o['avatar'] = $this->attr($t[0][$i],"src"); 
					break;
				}
			}


		} else {
			preg_match_all('#<table class=\"profile-stats\">(.*)</table>#Uis', $document, $stats2);
			if(isset($stats2[1][0])) {
				preg_match_all('#<div class="statnum">(.*)</div>#Uis', $stats2[1][0], $stats3);
				//print_r($stats3);
				if(isset($stats3[1][0])) {
					$o['tweets'] = preg_replace("/[^0-9]/","",$stats3[1][0]);
					$o['followers'] = preg_replace("/[^0-9]/","",$stats3[1][1]);
					$o['following'] = preg_replace("/[^0-9]/","",$stats3[1][2]);
				}
			}

			preg_match_all('#<td class=\"avatar\">(.*)</td>#Uis', $document, $stats4);
			if(isset($stats4[1][0])) {
				//print_r($stats4[1][0]);
				$o['avatar'] = $this->attr($stats4[1][0],"src");
				//die;
			}
			//die;
		}



		if(isset($o) && !empty($o)) {
			return $o;
		} else {
			return false;
		}
	}





	/*
		Get a Gravatar URL for a specified email address
		based on code found here: http://gravatar.com/site/implement/images/php/
		
		@param string $email The email address
		@param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
		@param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	*/
	public function getGravatar( $email, $s = 80, $d = 'mm' ) {
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		return $url;
	}

	/*
		retrieves book informations from Google Books and from IBS
	*/
	public function getBookData($isbn) {
		//
		$return_data = $this->getPage('https://www.googleapis.com/books/v1/volumes?q=isbn:'.urlencode($isbn));
		$json = json_decode($return_data, true);
		if(isset($json["items"][0]["volumeInfo"])) {

			return array(
				"title" => $json["items"][0]["volumeInfo"]["title"],
				"thumb"=>$json["items"][0]["volumeInfo"]["imageLinks"]["thumbnail"],
				"cover"=>"https://img.ibs.it/images/".$isbn."_0_0_600_80.jpg",
				"google"=> $json
			);
		} 
		return false;

	}




	/*
		this method retrieves Linkedin sharing counter for an url
	*/
	public function getLinkedinCounter($url) { 
		return "BROKEN";
		// added https 23/12/2016 or doesn't work
		$json_string = $this->getPage("https://www.linkedin.com/countserv/count/share?url=$url&format=json");
		//print_r($json_string);
		$json = json_decode($json_string, true);
		return isset($json['fCntPlusOne'])?intval($json['fCntPlusOne']):0;
	}




	/*
		retrieves Pinterest sharing counter for an URL
	*/
	public function getPinterestCounter($url) {
		// added https 23/12/2016
		$return_data = $this->getPage('https://api.pinterest.com/v1/urls/count.json?url='.urlencode($url));
		$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
		$json = json_decode($json_string, true);
		return isset($json['count'])?intval($json['count']):0;
	}




	/*
		DEAD CODE WALKING
		
		search google images for the specified keywords. The results are small in
		size
	*/
		public function getImageGoogle($k) {
			// domain .it works 26/04/2017
			$url = "https://www.google.it/search?q=##query##&tbm=isch";
			$web_page = $this->getPage( str_replace("##query##",urlencode($k), $url ));
			preg_match_all("/-?src=\"(http([^\"]*))\"/",$web_page,$a);
			return isset($a[1]) ? $a[1] : null;
		}
	


	

}




?>