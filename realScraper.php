<?php

require "vendor/autoload.php";

use Scraper\PHPScraper;

$phpScraper = new PHPScraper();
$phpScraper->init();

$crawler = $phpScraper->getCrawler();

$nodeList = $phpScraper->crawl($crawler, array(), 0);
$iter = 0;

foreach ($nodeList as $nodeKey => $nodeValue) {
    echo "Current iteration $iter \n";
    $phpScraper->setUri($nodeKey);
    $crawler = $phpScraper->getCrawler();
    $nodeList += $phpScraper->crawl($crawler, array(), 0);
    $iter++;
//    echo "Current nodeKey $nodeKey \n";
//    echo "----------------------------------- \n";
}

$keys = array_keys($nodeList);

echo "First 10 links \n";
for ($i = 0; $i < count($nodeList); $i++) {
    if ($i === 10) {
        break;
    }
    echo $keys[$i] . "\n";
}

// Sort the array by value and maintain index association
asort($nodeList);

// Total node
$n = count($nodeList);

$root = null;

$root = $phpScraper->bstScraper($keys, $nodeList, $root, 0, $n);

echo "Enter an URL included in the above 10 links \n";
$uri = trim(fgets(STDIN));
echo "I got it:\n" . $uri . "\n";

echo "URL Title: " . $phpScraper->bstNodeFind($root, $uri)->getValue() . "\n";
