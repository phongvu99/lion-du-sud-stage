<?php

require "./vendor/autoload.php";
require "Node.php";

echo "Where do you want to scrape today? \n";
$uri = trim(fgets(STDIN));
echo "I got it:\n" . $uri . "\n";
$parsedURL = parse_url($uri);
$rootURL = $parsedURL['scheme'] . "://" . $parsedURL['host'] . "/";
$title = $parsedURL['host'] . $parsedURL['path'];
echo "Root URL: $rootURL \n\n";
