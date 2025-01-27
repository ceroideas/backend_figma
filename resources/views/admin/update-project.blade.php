<x-app-layout>


    <div class="py-12">
        <div class=" mx-auto sm:px-6 lg:px-9">
            <div style="padding: 25px" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <!-- Main Content -->
                <div class="container mx-auto mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        

                    @if (session('success'))
                    <div class="success-message">
                        <div class="success-icon">
                            ✔
                        </div>
                        <p class="success-text">
                            {{ session('success') }}
                        </p>
                    </div>
                    @endif

                        <aside class="bg-white p-6 shadow-md rounded-lg">
                            <div class="text-center justify-items-center">
                                <x-application-logo class=" h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                                <h2 class="mt-4 text-xl font-semibold text-gray-800"> {{$project['name']}} </h2>

                            </div>
                        </aside>

                        <!-- Main Section -->
                        <main class="col-span-1 md:col-span-2 bg-white p-6 shadow-md rounded-lg">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Project Information</h2>
                            


                            <form id="edit-user-form" action="{{ route('admin.project-update', $project['id']) }}" method="POST">
                                @csrf
                                @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-600">Name</label>
                                    <p class="bg-gray-50 p-3 rounded-lg border">{{$project['name']}} </p>
                                </div>
                                <div>
                                    <label class="block text-gray-600">Owner</label>
                                    <select id="owner-select" class="bg-gray-50 p-3 rounded-lg border w-full  h-full" name="user_id">
                                        @foreach($owners as $owner)
                                        <option value="{{ $owner['id'] }}" {{ $project['user_id'] == $owner['id'] ? 'selected' : '' }}>
                                            {{ $owner['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="footer">
                                    <button
                                        type="submit"
                                        id="save-user-btn"
                                        class=" px-6 py-2 rounded-lg bg-blue">
                                        Save Changes
                                    </button>
                                </div>



                            </form>



                        </main>
                        <main class="col-span-1 md:col-span-2 bg-white p-6 shadow-md rounded-lg">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">User Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-600">Full Name</label>
                                    <p class="bg-gray-50 p-3 rounded-lg border">{{$user['name']}} </p>
                                </div>
                                <div>
                                    <label class="block text-gray-600">Email Address</label>
                                    <p class="bg-gray-50 p-3 rounded-lg border"> {{$user['email']}} </p>
                                </div>
                                <div>
                                    <label class="block text-gray-600">Role</label>
                                    <p class="bg-gray-50 p-3 rounded-lg border"> {{$user['role']}}</p>
                                </div>
                            </div>

                            <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4">Recent Activity</h2>
                            <ul class="space-y-4">
                                <li class="flex items-center">
                                    <span class="bg-blue-100 text-blue-600 px-4 py-2 rounded-full">Created In</span>
                                    <span class="ml-4 text-gray-600"> {{ $user['created_at'] }} </span>
                                </li>
                                <li class="flex items-center">
                                    <span class="bg-green-100 text-green-600 px-4 py-2 rounded-full">Updated Profile</span>
                                    <span class="ml-4 text-gray-600"> {{ $user['updated_at'] }} </span>
                                </li>
                            </ul>

                        </main>
                    </div>
                </div>



            </div>
        </div>

    </div>

</x-app-layout>
<link rel="stylesheet" type="text/css" href="{{ asset('css/styles.css') }}">
<script>
    $(document).ready(function() {
        $('#owner-select').select2();
    });
    var project = @json($project);
    var user = @json($user);
    console.log(project, user);
</script>