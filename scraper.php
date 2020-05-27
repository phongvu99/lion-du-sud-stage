<?php

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Scraper\Node;

function getCrawler(string $uri): Crawler
{
    $client = new Client(HttpClient::create(['timeout' => 60]));

    // Return a crawler at the request URL
    return $client->request('GET', $uri);
}

function getNodeKey(Crawler $nodeList, $n)
{
    try {
        $nodeKey = $nodeList->eq($n)->link()->getUri();
    } catch (LogicException $e) {
        // Do nothing;
    }
    $nodeKey = !empty($nodeKey) ? $nodeKey : "";
    return $nodeKey;
}

// Crawling in my skin
function crawl(string $uri, Crawler $crawler, array $nodeArray, int $n): array
{
    // All filter methods return a new Crawler instance with filtered content.
    // Return all elements with 'href' attribute found in the DOM.
    $nodeList = $crawler->filter('a');
    $totalNode = count($nodeList);
//    echo "Current iteration: $n \n";
//    echo "Total node: $totalNode \n";
    // Recursion criteria
    if ($totalNode === 0 || $n === 10 || $n === $totalNode) {
        return $nodeArray;
    }

    $nodeKey = getNodeKey($nodeList, $n);
    if ($nodeKey !== $uri && $nodeKey !== "") {
        $nodeValue = $nodeList->eq($n)->text();
        if (empty($nodeValue)) {
            $nodeValue = $nodeKey;
            $nodeValue = parse_url($nodeValue);
            if (isset($nodeValue['path'])) {
                $nodeValue = $nodeValue['host'] . $nodeValue['path'];
            } else {
                $nodeValue = $nodeValue['host'];
            }
        }
        $nodeArray += [$nodeKey => $nodeValue];
    }
    return crawl($uri, $crawler, $nodeArray, ++$n);
}

function newNode(string $key, string $value): Node
{
    return new Node($key, $value, null, null);
}

function bstScraper(array $keys, array $nodeList, ?Node $root, int $i, int $n): ?Node
{
    if ($i < $n) {
        $root = newNode($keys[$i], $nodeList[$keys[$i]]);

        // Insert left node
        $root->setLeft(bstScraper($keys, $nodeList, $root->getLeft(), 2 * $i + 1, $n));

        // Insert right node
        $root->setRight(bstScraper($keys, $nodeList, $root->getRight(), 2 * $i + 2, $n));
    }
    return $root;
}

function bstInOrder(?Node $root, string $needle): ?Node
{
    if ($root !== null) {
        if ($needle === $root->getKey()) {
            return $root;
        } else {
            $foundNode = bstInOrder($root->getLeft(), $needle);
            if ($foundNode === null) {
                $foundNode = bstInOrder($root->getRight(), $needle);
            }
            return $foundNode;
        }
    } else {
        return null;
    }
}
