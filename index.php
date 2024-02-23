<?php

require __DIR__ . '/vendor/autoload.php';
use Leaf\Blade;

$app = new Leaf\App();
$blade = new Blade('views', 'storage/cache');
session_start();

class Webhook
{
    public $id;
    public $date;
    public $title;
    public $description;
    public $participants;
    public $status;
}

$webhooks = array();

$app->get('/init', function() use($app){
  $_SESSION['webhooks'] = array();
});

$app->get('/', function() use($app, $blade){
  echo $blade->render('webhooks', ['webhooks' => $_SESSION['webhooks']]);
});

$app->get('/clear', function() use ($app){
  session_unset();
});

// This will be called to validate our webhook
$app->get('/webhook', function () use($app) {
  $challenge = request()->get('challenge');
  echo "$challenge";
});

// Page for the Webhook to send the information to
$app->post('/webhook', function () use($app) {
  error_log("POST WEBHOOK");
  error_log("Printing variables");
  $json = file_get_contents('php://input', true);
  //$body = request()->body();
  $data = json_decode($json, true);
  error_log(print_r("JSON: $json", true));
  //$is_genuine = verify_signature(file_get_contents('php://input'),
  //                               utf8_encode(getenv('CLIENT_SECRET')),
  //	                         request()->headers('X-Nylas-Signature'));
  //error_log(print_r("Is genuine: $is_genuine",true));	
  # Is it really coming from Nylas?	
  //if(!$is_genuine){
  //  response()->status(401)->plain('Signature verification failed!');
  //}

  response()->status(200)->plain('Webhook received');;
});

function verify_signature($message, $key, $signature){
  $digest = hash_hmac('sha256', $message, $key);
  return(hash_equals($digest, $signature));
}

$app->run();
