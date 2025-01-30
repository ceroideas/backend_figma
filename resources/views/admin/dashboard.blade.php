<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-9">
            <div class="flex flex-wrap justify-center gap-custom ">
                <!-- Tarjeta de Usuarios -->
                <div class="w-full md:w-1/5 lg:w-1/6 bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg p-6 text-center">
                    <div class="text-gray-900 dark:text-gray-100">
                        <i class="fas fa-users text-6xl"></i> <!-- Ícono grande -->
                        <h2 class="text-lg-custom font-semibold mt-4">Usuarios</h2>
                        <p class="text-2xl font-semibold mt-2"> {{$usersCount}}  </p>
                    </div>
                </div>

                <!-- Tarjeta de Proyectos -->
                <div class="w-full md:w-1/5 lg:w-1/6 bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg p-6 text-center">
                    <div class="text-gray-900 dark:text-gray-100">
                        <i class="fas fa-briefcase text-6xl"></i> <!-- Ícono grande -->
                        <h2 class="text-lg-custom font-semibold mt-4">Proyectos</h2>
                        <p class="text-2xl font-semibold mt-2">  {{$projectsCount}}  </p>
                    </div>
                </div>

                <!-- Tarjeta de Simulaciones -->
                <div class="w-full md:w-1/5 lg:w-1/6 bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg p-6 text-center">
                    <div class="text-gray-900 dark:text-gray-100">
                        <i class="fas fa-chart-line text-6xl"></i> <!-- Ícono grande -->
                        <h2 class="text-lg-custom font-semibold mt-4">Simulaciones</h2>
                        <p class="text-2xl font-semibold mt-2"> {{$simulationsCount}} </p>
                    </div>
                </div>
            </div>
            <div style="height: 450px !important; display: flex; flex-direction: column; width: 100%; margin-top: 20px;">


                <div style="height: 450px !important; display: flex; justify-content: space-between; width: 100%; gap: 30px;" class="flex">
                    <div style="flex: 1;" class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg p-6 card-container">
                        <div>
                            <label for="yearSelect" class="  text-gray-600">Year</label>

                            <select style="width: 80px;" id="yearSelect" class="bg-gray-50 p-3 rounded-lg ">
                            </select>


                            <label style="margin-left: 10px;" for="monthSelect" class="  text-gray-600">Month</label>
                            <select style="width: 130px;" id="monthSelect" class="bg-gray-50 p-3 rounded-lg">
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>


                            <button  style="margin-left: 10px;"  class=" px-6 py-2 rounded-lg bg-blue"  id="loadDataBtn">Search </button>
                        </div>
                        <canvas id="activeUsersChart" class="chart-container"></canvas>
                    </div>
                    <div style="flex: 1;" class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg p-6 card-container">
                    <div>
                            <label for="yearSelect" class="  text-gray-600">Year</label>

                            <select style="width: 80px;" id="yearSelectProject" class="bg-gray-50 p-3 rounded-lg ">
                            </select>


                            <label style="margin-left: 10px;" for="monthSelect" class="  text-gray-600">Month</label>
                            <select style="width: 130px;" id="monthSelectProject" class="bg-gray-50 p-3 rounded-lg">
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>


                            <button  style="margin-left: 10px;"  class=" px-6 py-2 rounded-lg bg-blue"  id="loadDataBtnProject">Search </button>
                        </div>
                        <canvas id="activeUsersChart2" class="chart-container"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<link rel="stylesheet" type="text/css" href="{{ asset('css/styles.css') }}">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const yearSelect = document.getElementById('yearSelect');
        const monthSelect = document.getElementById('monthSelect');
        const loadDataBtn = document.getElementById('loadDataBtn');

        const yearSelectProject = document.getElementById('yearSelectProject');
        const monthSelectProject = document.getElementById('monthSelectProject');
        const loadDataBtnProject = document.getElementById('loadDataBtnProject');

        // Generar años dinámicamente (últimos 5 años hasta el actual)
        const currentYear = new Date().getFullYear();
        for (let i = currentYear; i >= currentYear - 5; i--) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            yearSelect.appendChild(option);
            
        }
        for (let i = currentYear; i >= currentYear - 5; i--) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            yearSelectProject.appendChild(option);
            
        }


        // Evento para cargar datos cuando se hace clic en el botón
        loadDataBtn.addEventListener('click', function() {
            const selectedYear = yearSelect.value;
            const selectedMonth = monthSelect.value;
            fetchData(selectedMonth, selectedYear);
        });

        loadDataBtnProject.addEventListener('click', function() {
            const selectedYear = yearSelectProject.value;
            const selectedMonth = monthSelectProject.value;
            fetchDataProject(selectedMonth, selectedYear);
        });

        // Cargar datos al iniciar la página con el mes y año actual
        fetchData(new Date().getMonth() + 1, currentYear);
        fetchDataProject(new Date().getMonth() + 1, currentYear);
    });



    function fetchData(month, year) {
        fetch(`/api/public/admin/get-active-users/${month}/${year}`)
            .then(response => response.json())
            .then(data => {
                console.log('Datos recibidos:', data);
                updateChart(data);
            })
            .catch(error => console.error('Error al obtener datos:', error));
    }

    function fetchDataProject(month, year) {
        fetch(`/api/public/admin/get-active-projects/${month}/${year}`)
            .then(response => response.json())
            .then(data => {
                console.log('Datos recibidos:', data);
                updateChartProjects(data);
            })
            .catch(error => console.error('Error al obtener datos:', error));
    }

    function updateChart(dailyActiveUsers) {
        const ctx1 = document.getElementById('activeUsersChart').getContext('2d');
        const ctx2 = document.getElementById('activeUsersChart2').getContext('2d');

        const dates = Object.keys(dailyActiveUsers).map(date => {
            const [year, month, day] = date.split('-');
            return `${day}/${month}`;
        });
        const counts = Object.values(dailyActiveUsers);
        console.log(dates, counts);
        const chartData = {
            labels: dates,
            datasets: [{
                label: 'Active Users',
                data: counts,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: false
            }]
        };
        const options = {
            scales: {
                x: {
                    type: 'category', // Usar 'category' para el eje X
                    labels: dates
                },
                y: {
                    beginAtZero: true, // Empezar el eje Y desde 0
                    ticks: {
                        precision: 0, // Forzar números enteros en el eje Y
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                        }
                    }
                }
            }
        }

        // Destruir gráficos previos si existen
        if (window.myChart1) window.myChart1.destroy();
       


        window.myChart1 = new Chart(ctx1, {
            type: 'line',
            data: chartData,
            options: options
        });
   
    }

    function updateChartProjects(dailyActiveUsers) {
        const ctx1 = document.getElementById('activeUsersChart').getContext('2d');
        const ctx2 = document.getElementById('activeUsersChart2').getContext('2d');

        const dates = Object.keys(dailyActiveUsers).map(date => {
            const [year, month, day] = date.split('-');
            return `${day}/${month}`;
        });
        const counts = Object.values(dailyActiveUsers);

        const chartData = {
            labels: dates,
            datasets: [{
                label: 'Active Projects',
                data: counts,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: false
            }]
        };
        const options = {
            scales: {
                x: {
                    type: 'category', // Usar 'category' para el eje X
                    labels: dates
                },
                y: {
                    beginAtZero: true, // Empezar el eje Y desde 0
                    ticks: {
                        precision: 0, // Forzar números enteros en el eje Y
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                        }
                    }
                }
            }
        }

  
        if (window.myChart2) window.myChart2.destroy();


        window.myChart2 = new Chart(ctx2, {
            type: 'line',
            data: chartData,
            options: options
        });
    }
</script>