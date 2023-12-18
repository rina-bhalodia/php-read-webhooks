<?php

require __DIR__ . '/vendor/autoload.php';

$app = new Leaf\App();

$webhook = "";

$app->get('/', function() use($app){
	echo $GLOBALS["webhook"];
});

# This will be called to validate our webhook
$app->get('/webhook', function () use($app) {
	$challenge = request()->get('challenge');
	echo "$challenge";
});

# Page for the Webhook to send the information to
$app->post('/webhook', function () use($app) {
	echo "On Post!";
	echo "file_get_contents('php://input')";
    $is_genuine = verify_signature(file_get_contents('php://input'),
    utf8_encode(getenv('CLIENT_SECRET')),
	request()->headers('X-Nylas-Signature'));
	
	# Is it really coming from Nylas?	
    if(!$is_genuine){
		response()->plain('Signature verification failed!', 401);
    }
    
	$json = file_get_contents('php://input');
	$data = json_decode($json, true);    
    $GLOBALS["webhook"] = $data;
});

function verify_signature($message, $key, $signature){
	$digest = hash_hmac('sha256', $message, $key);
	return(hash_equals($digest, $signature));
}

$app->run();
