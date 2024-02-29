<?php
ini_set('session.save_path', 'data');
session_start();
  
require __DIR__ . '/vendor/autoload.php';
require("txtdb.class.php");
use Leaf\Blade;

$app = new Leaf\App();
$blade = new Blade('views', 'storage/cache');
$db = new TxtDb();

class Webhook
{
    public $id;
    public $date;
    public $title;
    public $description;
    public $participants;
    public $status;
}

$app->get('/', function() use($app, $blade, $db){
  
        $session_id =  $db->select('session');
        foreach($session_id as $session){
          $id = $session["id"];
        }
        
        if(!isset($_SESSION['webhooks']) && count($session_id) > 0){
          session_destroy();
          session_id($id);
          session_start();			
		}
  
    if(isset($_SESSION['webhooks'])){
        $webhooks = $_SESSION['webhooks'];
        echo $blade->render('webhooks', ['webhooks' => $webhooks]);
    }else{
        echo "<h1>Webhooks</h1>";
    }
});

// This will be called to validate our webhook
$app->get('/webhook', function () use($app) {
  $challenge = request()->get('challenge');
  echo "$challenge";
});

// Page for the Webhook to send the information to
$app->post('/webhook', function () use($app, $db) {
    $id = session()->id();
    $session_id =  $db->select('session');

    foreach($session_id as $session){
      $id = $session["id"];
    }
    if(isset($session_id) && count($session_id) === 0){
          $db->insert("session", ["id" => $id]);
	   }
  
  $json = file_get_contents('php://input', true);
  $data = json_decode($json);
  //$is_genuine = verify_signature(file_get_contents('php://input'),
  //                               mb_convert_encoding(getenv('CLIENT_SECRET'), 'UTF-8', 'ISO-8859-1'),
  //                               request()->headers('X-Nylas-Signature'));
  # Is it really coming from Nylas? 
  //if(!$is_genuine){
  //  response()->status(401)->plain('Signature verification failed!');
  //}
  error_log("Time to save the webhook");

  if(isset($_SESSION['webhooks'])){
    $webhooks = $_SESSION['webhooks'];
  }else{
      $webhooks = array();
  }
  $index = count($webhooks) + 1;
  $webhooks[$index] = new Webhook();
  $webhooks[$index]->id = $data->data->object->id;
  $event_datetime = "";
  switch($data->data->object->when->object){
      case "timespan":
        $s_t = $data->data->object->when->start_time;
        $st = new DateTime("@$s_t");
        $st = $st->format('Y-m-d H:i:s'); 
        $e_t = $data->data->object->when->end_time;
        $et = new DateTime("@$e_t");
        $et = $et->format('Y-m-d H:i:s');
        $event_datetime = "From " . $st . " to " . $et;
      break;
  }
  $participants = $data->data->object->participants;
  $participants_list = "";
  foreach($participants as $participant){
      $participants_list = $participants_list . " " . $participant . ","; 
  }
  $participants_list = rtrim($participants_list, ",");
  
  $webhooks[$index]->date = $event_datetime;
  $webhooks[$index]->title = $data->data->object->title;
  $webhooks[$index]->description = $data->data->object->description;
  $webhooks[$index]->participants = $participants_list;
  $webhooks[$index]->status = $data->data->object->status;
  $_SESSION['webhooks'] = $webhooks;
  error_log("Webhook was saved");
  response()->status(200)->plain('Webhook received');
  exit();
});

function verify_signature($message, $key, $signature){
  $digest = hash_hmac('sha256', $message, $key);
  return(hash_equals($digest, $signature));
}

$app->run();
?>
