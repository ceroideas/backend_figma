<x-app-layout>


    <div class="py-12">
        <div  class=" mx-auto sm:px-6 lg:px-9">
            <div style="padding: 25px" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="title">
                Simulations List

                </div>
                
       
        <table id="simulations-table" class="display table-style" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Project</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($simulations as $simulation)
                <tr>
                    <td>{{ $simulation['id'] }}</td>
                    <td>{{ $simulation['name'] }}</td>
                    <td>{{ $simulation['project_name'] }}</td>
                    <td>
                    <button class="btn btn-primary" onclick="window.location.href='{{ route('admin.simulation', ['id' => $simulation['id']]) }}'">See</button>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
   
            </div>
        </div>
        
    </div>

</x-app-layout>
 <link rel="stylesheet" type="text/css" href="{{ asset('css/styles.css') }}">
    <script>
        $(document).ready(function() {
            $('#simulations-table').DataTable();
        });
        var variableLaravel = @json($simulations); 
        console.log(variableLaravel);
    </script>