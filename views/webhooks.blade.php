<!Doctype html>
<html>
    <head>
        <script src="https://cdn.tailwindcss.com"></script>     
        <title>Webhooks</title>
    </head>
    <body>
   <h1 class="text-4xl font-bold dark:text-black bg-green-600 border-green-600 border-b p-4 m-4 rounded grid place-items-center">Webhooks</h1>
   <table style="width:100%">
       <tr class="bg-green-600 border-green-600 border-b p-4 m-4 rounded">
           <th>Id</th>
           <th>Date</th>
           <th>Subject</th>
           <th>From Email</th>
           <th>From Name</th>
       </tr>
        @if (!is_null($webhooks))
          @foreach ($webhooks as $webhook["webhook"])
            @foreach ($webhook["webhook"] as $webhook_elem)
                <tr class="bg-white-600 border-green-600 border-b p-4 m-4 rounded">
                  <td><p class="text-sm font-semibold">{{$webhook_elem["id"]}}</p></td>
                  <td><p class="text-sm font-semibold">{{$webhook_elem["date"]}}</p></td>
                  <td><p class="text-sm font-semibold">{{$webhook_elem["subject"]}}</p></td>
                  <td><p class="text-sm font-semibold">{{$webhook_elem["from_email"]}}</p></td>
                  <td><p class="text-sm font-semibold">{{$webhook_elem["from_name"]}}</p></td>
                </tr>
            @endforeach
          @endforeach
        @endif 
   </table>
   
    </body>
</html>
