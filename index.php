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
  $_SESSION['test'] = "I'm a session";
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
  $is_genuine = verify_signature(file_get_contents('php://input'),
                                 mb_convert_encoding(getenv('CLIENT_SECRET'), 'UTF-8', 'ISO-8859-1'),
                                 request()->headers('X-Nylas-Signature'));
  error_log(print_r("is_genuine: $is_genuine", true));
  error_log(print_r($_SESSION['test'], true));  
  # Is it really coming from Nylas?	
  error_log("Before checking for is_genuine");
  if(!$is_genuine){
    response()->status(401)->plain('Signature verification failed!');
  }
  error_log("Time to save the webhook");
  $index = count($webhooks) + 1;
  $webhooks[$index] = new Webhook();
  $webhooks[$index]->id = $data->data->object->id;
  $webhooks[$index]->date = "11-22-1977";
  array_push($_SESSION['webhooks'], $webhooks);
  error_log(print_r("id: $data->data->object->id", true));  
  response()->status(200)->plain('Webhook received');;
});

function verify_signature($message, $key, $signature){
  $digest = hash_hmac('sha256', $message, $key);
  return(hash_equals($digest, $signature));
}

$app->run();
