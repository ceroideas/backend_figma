<x-app-layout>

    @if (session('success'))
    <div style="position: relative; top: 20px;" class="success-message">
        <div class="success-icon">
            ✔
        </div>
        <p class="success-text">
            {{ session('success') }}
        </p>
    </div>
    @endif
    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-9">
            <div style="padding: 25px" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="title">
                    User List

                </div>

                <a href="http://209.38.31.107/#/register" target="_blank" class="px-6 py-2 rounded-lg bg-blue">Register User</a>

                <table id="users-table" class="display table-style" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Last time seen</th>
                            <th>Actions</th>
                        </tr>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $user['id'] }}</td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td>{{ $user['last_login_at'] }}</td>
                            <td>
                                <div class="table-actions">
                                    <i class="pointer fa-regular fa-eye" onclick="window.location.href='{{ route('admin.user', ['id' => $user['id']]) }}'"></i>
                                    <i class="pointer fa-regular fa-pen-to-square" onclick="window.location.href='{{ route('admin.update-user', ['id' => $user['id']]) }}'"></i>
                                    <i class="pointer fa-regular fa-trash-can" onclick="confirmDelete({{ $user['id'] }})"></i>
                                    <form id="delete-form-{{ $user['id'] }}" action="{{ route('admin.delete-user', ['id' => $user['id']]) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
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

    console.log(variableLaravel);

    function confirmDelete(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "¡This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${userId}`).submit();
            }
        });
    }
</script>