<?php 
require 'vendor/autoload.php';
require 'libs/NotORM.php'; 
//membuat dan mengkonfigurasi slim app
$app = new \Slim\app;

// konfigurasi database
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'neomaa';
$dbmethod = 'mysql:dbname=';

$dsn = $dbmethod.$dbname;
$pdo = new PDO($dsn, $dbuser, $dbpass);
$db  = new NotORM($pdo);

$app-> get('/', function(){
    echo "Hello World this is SIMUTU-API";
});

$app ->get('/caridosen', function($request, $response, $args) use($app, $db){
	
    try {
    	$q = $request->getParam('q');
    	$dosen = array('items' => null);
		foreach($db->master_dosen()->where('namaDosen like ?', $q.'%')->limit(10) as $data){
	        $dosen['items'][] = array(
	            'nidn' => $data['nidn'],
	            'nip' => $data['nip'],
	            'nama' => strtoupper($data['namaDosen']).' '.$data['gelarLengkap'],
	            'id' => $data['no_dosen'],
	            );
	    }
	    return $response->withJson($dosen, 201, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
    	$app->response()->status(400);
	    $app->response()->header('X-Status-Reason', $e->getMessage());
    }
    //$app->render(201, $produk);
    //echo json_encode($produk, JSON_PRETTY_PRINT);
});
$app ->get('/dosen/{nidn}', function($request, $response, $args) use($app, $db){
	
    try {
    	//$dosen = array('items' => null);
		$data = $db->master_dosen()->where('nidn', $args['nidn'])->fetch();
        $dosen['data'] = array(
            'nidn' => $data['nidn'],
            'nip' => $data['nip'],
            'nama' => strtoupper($data['namaDosen']).(empty($data['gelarLengkap']) ? '' : ' '.$data['gelarLengkap']),
            'id' => $data['no_dosen'],
            );
	    return $response->withJson($dosen, 201, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
    	$app->response()->status(400);
	    $app->response()->header('X-Status-Reason', $e->getMessage());
    }
    //$app->render(201, $produk);
    //echo json_encode($produk, JSON_PRETTY_PRINT);
});

//run App
$app->run();