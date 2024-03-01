<?php
ini_set('session.save_path', 'data');
session_start();
  
// Load dependencies
require __DIR__ . '/vendor/autoload.php';
require("txtdb.class.php");
use Leaf\Blade;

// Declare global objects
$app = new Leaf\App();
$blade = new Blade('views', 'storage/cache');
$db = new TxtDb();

// Class to hold Webhooks information
class Webhook
{
    public $id;
    public $date;
    public $title;
    public $description;
    public $participants;
    public $status;
}

// Main page
$app->get('/', function() use($app, $blade, $db){
  // Check our text db for a recorded session
  $session_id =  $db->select('session');
  foreach($session_id as $session){
    // Found one
    $id = $session["id"];
  }

  // Do we have a webhook recorded? Do we have a session stored?
  if(!isset($_SESSION['webhooks']) && count($session_id) > 0){
    session_destroy();
    session_id($id);
    session_start();			
  }
  
  // We do have at least one webhook recorded
  if(isset($_SESSION['webhooks'])){
    $webhooks = $_SESSION['webhooks'];
    // Display it
    echo $blade->render('webhooks', ['webhooks' => $webhooks]);
  }else{
    // Nothing to display yet
    echo "<h1>Webhooks</h1>";
  }
});

// This will be called to validate our webhook
$app->get('/webhook', function () use($app) {
  $challenge = request()->get('challenge');
  //Return the challenge
  echo "$challenge";
});

// Page for the Webhook to send the information to
$app->post('/webhook', function () use($app, $db) {
  error_log("Posting the webhook");
  // Grab the current session id
  $id = session_id();
  // Do we have a session recorded?
  $session_id =  $db->select('session');
    
  foreach($session_id as $session){ 
  // Do have a session
    $id = $session["id"];
  }
    
  // We have a session but it's not recorded yet	
  if(isset($session_id) && count($session_id) === 0){
    $db->insert("session", ["id" => $id]); 
  }
  
  // Read the webhook information
  $json = file_get_contents('php://input', true);
  // Decode the json
  $data = json_decode($json);
  //$is_genuine = verify_signature(file_get_contents('php://input'),
  //                               mb_convert_encoding(getenv('CLIENT_SECRET'), 'UTF-8', 'ISO-8859-1'),
  //                               request()->headers('X-Nylas-Signature'));
  # Is it really coming from Nylas? 
  //error_log("Coming from Nylas " . $is_genuine);
  error_log("Signature " . request()->headers('X-Nylas-Signature')); 
  //if(!$is_genuine){
  //  response()->status(401)->plain('Signature verification failed!');
  //  exit();
  //}
  error_log("Time to save the webhook");

  // Do we have session information stored?
  if(isset($_SESSION['webhooks'])){
    $webhooks = $_SESSION['webhooks'];
  }else{
    // No, we don't. But we need a variable
    $webhooks = array();
  }
  // Increase the webhook counter
  $index = count($webhooks) + 1;
  // Create a new webhook object
  $webhooks[$index] = new Webhook();
  // Fetch all the webhook information
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
  
  // Assign all the webhook information
  $webhooks[$index]->date = $event_datetime;
  $webhooks[$index]->title = $data->data->object->title;
  $webhooks[$index]->description = $data->data->object->description;
  $webhooks[$index]->participants = $participants_list;
  $webhooks[$index]->status = $data->data->object->status;
  // Store the webhook information into the session
  $_SESSION['webhooks'] = $webhooks;
  error_log("Webhook was saved");
  // Return success back to Nylas
  response()->status(200)->plain('Webhook received');
  exit();
});

// Function to verify the signature
function verify_signature($message, $key, $signature){
  $digest = hash_hmac('sha256', $message, $key);
  return(hash_equals($digest, $signature));
}

// Run the app
$app->run();
?>
