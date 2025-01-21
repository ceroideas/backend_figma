<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-9">
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
                        <!-- Sidebar -->
                        <aside class="bg-white p-6 shadow-md rounded-lg">
                            <div class="text-center justify-items-center">
                                <x-application-logo class="h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                                <h2 class="mt-4 text-xl font-semibold text-gray-800">{{$user['name']}}</h2>
                                <p class="text-gray-600">{{$user['email']}}</p>
                            </div>
                        </aside>

                        <!-- Main Section -->
                        <main class="col-span-1 md:col-span-2 bg-white p-6 shadow-md rounded-lg">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Edit User Information</h2>

                            <!-- Formulario para editar los datos -->
                            <form id="edit-user-form" action="{{ route('admin.update', $user['id']) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-gray-600">Full Name</label>
                                        <input
                                            type="text"
                                            name="name"
                                            id="name"
                                            class="bg-gray-50 p-3 rounded-lg border w-full"
                                            value="{{$user['name']}}"
                                            required />
                                    </div>
                                    <div>
                                        <label for="email" class="block text-gray-600">Email Address</label>
                                        <input
                                            type="email"
                                            name="email"
                                            id="email"
                                            class="bg-gray-50 p-3 rounded-lg border w-full"
                                            value="{{$user['email']}}"
                                            required />
                                    </div>
                                    <div>
                                        <label for="is_admin" class="block text-gray-600">Role</label>
                                        <select
                                            name="is_admin"
                                            id="is_admin"
                                            class="bg-gray-50 p-3 rounded-lg border w-full"
                                            required>
                                            <option value="1" {{$user['is_admin'] == 1 ? 'selected' : ''}}>Admin</option>
                                            <option value="0" {{$user['is_admin'] == 0 ? 'selected' : ''}}>User</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="is_enabled" class="block text-gray-600">Enabled User</label>
                                        <select
                                            name="is_enabled"
                                            id="is_enabled"
                                            class="bg-gray-50 p-3 rounded-lg border w-full"
                                            required>
                                            <option value="1" {{$user['is_enabled'] == 1 ? 'selected' : ''}}>Yes</option>
                                            <option value="0" {{$user['is_enabled'] == 0 ? 'selected' : ''}}>No</option>
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