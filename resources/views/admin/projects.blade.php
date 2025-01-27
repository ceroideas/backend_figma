<x-app-layout>


    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-9">
            <div style="padding: 25px" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="title">
                    Project List

                </div>


                <table id="projects-table" class="display table-style" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Owner</th>
                            <th>Year (from - to)</th>
                            <th>Number of scenarios</th>
                            <th>Color Line</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                        <tr>
                            <td>{{ $project['id'] }}</td>
                            <td>{{ $project['name'] }}</td>
                            <td>{{ $project['user_name'] }}</td>
                            <td>{{ $project['year_from'] . '-' . $project['year_to'] }}</td>
                            <td>{{ $project['sceneries_count'] }}</td>
                            <td style="    justify-items: center;">
                                <div style="width: 20px; height: 20px; background-color: rgb({{ $project['line_color'] }}); border-radius: 50%;"></div>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <i class="pointer fa-regular fa-eye" onclick="window.location.href='{{ route('admin.project', ['id' => $project['id']]) }}'"></i>
                                    <i class="pointer fa-regular fa-pen-to-square" onclick="window.location.href='{{ route('admin.update-project', ['id' => $project['id']]) }}'"></i>
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
        $('#projects-table').DataTable();
    });
    var variableLaravel = @json($projects);
    console.log(variableLaravel);
</script>