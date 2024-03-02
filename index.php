<?php
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
	public $subject;
	public $from_email;
	public $from_name;
}

// Main page
$app->get('/', function() use($app, $blade, $db){
  // Check our text db for a recorded session
  $webhooks =  $db->select('webhooks');
  // Display it
  echo $blade->render('webhooks', ['webhooks' => $webhooks]);
});

// This will be called to validate our webhook
$app->get('/webhook', function () use($app) {
  $challenge = request()->get('challenge');
  //Return the challenge
  echo "$challenge";
});

// Page for the Webhook to send the information to
$app->post('/webhook', function () use($app, $db) {
  // Read the webhook information
  $json = file_get_contents('php://input', true);
  // Decode the json
  $data = json_decode($json);
  $is_genuine = verify_signature(file_get_contents('php://input'),
                                 mb_convert_encoding(getenv('CLIENT_SECRET'), 'UTF-8', 'ISO-8859-1'),
                                 request()->headers('X-Nylas-Signature'));
  # Is it really coming from Nylas? 
  if(!$is_genuine){
    response()->status(401)->plain('Signature verification failed!');
  }
  error_log("Time to save the webhook");

  $webhook = new Webhook();
  // Fetch all the webhook information
  $webhook->id = $data->data->object->id;
  $date = $data->data->object->date;
  $date = new DateTime("@$date");
  $date = $date->format('Y-m-d H:i:s'); 
  $webhook->date = $date;
  $webhook->subject = $data->data->object->subject;
  $webhook->from_email = $data->data->object->from[0]->email;
  $webhook->from_name = $data->data->object->from[0]->name;
  // Store the webhook information into the session
  $db->insert("webhooks", ["webhook" => $webhook]); 
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
