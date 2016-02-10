<?php
require '../../vendor/autoload.php';
use Islandora\Chullo\Chullo;
use GuzzleHttp\Client;


$chullo = Chullo::create("http://localhost:8080/fcrepo/rest");
$uri = $chullo->createResource();
$graph = $chullo->getGraph($uri);

//give it your own title
$graph->set($uri, 'dc:title', 'Mingo5');
$chullo->saveGraph($uri, $graph);
$transaction = $chullo->createTransaction();


//$client = new Client(['base_uri' => $uri]);
$client = new Client(['base_uri' => 'http://localhost:8080/fcrepo/rest']);

//change $dirpath to the path of your folder of stuff (binaries)
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

echo $body . "\n";
//echo $dirpath . $path . "\n";
$r = $client->request('POST', 'http://localhost:8080/fcrepo/rest', ['body' => $body]);
}

$chullo->commitTransaction($transaction);


 echo $uri . "\n";

?>