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
                    Simulations List

                </div>


                <table id="simulations-table" class="display table-style" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Steps</th>
                            <th>Size</th>
                            <th>Time</th>
                            <th>Project</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($simulations as $simulation)
                        <tr>
                            <td>{{ $simulation['id'] }}</td>
                            <td>{{ $simulation['name'] }}</td>
                            <td>{{ $simulation['steps'] }}</td>
                            <td>{{ $simulation['csvDataSize'] . "MB" }}</td>
                            <td>{{ $simulation['execution_time'] }}</td>
                            <td>{{ $simulation['project_name'] }}</td>
                            <td>

                                <i class="pointer fa-regular fa-trash-can" onclick="confirmDelete({{ $simulation['id'] }})"></i>
                                <form id="delete-form-{{ $simulation['id'] }}" action="{{ route('admin.delete-simulation', ['id' => $simulation['id']]) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
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

    function confirmDelete(simulationId) {
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
                document.getElementById(`delete-form-${simulationId}`).submit();
            }
        });
    }
</script>