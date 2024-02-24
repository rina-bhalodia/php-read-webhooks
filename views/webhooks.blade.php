<!Doctype html>
<html>
    <head>
        <title>Webhooks</title>
    </head>
    <body>
	<h1>Webhooks</h1>
	    @if (!is_null($webhooks))
          @foreach ($webhooks as $webhook)
              <p>This is user {{ $webhook->id }}</p>
          @endforeach
        @endif 
    </body>
</html>
