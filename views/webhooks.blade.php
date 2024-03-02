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
           <th>Title</th>
           <th>Description</th>
           <th>Participants</th>
           <th>Status</th>
       </tr>
        @if (!is_null($webhooks))
          @foreach ($webhooks as $webhook["webhook"])
            @foreach ($webhook["webhook"] as $webhook_elem)
              @foreach ($webhook_elem as $webhook_line)
                <tr class="bg-white-600 border-green-600 border-b p-4 m-4 rounded">
                  <td><p class="text-sm font-semibold">{{$webhook_line["id"]}}</p></td>
                  <td><p class="text-sm font-semibold">{{$webhook_line["date"]}}</p></td>
                  <td><p class="text-sm font-semibold">{{$webhook_line["title"]}}</p></td>
                  <td><p class="text-sm font-semibold">{{$webhook_line["description"]}}</p></td>
                  <td><p class="text-sm font-semibold">{{$webhook_line["participants"]}}</p></td>
                  <td><p class="text-sm font-semibold">{{$webhook_line["status"]}}</p></td>
                </tr>
               @endforeach
            @endforeach
          @endforeach
        @endif 
   </table>
   
    </body>
</html>
