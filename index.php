<?php
require_once 'vendors/limonade.php';

dispatch('/webhook/:challenge', 'webhook');
  function webhook()
  {
      return params('challenge');
  }

run();
?>
