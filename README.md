# Minibots PHP class

This PHP class is a growing collection (I've started in 2010) of small spiders (bots) that go out on the web and make some small useful jobs. It also contains a bunch of useful functions that can help to grow your PHP projects.

For a few years it was sold on Codecanyon, than it became difficult for me to mantain, so I've turned it offline. Now I've decided to make it open source and I've put it on Github.

Some methods aren't working, often due to services no longer available or aquired and deeply changed.

I hope that turning this software "open" will help to make it work again. 💪🏼💪🏼💪🏼

You can see it in action in this four test pages:

[TEST 1](https://www.barattalo.it/demo/minibots/test.php) - [TEST 2](https://www.barattalo.it/demo/minibots/test2.php) - [TEST 3](https://www.barattalo.it/demo/minibots/test3.php) - [TEST 4](https://www.barattalo.it/demo/minibots/test4.php)


## Disclaimer
Since this software has some functions that scrape data from third parties sites, please remember to use it with moderation and at your own risk. I've created it primarily for small project purposes, and it could help you to build spiders with PHP and easily bring new functionalities to your works.
Explore it and be fair, when you create a bot you could harm a server by performing a lot of calls very fast, so pay attention on how you use this software.


## Example of usage

```
/*
  Google spell suggest.
*/
$obj = New Minibots();
$word = $obj->doSpelling("wikipezia"); 
echo $word;
```
output wiil be

```wikipedia```

## List of bot methods and their status
The bot methods marked with a tick are working.
If you want, you can participate in improving this library by fixing methods.

- [x] **doSpelling** : spelling suggest with Google
- [x] **doShortURL** : make a short url with tinyurl.com
- [x] **doShortURLDecode** : expand a short url
- [x] **checkMp3** : check if a url points to an existing mp3 file 
- [x] **url_exists** : check if an url exists
- [x] **doSMTPValidation** : perform smtp validation (not reliable)
- [x] **getUrlInfo** : get information on a URL
- [x] **getVideoUrlInfo** : get information on a video URL, both yotube or vimeo
- [x] **getVimeoInfo** : more information on Vimeo videos
- [ ] **readFacebookCounters** : get Facebook counters for a url using Facebook APIs (_STATUS UNKNOWN_)
- [ ] **readFacebookPageCounters** : read Facebook Page counters using the informations in the meta
- [ ] **readTwitterCounter** : get number of tweets with the specified url counters for a url
- [x] **googleSuggestKeywords** : get the keyword suggestion from Google for a word and return an array with suggested possible keywords
- [ ] **getLatLong** : get latitute and longitude of a typed address, need an API key (_STATUS UNKNOWN_)
- [ ] **getLatLongBis** : another get latitute and longitude of a typed address, need an API key (_STATUS UNKNOWN_)
- [x] **doGeoIp** : get information on user location from its IP address
- [x] **wikiDefinition** : get definition of a term from Wikipedia
- [x] **getExchangeRateFromTo** : get exchange rate from a currency to another one
- [ ] **getImage** : return an image from a string 
- [ ] **getImageBig** : return a large image from a string
- [ ] **notifyMyDevice** : send notification to mobile phone using an app (_STATUS UNKNOWN_)
- [ ] **notifyPushover** : send notification to mobile phone using an app (_STATUS UNKNOWN_)
- [ ] **pingomatic** : send a ping to pingomatic services to help bloggers (_STATUS UNKNOWN_)
- [ ] **getInstagramFollowers** : get Instagram followers count for a user
- [ ] **getInstagramPic** : get Instagram pic and informations from URL
- [ ] **getInstagramPics** : get Instagram pictures from a username
- [ ] **getInstagramPicsByTag** : get Instagram pictures searching with a tag
- [ ] **twitterInfo** : get Twitter information from username
- [x] **getGravatar** : get picture of user from email with Gravatar service
- [ ] **getBookData** : get book data from ISBN (_STATUS UNKNOWN_)
- [ ] **getLinkedinCounter** : get Linkedin shares counter from URL
- [x] **getPinterestCounter** : get the number of shares of a URL on Pinterest
- [ ] **getImageGoogle** : return an image from a string with Google
- [x] **copyFile** : copy a remote file to your server

## Helper methods

- **getIP** : get ip address of user
- **dayadd** : add days to a date
- **attr** : return the html attribute of a given tag, used for scraping HTML
- **betweenTags** : return a part of a string between two tags, used for scraping HTML
- **getTags** : return the array of matches when searching for a tag serie while scraping HTML
- **makeabsolute** : maker an url absolute when scraping HTML
- **getPagePost** : use cURL to get a page with POST variables
- **getPage** : get a page with cURL or file_get_contents
- **findLinks** : extract all the links from a page
- **findEmails** : extract all the email addresses from a page
- **justText** : return just text from a web page

## Private methods

- **getHttpResponseCode**
- **getRemoteFileSize**
- **on_curl_header**
- **on_curl_write**
