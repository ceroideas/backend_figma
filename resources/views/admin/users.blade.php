<x-app-layout>


    <div class="py-12">
        <div  class=" mx-auto sm:px-6 lg:px-9">
            <div style="padding: 25px" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="title">
                User List

                </div>
                
       
        <table id="users-table" class="display table-style" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user['id'] }}</td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>
                    <button class="btn btn-primary" onclick="window.location.href='{{ route('admin.user', ['id' => $user['id']]) }}'">See</button>

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
            $('#users-table').DataTable();
        });
        var variableLaravel = @json($users); 
        console.log(variableLaravel);
    </script>