<?php

require __DIR__ . '/vendor/autoload.php';

use upsell\wp\vimeo\Api as VimeoApi;

$vimeoAPI = new VimeoApi('a3079ddd1500d5b2f5a331e9b91fe9b0');
echo '<h2>VimeoApi::getInfo()</h2>';
var_dump($vimeoAPI->getInfo());
echo '<hr>';

echo '<h2>VimeoApi::getVideos()</h2>';
var_dump($vimeoAPI->getVideos());

//echo '<h2>VimeoApi::uploadVideo()</h2>';
//var_dump($vimeoAPI->uploadVideo('http://test.moznainaczej.net/wp-content/uploads/2019/shortvideo.mp4'));

//echo '<h2>VimeoApi::getVideo()</h2>';
//var_dump($vimeoAPI->getVideo('1'));

//echo '<h2>VimeoApi::deleteVideo()</h2>';
//var_dump($vimeoAPI->deleteVideo('1'));
