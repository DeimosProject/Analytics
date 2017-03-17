<?php fastcgi_finish_request();

include_once dirname(__DIR__) . '/vendor/autoload.php';

$analytics = new \Deimos\Analytics\Analytics('UA-93930807-1', 'analytics.deimos');

$analytics->setResource('image.png')
    ->setAction('download')
    ->setLabel('My Image')
    ->track();
