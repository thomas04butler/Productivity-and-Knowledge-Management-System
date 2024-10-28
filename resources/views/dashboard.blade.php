<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3 mt-4">
                <a href='{{ route("analytics") }}' class="flex flex-col rounded-lg bg-gray-200 dark:bg-gray-800 px-4 py-8 text-center">
                    <dt class="order-last text-lg font-medium text-gray-500 dark:text-gray-300">
                        <div class="flex flex-col justify-center content-center mt-4">
                            <span>View Analytics for projects and tasks</span>
                        </div>
                    </dt>

                    <dd class="text-4xl font-extrabold text-indigo-400 dark:text-indigo-600 md:text-5xl flex justify-center content-center">
                        Analytics
                    </dd>
                </a>

                <a href='{{ route("chat") }}' class="flex flex-col rounded-lg bg-gray-200 dark:bg-gray-800 px-4 py-8 text-center">
                    <dt class="order-last text-lg font-medium text-gray-500 dark:text-gray-300">
                        <div class="flex flex-col justify-center content-center mt-4">
                            <span>Send and make chats</span>
                        </div>
                    </dt>

                    <dd class="text-4xl font-extrabold text-indigo-400 dark:text-indigo-600 md:text-5xl flex justify-center content-center">
                        Chat
                    </dd>
                </a>

                <a href='/docs' class="flex flex-col rounded-lg bg-gray-200 dark:bg-gray-800 px-4 py-8 text-center">
                    <dt class="order-last text-lg font-medium text-gray-500 dark:text-gray-300">
                        <div class="flex flex-col justify-center content-center mt-4">
                            <span>Access documentation</span>
                        </div>
                    </dt>

                    <dd class="text-4xl font-extrabold text-indigo-400 dark:text-indigo-600 md:text-5xl flex justify-center content-center">
                        Docs
                    </dd>
                </a>

            </dl>
        </div>
    </div>

</x-app-layout>
