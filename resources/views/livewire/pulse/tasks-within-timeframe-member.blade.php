<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <div>
        <x-pulse::card-header
            name="Tasks within Time Frame"
            title=""
            details="Total tasks due within a time frame"
            x-bind:details="$store.task_timeframe"
        >

            <x-slot:icon>
                <x-pulse::icons.queue-list />
            </x-slot:icon>

            <x-slot:actions>

                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                        <div class="h-0.5 w-3 rounded-full bg-[#9333ea]"></div>
                        <span>Tasks Due</span>
                    </div>
                </div>

                <x-pulse::select
                    label="Period"
                    x-model="$store.task_timeframe"
                    :options="[
                        '1' => '1 Hour',
                        '24' => '24 Hours',
                        '168' => '7 Days',
                        '720' => '30 Days',
                        '1440' => '60 Days',
                        '2160' => '90 Days',
                        '4320' => '180 Days',
                        '8760' => '365 Days',
                    ]"
                    class="flex-1"
                />
            </x-slot:actions>
        </x-pulse::card-header>

        <x-pulse::scroll :expand="$expand">
            <div class="grid gap-3 mx-px mb-px">
                <div wire:key="requests-graph" >
                    <div class="mt-3 relative" x-data='taskCompletionTimeChart({ sampleRate: 1 })'>

                        <div x-show="Object.keys(request.tasks_completed ?? {}).length > 1" class="absolute -left-px -top-2 max-w-fit h-4 flex items-center px-1 text-xs leading-none text-white font-bold bg-purple-500 rounded after:[--triangle-size:4px] after:border-l-purple-500 after:absolute after:right-[calc(-1*var(--triangle-size))] after:top-[calc(50%-var(--triangle-size))] after:border-t-[length:var(--triangle-size)] after:border-b-[length:var(--triangle-size)] after:border-l-[length:var(--triangle-size)] after:border-transparent">
                            <span x-text="highest_val"</span>
                        </div>
                        <div wire:ignore class="" >

                            <template x-if="!(Object.keys(request.tasks_completed ?? {}).length > 1)">
                                <x-pulse::no-results />
                            </template>
                            <div class="h-52" wire:ignore x-show="Object.keys(request.tasks_completed ?? {}).length > 1">
                                <canvas x-ref="canvas"
                                    class="h-52 ring-1 ring-gray-900/5 dark:ring-gray-100/10 bg-gray-50 dark:bg-gray-800 rounded-md shadow-sm"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-pulse::scroll>
    </div>
</x-pulse::card>

@script
<script>
    Alpine.store('task_timeframe', 2160)

    Alpine.data('taskCompletionTimeChart', (data) => ({

        request: {},
        highest_val: 0,

        async fetchData(){
            const url = new URL(
                "{{ route('analytics.tasks.index') }}"
            );



            let date = new Date();
            date.setHours(date.getHours() - $store.task_timeframe);

            const params = {
                "with": "user",
                "orderBy": "completed_at",
                "whereNotNull[]": "completed_at",
                "order": "asc",
                "where[1000][0]": "completed_at",
                "where[1000][1]": ">=",
                "where[1000][2]": date.toISOString(),
                "where[1001][0]": "user_id",
                "where[1001][1]": "=",
                "where[1001][2]": {{auth()->user()->id}},
                "page": "1",
                "perPage": "1000",
                "groupBy": "deadline"
            };

            Object.keys(params)
                .forEach(key => url.searchParams.append(key, params[key]));

            const headers = {
                "Authorization": "Bearer {{ request()->user()->createToken('auth-token')->plainTextToken }}",
                "Content-Type": "application/json",
                "Accept": "application/json",
            };

            const query = await fetch(url, {
                method: "GET",
                headers,
            }).then(response => response.json()).then(response => {
                    return response;
            }).catch(error => {
                console.error('Error:', error);
            });

            let grouped = Object.groupBy(Object.entries(query), ([ date, vals ]) => {
                date = new Date(date);

                return date.toLocaleString('default', { month: 'short', year: 'numeric' })
            })

            let formatted = {};

            Object.entries(grouped).map(([date, vals]) => {

                const lengths = vals.map(([date, vals]) => {
                    return vals.length
                }).reduce((a, b) => a + b, 0)

                formatted[date] = lengths;
            });

            const formattedArray = Object.entries(formatted).map(([date, count]) => ({date, count}));

            formattedArray.sort((a, b) => {
            const dateA = new Date(a.date);
            const dateB = new Date(b.date);
            return dateA - dateB;
            });

            const formattedSorted = formattedArray.reduce((acc, { date, count }) => {
            acc[date] = count;
            return acc;
            }, {});

            this.request = { tasks_completed: formattedSorted }

        },

        async init() {

            await this.fetchData();
            const request = this.request;
            this.highest_val = this.highest(request);

            let chart = new Chart(
                this.$refs.canvas,
                {
                    type: 'bar',
                    data: {
                        labels: this.labels(request[Object.keys(request)[0]]),
                        datasets: [
                            {
                                label: 'Tasks within time frame',
                                borderColor: '#9333ea',
                                backgroundColor: 'rgba(147,51,234,0.10)',
                                borderWidth: 2,
                                borderRadius: 3,
                                borderCapStyle: 'round',
                                data: this.scale(request.tasks_completed),
                                pointHitRadius: 10,
                                pointStyle: false,
                                tension: 0.2,
                                spanGaps: false,
                            },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            autoPadding: false,
                            padding: {
                                top: 1,
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
                                max: this.highest(request),
                            },
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                mode: 'index',
                                position: 'nearest',
                                intersect: false,
                                callbacks: {
                                    beforeBody: (context) => context
                                        .filter(item => item.formattedValue > 0)
                                        .map(item => `${item.dataset.label}: ${data.sampleRate < 1 ? '~' : ''}${item.formattedValue}`)
                                        .join(', '),
                                    label: () => null,
                                },
                            },
                        },
                    },
                }
            )

            $watch('$store.task_timeframe', async (value) => {

                 if (chart === undefined) {
                    return
                }

                await this.fetchData();

                const request = this.request;

                this.highest_val = this.highest(request);

                chart.data.labels = this.labels(request[Object.keys(request)[0]]);
                chart.options.scales.y.max = this.highest(request);
                chart.data.datasets[0].data = request.tasks_completed;
                chart.update()
            })

        },
        labels(status) {
            return Object.keys(status)
        },
        scale(status) {
            return Object.values(status).map(value => value * (1 / data.sampleRate ))
        },
        highest(request) {
            var highest = 0;

            Object.keys(request).map(status => {
                let max = Math.max(...Object.values(request[status]))
                highest = max > highest ? max : highest
            })

            return highest
        }
    }))
</script>
@endscript
