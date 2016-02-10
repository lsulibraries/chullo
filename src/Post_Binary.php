<?php
require '../../vendor/autoload.php';
use Islandora\Chullo\Chullo;
use GuzzleHttp\Client;

//Create a parent folder called Mingo5 to conatin binaries...
$chullo = Chullo::create("http://localhost:8080/fcrepo/rest");
$uri = $chullo->createResource();
$graph = $chullo->getGraph($uri);
$graph->set($uri, 'dc:title', 'Mingo5');
$chullo->saveGraph($uri, $graph);
$transaction = $chullo->createTransaction();
$chullo->commitTransaction($transaction);


//I want to add a layer here to take the 5010101 whatever and make it's own directory within mingo.. unless we want everything there flat in Mingo5 folder


// guzzleHttp needs it's own client to reference.
$client = new Client(['base_uri' => 'http://localhost:8080/fcrepo/rest']);
//change $dirpath to the path of your folder of stuff (binaries)
$children_transaction = $chullo->createTransaction();
$dirpath = "/home/vagrant/islandora/Mingo5/";
$dir = new DirectoryIterator($dirpath);

foreach ($dir as $path){
	if($path->isDot()) continue;
	$rdf = <<<EOD
	@prefix dc:  <http://purl.org/dc/terms/>

	<> dc:title "$path"
EOD;

$child_uri = $chullo->createResource(
	$uri,
	$rdf,
	['Content-Type' => 'text/turtle'],
	$transaction,
	sha1($rdf)
	);


$body = fopen($dirpath . $path, 'r');
// echo $child_uri . "\n";
// echo $body . "\n";
$r = $client->request('POST', $uri, ['body' => $body]);
}

$chullo->commitTransaction($children_transaction);
 echo $uri . "\n";
?>