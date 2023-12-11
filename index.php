<?php
require_once 'vendors/limonade.php';

dispatch('/', 'hello');
  function hello()
  {
      return 'Hello world from Limonade!';
  }

dispatch('/test', 'test');
        function test()
        {
            return "This should work...";
        }

dispatch('/hello_name/:name', 'hello_name');
        function hello_name()
        {
            $name = params('name');
            return "Hello $name";
        }

run();
?>
