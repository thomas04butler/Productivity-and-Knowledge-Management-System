<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="Requests" title=""
        details="">
        <x-slot:icon>
            <x-pulse::icons.arrows-left-right />
        </x-slot:icon>
        <x-slot:actions>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[rgba(29,153,172,0.5)]"></div>
                    Informational
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[#9333ea]"></div>
                    Successful
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[rgba(107,114,128,0.5)]"></div>
                    Redirection
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[#eab308]"></div>
                    Client Error
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[#e11d48]"></div>
                    Server Error
                </div>
            </div>
        </x-slot:actions>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        <div class="grid gap-3 mx-px mb-px">
            <div wire:key="requests-graph">

                @php
                if(!function_exists('hightestValue')){
                    function hightestValue($data){
                        $highest = 0;

                        foreach($data as $item) {
                            $max = max($item);
                            $highest = $max > $highest ? $max : $highest;
                        }

                        return $highest;
                    }
                }

                $highest = 12;
                @endphp

                <div class="mt-3 relative">

                    <div wire:ignore class="" x-data='requestsChart({
                                    request: {
  "informational": {
    "2024-04-28 00:05:00": null,
    "2024-04-28 00:06:00": null,
    "2024-04-28 00:07:00": null,
    "2024-04-28 00:08:00": null,
    "2024-04-28 00:09:00": null,
    "2024-04-28 00:10:00": null,
    "2024-04-28 00:11:00": null,
    "2024-04-28 00:12:00": null,
    "2024-04-28 00:13:00": null,
    "2024-04-28 00:14:00": null,
    "2024-04-28 00:15:00": null,
    "2024-04-28 00:16:00": null,
    "2024-04-28 00:17:00": null,
    "2024-04-28 00:18:00": null,
    "2024-04-28 00:19:00": null,
    "2024-04-28 00:20:00": null,
    "2024-04-28 00:21:00": null,
    "2024-04-28 00:22:00": null,
    "2024-04-28 00:23:00": null,
    "2024-04-28 00:24:00": null,
    "2024-04-28 00:25:00": null,
    "2024-04-28 00:26:00": null,
    "2024-04-28 00:27:00": null,
    "2024-04-28 00:28:00": null,
    "2024-04-28 00:29:00": null,
    "2024-04-28 00:30:00": null,
    "2024-04-28 00:31:00": null,
    "2024-04-28 00:32:00": null,
    "2024-04-28 00:33:00": null,
    "2024-04-28 00:34:00": null,
    "2024-04-28 00:35:00": null,
    "2024-04-28 00:36:00": null,
    "2024-04-28 00:37:00": null,
    "2024-04-28 00:38:00": null,
    "2024-04-28 00:39:00": null,
    "2024-04-28 00:40:00": null,
    "2024-04-28 00:41:00": null,
    "2024-04-28 00:42:00": null,
    "2024-04-28 00:43:00": null,
    "2024-04-28 00:44:00": null,
    "2024-04-28 00:45:00": null,
    "2024-04-28 00:46:00": null,
    "2024-04-28 00:47:00": null,
    "2024-04-28 00:48:00": null,
    "2024-04-28 00:49:00": null,
    "2024-04-28 00:50:00": null,
    "2024-04-28 00:51:00": null,
    "2024-04-28 00:52:00": null,
    "2024-04-28 00:53:00": null,
    "2024-04-28 00:54:00": null,
    "2024-04-28 00:55:00": null,
    "2024-04-28 00:56:00": null,
    "2024-04-28 00:57:00": null,
    "2024-04-28 00:58:00": null,
    "2024-04-28 00:59:00": null,
    "2024-04-28 01:00:00": null,
    "2024-04-28 01:01:00": null,
    "2024-04-28 01:02:00": null,
    "2024-04-28 01:03:00": null,
    "2024-04-28 01:04:00": null
  },
  "successful": {
    "2024-04-28 00:05:00": null,
    "2024-04-28 00:06:00": null,
    "2024-04-28 00:07:00": null,
    "2024-04-28 00:08:00": null,
    "2024-04-28 00:09:00": null,
    "2024-04-28 00:10:00": null,
    "2024-04-28 00:11:00": null,
    "2024-04-28 00:12:00": null,
    "2024-04-28 00:13:00": null,
    "2024-04-28 00:14:00": null,
    "2024-04-28 00:15:00": null,
    "2024-04-28 00:16:00": null,
    "2024-04-28 00:17:00": null,
    "2024-04-28 00:18:00": null,
    "2024-04-28 00:19:00": null,
    "2024-04-28 00:20:00": null,
    "2024-04-28 00:21:00": null,
    "2024-04-28 00:22:00": null,
    "2024-04-28 00:23:00": null,
    "2024-04-28 00:24:00": null,
    "2024-04-28 00:25:00": null,
    "2024-04-28 00:26:00": null,
    "2024-04-28 00:27:00": null,
    "2024-04-28 00:28:00": null,
    "2024-04-28 00:29:00": null,
    "2024-04-28 00:30:00": null,
    "2024-04-28 00:31:00": null,
    "2024-04-28 00:32:00": null,
    "2024-04-28 00:33:00": null,
    "2024-04-28 00:34:00": null,
    "2024-04-28 00:35:00": null,
    "2024-04-28 00:36:00": null,
    "2024-04-28 00:37:00": null,
    "2024-04-28 00:38:00": null,
    "2024-04-28 00:39:00": null,
    "2024-04-28 00:40:00": null,
    "2024-04-28 00:41:00": null,
    "2024-04-28 00:42:00": null,
    "2024-04-28 00:43:00": null,
    "2024-04-28 00:44:00": null,
    "2024-04-28 00:45:00": null,
    "2024-04-28 00:46:00": null,
    "2024-04-28 00:47:00": null,
    "2024-04-28 00:48:00": null,
    "2024-04-28 00:49:00": null,
    "2024-04-28 00:50:00": null,
    "2024-04-28 00:51:00": null,
    "2024-04-28 00:52:00": null,
    "2024-04-28 00:53:00": null,
    "2024-04-28 00:54:00": null,
    "2024-04-28 00:55:00": null,
    "2024-04-28 00:56:00": null,
    "2024-04-28 00:57:00": null,
    "2024-04-28 00:58:00": null,
    "2024-04-28 00:59:00": null,
    "2024-04-28 01:00:00": null,
    "2024-04-28 01:01:00": null,
    "2024-04-28 01:02:00": "1.00",
    "2024-04-28 01:03:00": "3.00",
    "2024-04-28 01:04:00": "6.00"
  },
  "redirection": {
    "2024-04-28 00:05:00": null,
    "2024-04-28 00:06:00": null,
    "2024-04-28 00:07:00": null,
    "2024-04-28 00:08:00": null,
    "2024-04-28 00:09:00": null,
    "2024-04-28 00:10:00": null,
    "2024-04-28 00:11:00": null,
    "2024-04-28 00:12:00": null,
    "2024-04-28 00:13:00": null,
    "2024-04-28 00:14:00": null,
    "2024-04-28 00:15:00": null,
    "2024-04-28 00:16:00": null,
    "2024-04-28 00:17:00": null,
    "2024-04-28 00:18:00": null,
    "2024-04-28 00:19:00": null,
    "2024-04-28 00:20:00": null,
    "2024-04-28 00:21:00": null,
    "2024-04-28 00:22:00": null,
    "2024-04-28 00:23:00": null,
    "2024-04-28 00:24:00": null,
    "2024-04-28 00:25:00": null,
    "2024-04-28 00:26:00": null,
    "2024-04-28 00:27:00": null,
    "2024-04-28 00:28:00": null,
    "2024-04-28 00:29:00": null,
    "2024-04-28 00:30:00": null,
    "2024-04-28 00:31:00": null,
    "2024-04-28 00:32:00": null,
    "2024-04-28 00:33:00": null,
    "2024-04-28 00:34:00": null,
    "2024-04-28 00:35:00": null,
    "2024-04-28 00:36:00": null,
    "2024-04-28 00:37:00": null,
    "2024-04-28 00:38:00": null,
    "2024-04-28 00:39:00": null,
    "2024-04-28 00:40:00": null,
    "2024-04-28 00:41:00": null,
    "2024-04-28 00:42:00": null,
    "2024-04-28 00:43:00": null,
    "2024-04-28 00:44:00": null,
    "2024-04-28 00:45:00": null,
    "2024-04-28 00:46:00": null,
    "2024-04-28 00:47:00": null,
    "2024-04-28 00:48:00": null,
    "2024-04-28 00:49:00": null,
    "2024-04-28 00:50:00": null,
    "2024-04-28 00:51:00": null,
    "2024-04-28 00:52:00": null,
    "2024-04-28 00:53:00": null,
    "2024-04-28 00:54:00": null,
    "2024-04-28 00:55:00": null,
    "2024-04-28 00:56:00": null,
    "2024-04-28 00:57:00": null,
    "2024-04-28 00:58:00": null,
    "2024-04-28 00:59:00": null,
    "2024-04-28 01:00:00": null,
    "2024-04-28 01:01:00": null,
    "2024-04-28 01:02:00": null,
    "2024-04-28 01:03:00": null,
    "2024-04-28 01:04:00": null
  },
  "client_error": {
    "2024-04-28 00:05:00": null,
    "2024-04-28 00:06:00": null,
    "2024-04-28 00:07:00": null,
    "2024-04-28 00:08:00": null,
    "2024-04-28 00:09:00": null,
    "2024-04-28 00:10:00": null,
    "2024-04-28 00:11:00": null,
    "2024-04-28 00:12:00": null,
    "2024-04-28 00:13:00": null,
    "2024-04-28 00:14:00": null,
    "2024-04-28 00:15:00": null,
    "2024-04-28 00:16:00": null,
    "2024-04-28 00:17:00": null,
    "2024-04-28 00:18:00": null,
    "2024-04-28 00:19:00": null,
    "2024-04-28 00:20:00": null,
    "2024-04-28 00:21:00": null,
    "2024-04-28 00:22:00": null,
    "2024-04-28 00:23:00": null,
    "2024-04-28 00:24:00": null,
    "2024-04-28 00:25:00": null,
    "2024-04-28 00:26:00": null,
    "2024-04-28 00:27:00": null,
    "2024-04-28 00:28:00": null,
    "2024-04-28 00:29:00": null,
    "2024-04-28 00:30:00": null,
    "2024-04-28 00:31:00": null,
    "2024-04-28 00:32:00": null,
    "2024-04-28 00:33:00": null,
    "2024-04-28 00:34:00": null,
    "2024-04-28 00:35:00": null,
    "2024-04-28 00:36:00": null,
    "2024-04-28 00:37:00": null,
    "2024-04-28 00:38:00": null,
    "2024-04-28 00:39:00": null,
    "2024-04-28 00:40:00": null,
    "2024-04-28 00:41:00": null,
    "2024-04-28 00:42:00": null,
    "2024-04-28 00:43:00": null,
    "2024-04-28 00:44:00": null,
    "2024-04-28 00:45:00": null,
    "2024-04-28 00:46:00": null,
    "2024-04-28 00:47:00": null,
    "2024-04-28 00:48:00": null,
    "2024-04-28 00:49:00": null,
    "2024-04-28 00:50:00": null,
    "2024-04-28 00:51:00": null,
    "2024-04-28 00:52:00": null,
    "2024-04-28 00:53:00": null,
    "2024-04-28 00:54:00": null,
    "2024-04-28 00:55:00": null,
    "2024-04-28 00:56:00": null,
    "2024-04-28 00:57:00": null,
    "2024-04-28 00:58:00": null,
    "2024-04-28 00:59:00": null,
    "2024-04-28 01:00:00": null,
    "2024-04-28 01:01:00": null,
    "2024-04-28 01:02:00": null,
    "2024-04-28 01:03:00": null,
    "2024-04-28 01:04:00": null
  },
  "server_error": {
    "2024-04-28 00:05:00": null,
    "2024-04-28 00:06:00": null,
    "2024-04-28 00:07:00": null,
    "2024-04-28 00:08:00": null,
    "2024-04-28 00:09:00": null,
    "2024-04-28 00:10:00": null,
    "2024-04-28 00:11:00": null,
    "2024-04-28 00:12:00": null,
    "2024-04-28 00:13:00": null,
    "2024-04-28 00:14:00": null,
    "2024-04-28 00:15:00": null,
    "2024-04-28 00:16:00": null,
    "2024-04-28 00:17:00": null,
    "2024-04-28 00:18:00": null,
    "2024-04-28 00:19:00": null,
    "2024-04-28 00:20:00": null,
    "2024-04-28 00:21:00": null,
    "2024-04-28 00:22:00": null,
    "2024-04-28 00:23:00": null,
    "2024-04-28 00:24:00": null,
    "2024-04-28 00:25:00": null,
    "2024-04-28 00:26:00": null,
    "2024-04-28 00:27:00": null,
    "2024-04-28 00:28:00": null,
    "2024-04-28 00:29:00": null,
    "2024-04-28 00:30:00": null,
    "2024-04-28 00:31:00": null,
    "2024-04-28 00:32:00": null,
    "2024-04-28 00:33:00": null,
    "2024-04-28 00:34:00": null,
    "2024-04-28 00:35:00": null,
    "2024-04-28 00:36:00": null,
    "2024-04-28 00:37:00": null,
    "2024-04-28 00:38:00": null,
    "2024-04-28 00:39:00": null,
    "2024-04-28 00:40:00": null,
    "2024-04-28 00:41:00": null,
    "2024-04-28 00:42:00": null,
    "2024-04-28 00:43:00": null,
    "2024-04-28 00:44:00": null,
    "2024-04-28 00:45:00": null,
    "2024-04-28 00:46:00": null,
    "2024-04-28 00:47:00": null,
    "2024-04-28 00:48:00": null,
    "2024-04-28 00:49:00": null,
    "2024-04-28 00:50:00": null,
    "2024-04-28 00:51:00": null,
    "2024-04-28 00:52:00": null,
    "2024-04-28 00:53:00": null,
    "2024-04-28 00:54:00": null,
    "2024-04-28 00:55:00": null,
    "2024-04-28 00:56:00": null,
    "2024-04-28 00:57:00": null,
    "2024-04-28 00:58:00": null,
    "2024-04-28 00:59:00": null,
    "2024-04-28 01:00:00": null,
    "2024-04-28 01:01:00": null,
    "2024-04-28 01:02:00": "2.00",
    "2024-04-28 01:03:00": null,
    "2024-04-28 01:04:00": null
  }
},
                                    sampleRate: 1
                                })'>
                        <canvas x-ref="canvas"
                            class="h-52 ring-1 ring-gray-900/5 dark:ring-gray-100/10 bg-gray-50 dark:bg-gray-800 rounded-md shadow-sm"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </x-pulse::scroll>
</x-pulse::card>

@script
<script>
    Alpine.data('requestsChart', (data) => ({

    init() {
        var request = data.request;

        let chart = new Chart(
            this.$refs.canvas,
            {
                type: 'line',
                data: {
                    labels: this.labels(request[Object.keys(request)[0]]),
                    datasets: [
                        {
                            label: 'Server Error',
                            borderColor: '#e11d48',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.server_error),
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                        },
                        {
                            label: 'Client Error',
                            borderColor: '#eab308',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.client_error),
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                        },
                        {
                            label: 'Successful',
                            borderColor: '#9333ea',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.successful),
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                        },
                        {
                            label: 'Informational',
                            borderColor: 'rgba(29,153,172,0.5)',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.informational),
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                        },
                        {
                            label: 'Redirection',
                            borderColor: 'rgba(107,114,128,0.5)',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.redirection),
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
                            top: 3,
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

        Livewire.on('requests-chart-update', ({ request }) => {

            if (chart === undefined) {
                return
            }

            if (request === undefined && chart) {
                chart.destroy()
                chart = undefined
                return
            }

            chart.data.labels = this.labels(request[Object.keys(request)[0]])
            chart.options.scales.y.max = this.highest(request)
            chart.data.datasets[0].data = this.scale(request.server_error)
            chart.data.datasets[1].data = this.scale(request.client_error)
            chart.data.datasets[2].data = this.scale(request.successful)
            chart.data.datasets[3].data = this.scale(request.informational)
            chart.data.datasets[4].data = this.scale(request.redirection)
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
