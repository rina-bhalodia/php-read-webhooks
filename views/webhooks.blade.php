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
          @foreach ($webhooks as $webhook)
       <tr class="bg-white-600 border-green-600 border-b p-4 m-4 rounded">
           <td><p class="text-sm font-semibold">{{$webhook->id}}</p></td>
           <td><p class="text-sm font-semibold">{{$webhook->date}}</p></td>
           <td><p class="text-sm font-semibold">{{$webhook->title}}</p></td>
           <td><p class="text-sm font-semibold">{{$webhook->description}}</p></td>
           <td><p class="text-sm font-semibold">{{$webhook->participants}}</p></td>
           <td><p class="text-sm font-semibold">{{$webhook->status}}</p></td>
       </tr>
          @endforeach
        @endif 
   </table>
   
    </body>
</html>
