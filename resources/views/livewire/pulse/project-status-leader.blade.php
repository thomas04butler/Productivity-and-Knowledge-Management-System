<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header 
    name="Project Statuses"
    details="Statuses of projects you manage"
    >
        <x-slot:icon>

        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand">
        <div class="grid gap-3 mx-px mb-px">
            <div wire:key="requests-graph">
                <div class="mt-3 relative">
                    <div wire:ignore class="h-52" x-data='projectStatus(@json($userToken), @json($analyticsUrl))'>
                    <canvas x-ref="canvas" class="h-52 ring-1 ring-gray-900/5 dark:ring-gray-100/10 bg-gray-50 dark:bg-gray-800 rounded-md shadow-sm"></canvas>
                    <div x-show="totalProjects > 0" class="absolute top-0 left-0 w-full h-full flex justify-center items-center pointer-events-none">
                        <div class="text-center text-sm font-semibold text-gray-800 dark:text-gray-200">
                            <span class="text-xl" x-text="totalProjects"></span>
                            <span class="block text-s text-gray-600 dark:text-gray-400">Total Projects</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-pulse::scroll>
</x-pulse::card>

@script
<script>

    Alpine.data('projectStatus', (token, url) => ({
        totalProjects: 0,
        projectCounts: {
            outstanding: 0,
            overdue: 0,
            completed: 0,
        },
        
        async fetchProjectsData(token, url) {
            let currentPage = 1;
            let totalPages = 1;
            this.projectCounts = {
                outstanding: 0,
                overdue: 0,
                completed: 0,
            };

            const params = {
                "with": "tasks",
                "page": "1",
                "perPage": "1000",
                "where[1000][0]": "user_id",
                "where[1000][1]": "=",
                "where[1000][2]": {{auth()->user()->id}},
            };
            // construct the URL string with query parameters
            const urlWithParams = new URL(url);
            Object.entries(params).forEach(([key, value]) => {
                urlWithParams.searchParams.append(key, value);
            });

            await fetch(urlWithParams, {
                method: "GET",
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const today = new Date().toISOString().split('T')[0]; // Today's date
                
                data.data.forEach(project => {

                    const projectDeadlineDate = new Date(project.deadline).toISOString().split('T')[0]; // Parse deadline date

                    if (projectDeadlineDate < today) {
                        this.projectCounts.overdue++;
                        this.totalProjects++;
                    } else {
                        const totalTasks = project.tasks.length;
                        var completedTasks = 0;
                        project.tasks.forEach(task => {
                            if (task.completed_at !== null) {
                                completedTasks++;
                            }
                        });
                        if (totalTasks == completedTasks) {
                            this.projectCounts.completed++;
                            this.totalProjects++;
                        } else {
                            this.projectCounts.outstanding++;
                            this.totalProjects++;
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        },

        async init() {
            // initial data fetch
            await this.fetchProjectsData(token, url);

            // set interval to fetch data every 5 seconds
            // setInterval(() => {
            //     this.fetchProjectsData(token, url);
            // }, 30000);
            
            const request = this.projectCounts;
            
            let chart = new Chart(
                this.$refs.canvas, {
                type: 'doughnut',
                data: {
                    labels: ['Outstanding', 'Overdue', 'Completed'],
                    datasets: [{
                        label: 'Number of projects',
                        data: [request.outstanding, request.overdue, request.completed],
                        backgroundColor: ['rgba(37, 99, 235, 0.6)', 'rgba(239, 68, 68, 0.6)', 'rgba(34, 197, 94, 0.6)'],
                        borderWidth: 2,
                        pointHitRadius: 10,
                        pointStyle: false,
                        tension: 0.2,
                        spanGaps: false,
                    }]
                },
                options: {
                    // disable animation to prevent charts from playing the animation when they refresh
                    maintainAspectRatio: false,
                    animation: false, 
                    cutout: 70,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                },
            });
        },
    }));

</script>
@endscript

