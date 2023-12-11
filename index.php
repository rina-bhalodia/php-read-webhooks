<?php
require_once 'vendors/limonade.php';

dispatch('/', 'hello');
  function hello()
  {
      return 'Hello world!';
  }

dispatch('/hello_name/:name', 'hello_name');
        function hello_name()
        {
            $name = params('name');
            return "Hello $name";
        }

run();
?>
