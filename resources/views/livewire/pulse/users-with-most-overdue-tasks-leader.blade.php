<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header 
    name="Users with most overdue tasks"
    details="Users with most overdue tasks in projects you manage">
    >
        <x-slot:icon>

        </x-slot:icon>

        <x-slot:actions>
            <x-pulse::select
                label="Top"
                x-model="$store.overdue_users"
                :options="[
                    '5' => '5 Users',
                    '10' => '10 Users',
                    '15' => '15 Users',
                ]"
                class="flex-1"
            />
        </x-slot:actions>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand">
        <div class="grid gap-3 mx-px mb-px">
            <div wire:key="requests-graph">
                <div class="mt-3 relative">
                    <div wire:ignore class="h-52" x-data='usersWithMostTasks(@json($userToken), @json($analyticsUrl))'>
                        <canvas x-ref="canvas" class="h-52 ring-1 ring-gray-900/5 dark:ring-gray-100/10 bg-gray-50 dark:bg-gray-800 rounded-md shadow-sm"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </x-pulse::scroll>
</x-pulse::card>

@script
<script>

    Alpine.store('overdue_users', 5)

    Alpine.data('usersWithMostTasks', (token, url) => ({

        async fetchTasksData(token, url) {
            let overdueTasks = {};

            const params = {
                "with[0]": "user",
                "with[1]": "project",
                "page": "1",
                "perPage": "1000",
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
                // filter tasks into overdue only
                const today = new Date().toISOString().split('T')[0]; // Today's date
                data.data.forEach(task => {
                    if(task.project.user_id == {{auth()->user()->id}}) {
                        const userId = task.user_id;
                        const userName = task.user.name;
                        const deadlineDate = new Date(task.deadline).toISOString().split('T')[0]; // Parse deadline date

                        if (task.completed_at === null && task.deadline < today) {
                        
                            if (overdueTasks[userId]) {
                                overdueTasks[userId].count++;
                            } else {
                                overdueTasks[userId] = { count: 1, name: userName };
                            }
                            
                        }
                    }   
                });
                const taskCountsArray = Object.values(overdueTasks);

                taskCountsArray.sort((a, b) => b.count - a.count);

                this.taskCountsArray = taskCountsArray.slice(0, $store.overdue_users);
                
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
           
        },


        async init() {
            // initial data fetch
            await this.fetchTasksData(token, url);

            // set interval to fetch data every 5 seconds
            // setInterval(() => {
            //     this.fetchTasksData(token, url);
            // }, 30000);

            const request = this.taskCountsArray;
            this.highest_val = request[0].count;

            let chart = new Chart(
                this.$refs.canvas, {
                type: 'bar',
                data: {
                    labels: this.labels(request),
                    datasets: [{
                        label: 'Overdue Tasks',
                        data: this.data(request),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.10)', // Bar color for overdue tasks
                        borderWidth: 2,
                        borderRadius: 3,
                        borderCapStyle: 'round',
                        pointHitRadius: 10,
                        pointStyle: false,
                        tension: 0.2,
                        spanGaps: false,
                    }]
                },
                options: {
                    // disable animation to prevent charts from playing the animation when they refresh
                    maintainAspectRatio: false,
                    // animation: false,
                    layout: {
                            autoPadding: false,
                            padding: {
                                top: 2,
                            },
                        },
                    datasets: {
                        line: {
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                            segment: {
                                borderColor: (ctx) => ctx.p0.raw === 0 && ctx.p1.raw === 0 ? 'transparent' : undefined,
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                            min: 0,
                            max: this.highest_val,
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                },
            });

            $watch('$store.overdue_users', async (value) => {

                if (chart === undefined) {
                return
                }

                await this.fetchTasksData(token, url);

                const request = this.taskCountsArray;

                this.highest_val = request[0].count;

                chart.data.labels = this.labels(request);
                chart.data.datasets[0].data = this.data(request);
                chart.update()
            })
        },
        labels(request) {
            return request.map(task => task.name);
        },
        data(request) {
            return request.map(task => task.count);
        },
    }));

</script>
@endscript

