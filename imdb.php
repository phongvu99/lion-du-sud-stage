<?php

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

$client = new Client(HttpClient::create(['timeout' => 60]));

// Go to the IMDB website
$crawler = $client->request('GET', 'https://www.imdb.com/title/tt0338013/reviews?ref_=tt_urv');

// Get all the reviews from the Users
$crawler->filter('a')->each(function ($node) {
    print $node->text() . "\n";
});
