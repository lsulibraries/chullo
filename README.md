#Chullo 

Chullo is a PHP client for Fedora 4 built using Guzzle and EasyRdf.

[![Latest Stable Version](https://img.shields.io/packagist/v/Islandora/chullo.svg?style=flat-square)](https://packagist.org/packages/islandora/chullo)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.5-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status](https://travis-ci.org/Islandora-CLAW/chullo.svg?branch=master)](https://travis-ci.org/Islandora-CLAW/chullo)

## Requirements

* PHP 5.5+
* [Composer](https://getcomposer.org/)

## Installation

1. `git clone https://github.com/Islandora-CLAW/chullo`
2. `cd chullo`

3. Edit /chullo/composer.json:

```
{
    "repositories": [
        {
            "type": "vcs",
            "url": "/path/to/chullo"  
        }
    ],
    "require": {
        "islandora/chullo": "dev-master"
    }
}
```

4. in bash /chullo/, do `composer install`

##Usage

###Fedora
```php
<?php
require 'vendor/autoload.php';

use Islandora\Chullo\Chullo;

// Instantiated with static factory
$chullo = Chullo::create('http://localhost:8080/fcrepo/rest');

// Create a new resource
$uri = $chullo->createResource(); // http://localhost:8080/fcrepo/rest/0b/0b/6c/68/0b0b6c68-30d8-410c-8a0e-154d0fd4ca20

// Parse resource as an EasyRdf Graph
$graph = $chullo->getGraph($uri);

// Set the resource’s title
$graph->set($uri, 'dc:title', 'My Sweet Title');

// Save the graph to Fedora
$chullo->saveGraph($uri, $graph);

// Open a transaction
$transaction = $chullo->createTransaction(); //tx:2b27e944-483d-4e59-a33b-f378bd42faf5

// Do a bulk upload
for ($i = 0; $i < 20; $i++) {
    $rdf = <<<EOD
        @prefix dc: <http://purl.org/dc/terms/>

        <> dc:title "Child Resource $i"
EOD;

    // Using all possible arguments in createResource() this time
    $child_uri = $chullo->createResource(
        $uri,
        $rdf,
        ['Content-Type' => 'text/turtle'],
        $transaction,
        sha1($rdf)
    );
}

// Commit it
$chullo->commitTransaction($transaction);

// Check it out:
echo $uri . "\n";
?>
```

### Triplestore

```php
<?php
use Islandora\Chullo\TriplestoreClient;

$triplestore = TriplestoreClient::create('http://127.0.0.1:8080/bigdata/namespace/kb/sparql/');

$sparql = <<<EOD
    PREFIX fedora: <http://fedora.info/definitions/v4/repository#>

    SELECT ?s
    WHERE {
        ?s fedora:hasParent <http://localhost:8080/fcrepo/rest/> .
    }
    LIMIT 25
EOD;

$results = $triplestore->query($sparql);

foreach ($results as $triple) {
    echo $triple->s . "\n";
}
?>
```

## Maintainers/Sponsors

Current maintainers:

* [Daniel Lamb](https://github.com/daniel-dgi)
* [Nick Ruest](https://github.com/ruebot)

## Development

If you would like to contribute to this module, please check out [CONTRIBUTING.md](CONTRIBUTING.md).
