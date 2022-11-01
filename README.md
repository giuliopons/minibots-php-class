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

## List of method and status

[x] doSpelling - spelling suggest with Google
[ ] doShortURL - make a short url with tinyurl.com
[v] doShortURLDecode - expand a short url
