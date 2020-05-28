<?php

namespace Scraper;

use Goutte\Client;
use LogicException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class PHPScraper
{
    private $uri;


    /**
     * Initialize a PHPScraper given the URI
     */
    public function init()
    {
        echo "Where do you want to scrape today? \n";
        $this->uri = trim(fgets(STDIN));
        echo "I got it:\n" . $this->uri . "\n";
        $parsedURL = parse_url($this->uri);
        $rootURL = $parsedURL['scheme'] . "://" . $parsedURL['host'] . "/";
        echo "Root URL: $rootURL \n\n";
    }


    /**
     * @return Crawler
     * Returns a crawler given an URI.
     * The crawler can be manipulated to fetch desired content from the DOM
     */
    public function getCrawler(): Crawler
    {
        $client = new Client(HttpClient::create(['timeout' => 60]));
        return $client->request('GET', $this->uri);
    }

    /**
     * @param Crawler $nodeList
     * @param $n
     * @return string
     */
    private function getNodeKey(Crawler $nodeList, $n)
    {
        try {
            $nodeKey = $nodeList->eq($n)->link()->getUri();
        } catch (LogicException $e) {
            // Do nothing;
        }
        $nodeKey = !empty($nodeKey) ? $nodeKey : "";
        return $nodeKey;
    }

    /**
     * @param Crawler $crawler
     * @param array $nodeArray
     * @param int $n
     * @return array
     */
    public function crawl(Crawler $crawler, array $nodeArray, int $n): array
    {
        // All filter methods return a new Crawler instance with filtered content.
        // Return all elements with 'href' attribute found in the DOM.
        $nodeList = $crawler->filter('a');
        $totalNode = count($nodeList);

        /*echo "Current iteration: $n \n";
        echo "Total node: $totalNode \n";*/

        // Recursion criteria || base cases
        if ($totalNode === 0 || $n === 10 || $n === $totalNode) {
            return $nodeArray;
        }

        $nodeKey = $this->getNodeKey($nodeList, $n);
        if ($nodeKey !== $this->uri && $nodeKey !== "") {
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
        return $this->crawl($crawler, $nodeArray, ++$n);
    }

    /**
     * @param string $key
     * @param string $value
     * @return Node
     */
    private function newNode(string $key, string $value): Node
    {
        return new Node($key, $value, null, null);
    }

    /**
     * @param array $keys
     * @param array $nodeList
     * @param Node|null $root
     * @param int $i
     * @param int $n
     * @return Node|null
     * Construct a binary search tree from given scraped href links array.
     */
    public function bstScraper(array $keys, array $nodeList, ?Node $root, int $i, int $n): ?Node
    {
        if ($i < $n) {
            $root = $this->newNode($keys[$i], $nodeList[$keys[$i]]);

            // Insert left node
            $root->setLeft($this->bstScraper($keys, $nodeList, $root->getLeft(), 2 * $i + 1, $n));

            // Insert right node
            $root->setRight($this->bstScraper($keys, $nodeList, $root->getRight(), 2 * $i + 2, $n));
        }
        return $root;
    }

    /**
     * @param Node|null $haystack
     * @param string $needle
     * @return Node|null
     * Find the given 'needle' in a 'haystack' and return the URL title
     * of the found node.
     */
    public function bstNodeFind(?Node $haystack, string $needle): ?Node
    {
        if ($haystack !== null) {
            if ($needle === $haystack->getKey()) {
                return $haystack;
            } else {
                $foundNode = $this->bstNodeFind($haystack->getLeft(), $needle);
                if ($foundNode === null) {
                    $foundNode = $this->bstNodeFind($haystack->getRight(), $needle);
                }
                return $foundNode;
            }
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri): void
    {
        $this->uri = $uri;
    }
}
