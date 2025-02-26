<x-app-layout>


    <div class="py-12">
        <div  class=" mx-auto sm:px-6 lg:px-9">
            <div style="padding: 25px" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                    <!-- Main Content -->
    <div class="container mx-auto mt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Sidebar -->
            <aside class="bg-white p-6 shadow-md rounded-lg">
                <div class="text-center justify-items-center">
                <x-application-logo class=" h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    <h2 class="mt-4 text-xl font-semibold text-gray-800">  {{$user['name']}}</h2>
                    <p class="text-gray-600"> {{$user['email']}} </p>
                </div>
            </aside>

            <!-- Main Section -->
            <main class="col-span-1 md:col-span-2 bg-white p-6 shadow-md rounded-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-4">User Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600">Full Name</label>
                        <p class="bg-gray-50 p-3 rounded-lg border">{{$user['name']}}</p>
                    </div>
                    <div>
                        <label class="block text-gray-600">Email Address</label>
                        <p class="bg-gray-50 p-3 rounded-lg border"> {{$user['email']}} </p>
                    </div>
                    <div>
                        <label class="block text-gray-600">Role</label>
                        <p class="bg-gray-50 p-3 rounded-lg border"> {{$user['role']}} </p>
                    </div>
                    <div>
                        <label class="block text-gray-600">Phone Number</label>
                        <p class="bg-gray-50 p-3 rounded-lg border">+123 456 7890</p>
                    </div>
                    <div>
                        <label class="block text-gray-600">Enabled User</label>
                        <p class="bg-gray-50 p-3 rounded-lg border"> {{$user['enabled']}}  </p>
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4">Recent Activity</h2>
                <ul class="space-y-4">
                <li class="flex items-center">
                        <span class="bg-green-100 text-red-600 px-4 py-2 rounded-full">Last Login</span>
                        <span class="ml-4 text-gray-600"> {{$user['last_login_at2']}}  </span>
                    </li>
                    <li class="flex items-center">
                        <span class="bg-blue-100 text-green-600  px-4 py-2 rounded-full">Created Profile</span>
                        <span class="ml-4 text-gray-600"> {{$user['created_at2']}} </span>
                    </li>
                    <li class="flex items-center">
                        <span class="bg-green-100 px-4 py-2 rounded-full">Updated Profile</span>
                        <span class="ml-4 text-gray-600"> {{$user['updated_at2']}}  </span>
                    </li>
                    <li class="flex items-center">
                        <span class="bg-green-100 px-4 py-2 rounded-full">Last Project Created</span>
                        <span class="ml-4 text-gray-600"> {{$user['last_project_created_at']}}  </span>
                    </li>
                    <li class="flex items-center">
                        <span class="bg-green-100 px-4 py-2 rounded-full">Last Project Updated</span>
                        <span class="ml-4 text-gray-600"> {{$user['last_project_created_at']}}  </span>
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

        var variableLaravel = @json($user); 
        console.log(variableLaravel);
    </script>