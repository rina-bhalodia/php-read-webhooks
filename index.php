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
  global $webhooks;  
  error_log("POST WEBHOOK");
  $json = file_get_contents('php://input', true);
  $data = json_decode($json, true);
  error_log(print_r($json, true));
  error_log(print_r(request()->headers('X-Nylas-Signature'), true));  
  $is_genuine = verify_signature(file_get_contents('php://input'),
                                 utf8_encode(getenv('CLIENT_SECRET')),
                                 request()->headers('X-Nylas-Signature'));
  error_log(print_r($is_genuine, true)); 
  # Is it really coming from Nylas?	
  if(!$is_genuine){
    response()->status(401)->plain('Signature verification failed!');
  }
  $index = count($webhooks) + 1;
  $webhooks[$index] = new Webhook();
  $webhooks[$index]->id = $data->data->object->id;
  array_push($_SESSION['webhooks'], $webhooks);
  response()->status(200)->plain('Webhook received');;
});

function verify_signature($message, $key, $signature){
  $digest = hash_hmac('sha256', $message, $key);
  return(hash_equals($digest, $signature));
}

$app->run();
