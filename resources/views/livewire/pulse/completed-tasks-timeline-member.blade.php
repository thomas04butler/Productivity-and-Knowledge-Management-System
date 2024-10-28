<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <div>
        <x-pulse::card-header
            name="Task Timeline"
            title=""
            details="Average task completion time in days"
            x-bind:details="$store.timeline_period"
        >

            <x-slot:icon>
                <x-pulse::icons.clock />
            </x-slot:icon>

            <x-slot:actions>

                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                        <div class="h-0.5 w-3 rounded-full bg-[#9333ea]"></div>
                        <span>Tasks Completed</span>
                    </div>
                </div>

                <x-pulse::select
                    label="Period"
                    x-model="$store.timeline_period"
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
                            <span x-text="highest_val.toFixed(2)"</span>
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
    Alpine.store('timeline_period', 2160)

    Alpine.data('taskCompletionTimeChart', (data) => ({

        request: {},
        highest_val: 0,
        width: 0,
        height: 0,
        gradient: null,

        getGradient(ctx, chartArea){
            const chartWidth = chartArea.right - chartArea.left;
            const chartHeight = chartArea.bottom - chartArea.top;
            if (!this.gradient || this.width !== chartWidth || this.height !== chartHeight) {
                // Create the gradient because this is either the first render
                // or the size of the chart has changed
                this.width = chartWidth;
                this.height = chartHeight;
                this.gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                gradient.addColorStop(0.3, '#5B91FC');
                gradient.addColorStop(0.5, '#9333ea');
                gradient.addColorStop(0.8, '#e11d48');
            }
            return gradient
        },

        async fetchData(){
            const url = new URL(
                "{{ route('analytics.tasks.index') }}"
            );

            let date = new Date();
            date.setHours(date.getHours() - $store.timeline_period);

            const params = {
                "with": "user",
                "orderBy": "completed_at",
                "whereNotNull[]": "completed_at",
                "order": "asc",
                "where[1000][0]": "completed_at",
                "where[1000][1]": ">",
                "where[1000][2]": date.toISOString(),
                "where[1001][0]": "user_id",
                "where[1001][1]": "=",
                "where[1001][2]": {{auth()->user()->id}},
                "page": "1",
                "perPage": "1000",
                "groupBy": "completed_at"
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

            // let grouped = Object.groupBy(Object.entries(query), ([ date, vals ]) => {
            //     date = new Date(date);
            //
            //     return date.toLocaleString('default', {month: 'short', year: 'numeric'})
            // })

            let formatted = {};
            Object.entries(query).map(([date, vals]) => {

                const lengths = vals.map(e => {
                    const date1 = new Date(e.completed_at);
                    const date2 = new Date(e.created_at);
                    const diffTime = Math.abs(date2 - date1);
                    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                    return diffDays
                }).reduce((a, b) => a + b, 0) / vals.length

                // const lengths = vals.map(([date, vals]) => {
                //     return vals.map(e => {
                //             const date1 = new Date(e.completed_at);
                //             const date2 = new Date(e.created_at);
                //             const diffTime = Math.abs(date2 - date1);
                //             const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                //             return diffDays
                //         }
                //     ).reduce((a, b) => a + b, 0)
                // }).reduce((a, b) => a + b, 0) / vals.length

                formatted[date] = lengths;
            });

            this.request = { tasks_completed: formatted }

        },

        async init() {

            await this.fetchData();
            const request = this.request;

            this.highest_val = this.highest(request);

            let getGradient = this.getGradient;

            let chart = new Chart(
                this.$refs.canvas,
                {
                    type: 'line',
                    data: {
                        labels: this.labels(request[Object.keys(request)[0]]),
                        datasets: [
                            {
                                label: 'Average Completed Task Length',
                                borderColor: function(context) {
                                    const chart = context.chart;
                                    const {ctx, chartArea} = chart;

                                    if (!chartArea) {
                                      // This case happens on initial chart load
                                      return;
                                    }
                                    return getGradient(ctx, chartArea);
                                },
                                borderWidth: 2,
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

            $watch('$store.timeline_period', async (value) => {

                 if (chart === undefined) {
                    return
                }

                await this.fetchData();

                const request = this.request;

                this.highest_val = this.highest(request);

                chart.data.labels = this.labels(request[Object.keys(request)[0]]);
                chart.data.datasets[0].data = this.scale(request.tasks_completed);
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
