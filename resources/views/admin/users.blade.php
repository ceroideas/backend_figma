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
                    <th>Last time seen</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user['id'] }}</td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ $user['last_login_at'] }}</td>
                    <td>
                    <div class="table-actions">
                    <i class="pointer fa-regular fa-eye"  onclick="window.location.href='{{ route('admin.user', ['id' => $user['id']]) }}'"></i>
                    <i class="pointer fa-regular fa-pen-to-square" onclick="window.location.href='{{ route('admin.update-user', ['id' => $user['id']]) }}'"></i>
                    <i class="pointer fa-regular fa-trash-can" onclick="confirmDelete({{ $user['id'] }})"></i>
                    </div>
                   
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
        var url = @json($angularAppUrl);
        console.log(variableLaravel, url);
        function confirmDelete(userId) 
        { if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) 
            { window.location.href = '/admin/delete-user/' + userId; 

            } }
    </script>