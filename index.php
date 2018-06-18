<?php
ini_set('display_error', 'on');
error_reporting(E_ALL);
require_once './vendor/autoload.php';
$ffmpeg = FFMpeg\FFMpeg::create(array(
    'ffmpeg.binaries'  => '/usr/local/Cellar/ffmpeg/3.4.2/bin/ffmpeg',
    'ffprobe.binaries' => '/usr/local/Cellar/ffmpeg/3.4.2/bin/ffprobe',
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
), $logger);

//$audio = $ffmpeg->open('./voice/2018/05/02/1525226075.mp3');
var_dump($ffmpeg);