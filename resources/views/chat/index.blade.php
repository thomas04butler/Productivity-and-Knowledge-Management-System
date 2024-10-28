<x-app-layout>

    <script>
        document.addEventListener('alpine:init', () => {
            console.log("alpine:init")

            Alpine.data('chats', () => ({
                chat: null,
                search_users: [],
                selected_users: [],
                chats: [],
                messages: [],
                chatSearch: '',
                config: {
                        headers: {
                            Authorization: `Bearer {{ auth()->user()->createToken('api_token')->plainTextToken }}`
                        }
                    },
                async init() {
                    this.chats = await this.getChats()
                    if(this.chats.data.length > 0){
                        this.chat = this.chats.data[0]
                        this.messages = await this.getMessages(this.chat)
                    }

                    setInterval(async () => {
                        if(!this.$refs.chatSearch.value.trim()){
                            // this.chats = await this.getChats()
                            const chats = await this.getChats()
                            const new_chats = chats.data.filter(c => !this.chats.data.find(c2 => c2.id === c.id))

                            if(new_chats.length > 0){
                                this.chats.data.unshift(...new_chats) // prepend new chats
                            }

                            this.chats.data = this.chats.data.map(c => {
                                const new_chat = chats.data.find(c2 => c2.id === c.id)
                                if(new_chat){
                                    return new_chat
                                }
                                return c
                            })

                            // sort by updated_at

                            this.chats.data.sort((a, b) => {
                                return new Date(b.updated_at) - new Date(a.updated_at)
                            })

                        }
                    }, 5000)

                    setInterval(async () => {
                        if(this.chat) {
                            const messages = await this.getMessages(this.chat)

                            // messages.data.forEach(m => {
                            //     const index = this.messages.data.findIndex(m2 => m2.id === m.id)
                            //     if(index !== -1){
                            //         this.messages.data[index] = m
                            //     }
                            // })

                            // for each messages that is in messages.data, if it is not in this.messages.data, then prepend it.

                            const new_messages = messages.data.filter(m => !this.messages.data.find(m2 => m2.id === m.id))

                            if(new_messages.length > 0){
                                this.messages.data.unshift(...new_messages)
                            }

                            // sort by created_at
                            this.messages.data.sort((a, b) => {
                                return new Date(b.created_at) - new Date(a.created_at)
                            })

                        }
                    }, 1500)

                },

                async getChats() {

                    const response = await axios.get('/api/chats', this.config)
                        .then(response => {
                            return response.data
                        })
                        .catch(error => {
                            console.log(error)
                        })

                    this.config.params = {}

                    return response
                },

                async getMessages(chat) {
                    return await axios.get(`/api/chats/${chat.id}/messages`, this.config)
                        .then(response => {
                            // console.log(response.data)
                            return response.data
                        })
                        .catch(error => {
                            console.log(error)
                        })
                },

                async selectChat(chat) {
                    this.chat = chat
                    this.messages = await this.getMessages(chat)
                },

                async sendMessage(target) {
                    if(target.value.trim()) {
                        // send Message via API, then get the messages again.

                        const data = await axios.post(`/api/chats/${this.chat.id}/messages`, {
                            content: target.value.trim()
                        }, this.config)

                        this.messages = await this.getMessages(this.chat)

                        target.value = ''

                    }
                },

                async paginateMessages(){
                    if(this.messages.next_page_url){
                        const response = await axios.get(this.messages.next_page_url, this.config)
                        console.log("response", response.data.data)
                        this.messages.data = this.messages.data.concat(response.data.data)
                        this.messages.next_page_url = response.data.next_page_url
                    }
                },

                async paginateChats(){
                    if(this.chats.next_page_url){
                        const response = await axios.get(this.chats.next_page_url, this.config)
                        console.log("response", response.data.data)
                        this.chats.data = this.chats.data.concat(response.data.data)
                        this.chats.next_page_url = response.data.next_page_url
                    }
                },

                async searchChats(target){
                    console.log("searchChats", target.value.trim())
                    console.log(target.value.trim())

                    if(target.value.trim() !== '') {
                        this.config.params = {
                            search: target.value.trim(),
                        };

                        try {
                            const response = await axios.get(`/api/chats`, this.config)

                            this.chats.data = response.data.data
                        } catch(error) {
                            console.log(error)
                            this.config.params = {}
                        }


                        this.config.params= {}
                    } else {
                        this.chats = await this.getChats()
                    }
                },

                async createChat(formData){

                    this.config.params = {}

                    try {

                        const response =
                            await axios.post(`/api/chats`, formData, this.config);

                        console.log(response.data)

                        const chat_data = response.data

                        await axios.post(`/api/chats/${chat_data.id}/users`, {
                            user_id: {{ auth()->id() }},
                            admin: true,
                        }, this.config)

                        for(const user of this.selected_users){
                            try {
                                await axios.post(`/api/chats/${chat_data.id}/users`, {
                                    user_id: user.id,
                                    admin: false,
                                }, this.config).then(response => {
                                    console.log(response.data)
                                }).catch(error => {
                                    console.log(error)
                                })
                            } catch(error) {
                                console.log(error)
                            }
                        }

                        const chat = await axios.get(`/api/chats/${chat_data.id}`, this.config)

                        this.chats.data.unshift(chat.data)
                        this.chat = chat.data;

                        this.$refs.create_chat_form.reset()
                        this.selected_users = []

                        this.$dispatch('close')

                    } catch (error) {
                        console.error(error)
                    }

                },

                async searchUsers(target){

                    console.log(target.value.trim())
                    if(target.value.trim() !== '') {
                        this.config.params = {
                            search: target.value.trim()
                        };

                        try {
                            const response = await axios.get(`/api/users`, this.config)
                            this.search_users = response.data.data
                        } catch (error) {
                            this.config.params = {}
                            console.error(error)
                        }

                        this.config.params = {}

                    }

                },

                async leaveChat(chat){
                    console.log('leaveChat', chat)

                    await axios.delete(`/api/chats/${chat.id}/users/{{ auth()->id() }}`, this.config)

                    this.chats.data = this.chats.data.filter(c => c.id !== chat.id)

                    if(this.chats.data.length > 0){
                        this.chat = this.chats.data[0]
                        this.messages = await this.getMessages(this.chat)
                    } else {
                        this.chat = null
                        this.messages = []
                    }
                },

                async deleteChat(chat){
                    console.log('deleteChat', chat)

                    await axios.delete(`/api/chats/${chat.id}`, this.config)

                    this.chats.data = this.chats.data.filter(c => c.id !== chat.id)

                    if(this.chats.data.length > 0){
                        this.chat = this.chats.data[0]
                        this.messages = await this.getMessages(this.chat)
                    } else {
                        this.chat = null
                        this.messages = []
                    }
                },

                async toggleAdmin(chat, user){
                    console.log('toggleAdmin', chat, user)

                    await axios.put(`/api/chats/${chat.id}/users/${user.id}`, {
                        admin: !user.pivot.is_admin
                    }, this.config)

                    const chat_data = await axios.get(`/api/chats/${chat.id}`, this.config)

                    this.chat = chat_data.data
                },

                async deleteChatUser(chat, user){
                    console.log('deleteChatUser', chat, user)

                    await axios.delete(`/api/chats/${chat.id}/users/${user.id}`, this.config)

                    const chat_data = await axios.get(`/api/chats/${chat.id}`, this.config)

                    this.chat = chat_data.data

                },

                addUser(event) {
                    console.log('addUser', event.target.value)
                    console.log(event)
                    if(event.inputType === 'insertReplacementText' || event.inputType == undefined) {
                        const user = this.search_users.find(u => u.id.toString() == event.target.value)
                        this.selected_users.push(user)
                        console.log('selected_users', this.selected_users)
                        event.target.value = ''
                    }
                },

                async createChatUsers(chat){
                    console.log('createChatUsers', chat)

                    for(const user of this.selected_users){
                        await axios.post(`/api/chats/${chat.id}/users`, {
                            user_id: user.id,
                            admin: false,
                        }, this.config).then(response => {
                            console.log(response.data)
                        }).catch(error => {
                            console.log(error)
                        })
                    }

                    const chat_data = await axios.get(`/api/chats/${chat.id}`, this.config)

                    this.chat = chat_data.data

                    this.selected_users = []

                },

                async deleteMessage(message){
                    console.log('deleteMessage', message)

                    await axios.delete(`/api/chats/${this.chat.id}/messages/${message.id}`, this.config)

                    this.messages.data = this.messages.data.filter(m => m.id !== message.id)

                },

                async editMessage(message, formData){
                    console.log('editMessage', message)

                    await axios.put(`/api/chats/${message.chat_id}/messages/${message.id}`, formData, this.config)

                    this.messages = await this.getMessages(this.chat)

                    this.$dispatch('close')
                },

            }))

        })
    </script>

	<!-- component -->
	<!-- This is an example component -->
	<div class="shadow-lg rounded-lg" x-data="chats">
		<div class="h-[calc(100vh-64px)] flex flex-row justify-between bg-white dark:bg-gray-900">
			<!-- chat list -->
			<div class="flex flex-col w-1/4 border-r-2 dark:border-gray-800">
				<!-- search compt -->
				<div class="border-b-2 dark:border-gray-800 py-4 px-2">
					<!-- <input type="text" placeholder="Search chats" class="py-2 px-2 border-2 border-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-700 rounded-2xl w-full" /> -->
                    <div class="flex items-center max-w-lg mx-auto">
                        <label for="chat-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <input @input.debounce.250ms="searchChats($event.target)" x-ref="chatSearch" type="text" id="chat-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Chats" />
                        </div>
                        <button @click="$dispatch('open-modal', 'createChatModal')" type="submit" class="inline-flex items-center py-2.5 px-3 ms-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>

                        <x-modal name="createChatModal" title="Create Chat">

                            <form method="post" class="p-6" @submit.prevent="createChat($formData)" x-ref="create_chat_form">
                                @csrf
                                @method('post')

                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Create Chat')}}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Fill in the form below to create a new chat.') }}
                                </p>

                                <div class="mt-6">
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
                                </div>

                                <div class="mt-6">
                                    <x-input-label for="description" :value="__('Description')" />
                                    <x-text-input id="description" class="block mt-1 w-full" name="description" required />
                                </div>

                                <!-- A text input where that reacts to users searches. Places chip component inside text input when user is selected from dropdown list. Like how gmail does it -->
                                <div class="mt-6">
                                    <x-input-label for="users" :value="__('Users (select name from list)')" />
                                    <x-text-input ignore id="users" list="users_datalist" @input.debounce="searchUsers($event.target)" @input="addUser($event)" @keydown.enter.prevent="" class="block mt-1 w-full" type="text" />

                                    <datalist id="users_datalist" @change="console.log('change')">
                                        <template x-for="user in search_users" :key="user.id">
                                            <option x-text="user.name" :value="user.id" />
                                        </template>
                                    </datalist>

                                    <!-- user -->
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 me-2 text-sm font-medium text-blue-800 bg-blue-100 rounded dark:bg-blue-900 dark:text-blue-300">
                                            <span x-text="'{{ auth()->user()->name }}'"></span>
                                        </span>
                                        <template x-for="(s, index) in selected_users">
                                            <span class="inline-flex space-1 my-1 items-center px-2 py-1 me-2 text-sm font-medium text-blue-800 bg-blue-100 rounded dark:bg-blue-900 dark:text-blue-300">
                                                <span x-text="s.name"></span>
                                                <button type="button" class="inline-flex items-center p-1 ms-2 text-sm text-blue-400 bg-transparent rounded-sm hover:bg-blue-200 hover:text-blue-900 dark:hover:bg-blue-800 dark:hover:text-blue-300" @click="selected_users = selected_users.filter(e => e !== s); console.log(selected_users)" aria-label="Remove">
                                                    <svg class="w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                    </svg>
                                                    <span class="sr-only">Remove badge</span>
                                                </button>
                                            </span>
                                            <!-- Hidden input element -->
                                        </template>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>

                                    <x-primary-button class="ms-3">
                                        {{ __('Create Chat') }}
                                    </x-primary-button>

                                </div>
                            </form>
                        </x-modal>

                    </div>
                </div>



				<!-- end search compt -->
				<!-- user list -->
                <div class="overflow-y-auto">
                    <template x-for="(chat_data, index) in chats.data" :key="chat_data.id">
                        <div
                            class="flex flex-row cursor-pointer py-4 px-2 justify-start items-center border-b-2 dark:border-gray-800"
                            :class="chat.id === chat_data.id ? 'bg-gray-100 dark:bg-gray-800' : 'bg-white dark:bg-gray-900 hover:bg-gray-100 hover:dark:bg-gray-800 '"
                            @click="selectChat(chat_data)"
                        >
				        	<div class="mx-4">
                                <div class="w-full flex items-center justify-center">
                                    <div class="relative inline-flex items-center justify-center w-12 h-12 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                        <span class="font-medium text-gray-600 dark:text-gray-300" x-text="chat_data.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                </div>
				        	</div>
				        	<div class="hidden sm:block">
				        		<div class="text-lg font-semibold dark:text-white">
                                    <span x-text="chat_data.name"></span>
                                </div>
				        		<span class="text-gray-500" x-text="new Date(chat_data.updated_at).toDateString()"></span>
				        	</div>
				        </div>
                    </template>
                    <div class="h-1" x-intersect="paginateChats()">
                        <!-- This is a blank div that triggers getting more paginated data -->
                    </div>
                </div>

			</div>
			<!-- end chat list -->
			<!-- message -->

            <div class="flex flex-col w-full justify-between">

                <!-- Chat header with more refined style -->
                <div class="flex justify-between border-b-2 dark:border-gray-800 py-4 px-2 items-center">
                    <div class="flex flex-row">
                        <div class="mx-4 justify-start">
                            <div class="w-full flex items-center justify-center">
                                <div class="relative inline-flex items-center justify-center w-12 h-12 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                    <span class="font-medium text-gray-600 dark:text-gray-300" x-text="chat?.name.charAt(0).toUpperCase()"></span>
                                </div>
                            </div>
                        </div>
                        <div class="justify-start">
                            <div class="text-lg font-semibold dark:text-white">
                                <span x-text="chat?.name"></span>
                            </div>
                            <span class="text-gray-500" x-text="new Date(chat?.updated_at).toDateString()"></span>
                        </div>
                    </div>

                    <!-- Open sidebar button maybe use 3 dot icon -->
                    <div class="flex flex-row px-4" x-data="{ isSidebarOpen: false }">
                        <button type="button" @click="isSidebarOpen = true" class="inline-flex items-center justify-center rounded-full h-8 w-8 transition duration-200 ease-in-out text-white bg-blue-500 hover:bg-blue-600 dark:bg-gray-800 dark:hover:bg-gray-700 focus:outline-none">
                            <i class="text-xl leading-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                </svg>
                            </i>
                        </button>

                        <!-- Toggleable sidebar hidden with x-cloak and animated using x-transition and x-show -->
                        <div class="fixed inset-0 z-50 bg-black bg-opacity-50 text-white" x-show="isSidebarOpen" x-transtion x-cloak @click="isSidebarOpen = false"></div>
                        <div class="fixed inset-y-0 right-0 z-50 w-96 bg-white dark:bg-gray-900 shadow-lg p-4 overflow-y-auto" x-show="isSidebarOpen" x-transition x-cloak @click.away="isSidebarOpen = false">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold dark:text-white">Chat Info</h2>
                                <button type="button" @click="isSidebarOpen = false" class="inline-flex items-center justify-center rounded-full h-8 w-8 transition duration-200 ease-in-out text-white bg-blue-500 hover:bg-blue-600 dark:bg-gray-800 dark:hover:bg-gray-700 focus:outline-none">
                                    <i class="text-xl leading-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </i>
                                </button>
                            </div>
                            <div class="mt-4">
                                <div class="flex flex-row">
                                    <div class="mx-4 justify-start">
                                        <div class="w-full flex items-center justify-center">
                                            <div class="relative inline-flex items-center justify-center w-12 h-12 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                                <span class="font-medium text-gray-600 dark:text-gray-300" x-text='chat?.name.charAt(0).toUpperCase()'></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="justify-start">
                                        <div class="text-lg font-semibold dark:text-white">
                                            <span x-text="chat?.name"></span>
                                        </div>
                                        <span class="text-gray-500" x-text="new Date(chat?.updated_at).toDateString()"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex flex-col text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-bold text-lg mb-1">Description</span>
                                    <span x-text="chat?.description"></span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="flex flex-col text-sm text-gray-500 dark:text-gray-400">


                                    <span class="font-bold text-lg mb-1">Users</span>

                                    <template x-for="user in chat?.users" :key="user.id">
                                        <div class="flex justify-between items-center m-2">
                                            <span x-text="user.name"></span>
                                            <div class="flex items-center space-x-2">

                                                <!-- If the current user is an admin and the user is not the current user, and the other user is ad admin, then we can demote them-->
                                                <template x-if="chat?.users.find(u => u.id === {{ auth()->id() }}).pivot.is_admin && user.id !== {{ auth()->id() }} && user.pivot.is_admin">
                                                    <button type="button"
                                                        class="inline-flex items-center justify-center rounded-full h-6 w-6 transition duration-200 ease-in-out text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none"
                                                        @click="toggleAdmin(chat, user)"
                                                    >
                                                        <i class="text-xl leading-none">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                                                            </svg>
                                                        </i>
                                                    </button>
                                                </template>

                                                <!-- If the current user is an admin and the user is not the current user. We can promote a user. -->
                                                <template x-if="chat?.users.find(u => u.id === {{ auth()->id() }}).pivot.is_admin && !user.pivot.is_admin && user.id !== {{ auth()->id() }}">
                                                    <button type="button"
                                                        class="inline-flex items-center justify-center rounded-full h-6 w-6 transition duration-200 ease-in-out text-white bg-blue-500 hover:bg-blue-600 focus:outline-none"
                                                        @click="toggleAdmin(chat, user)"
                                                    >
                                                        <i class="text-xl leading-none">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 18.75 7.5-7.5 7.5 7.5" />
                                                              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 7.5-7.5 7.5 7.5" />
                                                            </svg>
                                                        </i>
                                                    </button>
                                                </template>

                                                <!-- If the current user is an admin and the user is not the current user. We can remove a user. -->
                                                <template x-if="chat?.users.find(u => u.id === {{ auth()->id() }}).pivot.is_admin  && user.id !== {{ auth()->id() }}">
                                                    <button type="button"
                                                        class="inline-flex items-center justify-center rounded-full h-6 w-6 transition duration-200 ease-in-out text-white bg-red-500 hover:bg-red-600 focus:outline-none"
                                                        @click="deleteChatUser(chat, user)"
                                                    >
                                                        <i class="text-xl leading-none">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                              <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                        </i>
                                                    </button>
                                                </template>

                                            </div>
                                        </div>
                                    </template>

                                    <!-- Can only add users if they are an admin -->
                                    <template x-if="chat?.users.find(u => u.id === {{ auth()->id() }}).pivot.is_admin">
                                        <!-- Quick form to add users -->
                                        <form class="my-8" @submit.prevent="createChatUsers(chat)">
                                            <x-text-input id="users" list="users_datalist" @input.debounce="searchUsers($event.target)" @input="addUser($event)" @keydown.enter.prevent="" class="block mt-1 w-full" placeholder="Add users" type="text" />

                                            <datalist id="users_datalist">
                                                <template x-for="user in search_users" :key="user.id">
                                                    <option x-text="user.name" :value="user.id" />
                                                </template>
                                            </datalist>

                                            <!-- user -->
                                            <div class="mt-2">
                                                <template x-for="(s, index) in selected_users">
                                                    <span class="inline-flex space-1 my-1 items-center px-2 py-1 me-2 text-sm font-medium text-blue-800 bg-blue-100 rounded dark:bg-blue-900 dark:text-blue-300">
                                                        <span x-text="s.name"></span>
                                                        <button
                                                            type="button"
                                                            class="inline-flex items-center p-1 ms-2 text-sm text-blue-400 bg-transparent rounded-sm hover:bg-blue-200 hover:text-blue-900 dark:hover:bg-blue-800 dark:hover:text-blue-300"
                                                            @click="selected_users = selected_users.filter(e => e !== s); console.log(selected_users)"
                                                            aria-label="Remove"
                                                        >
                                                            <svg class="w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                            </svg>
                                                            <span class="sr-only">Remove badge</span>
                                                        </button>
                                                    </span>
                                                    <!-- Hidden input element -->
                                                </template>
                                            </div>

                                            <!-- Full width button saying add users -->
                                            <div class="mt-2">
                                                <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg h-10 transition duration-200 ease-in-out text-white bg-blue-700 hover:bg-blue-800 focus:outline-none">
                                                    Add Users
                                                </button>
                                            </div>

                                        </form>
                                    </template>

                                </div>
                            </div>


                            <!-- Leave gc button takes up whole width only if the user is not an admin -->
                            <div class="mt-4">
                                <button type="button"
                                    class="w-full inline-flex items-center justify-center rounded-lg h-10 transition duration-200 ease-in-out text-white bg-red-500 hover:bg-red-600 focus:outline-none"
                                    x-show="!chat?.users.find(u => u.id === {{ auth()->id() }}).pivot.is_admin"
                                    @click="leaveChat(chat); isSidebarOpen = false"
                                >
                                    Leave Chat
                                </button>
                            </div>

                            <!-- If a user is an admin then they can delete a chat -->
                            <div class="mt-4">
                                <button type="button"
                                    class="w-full inline-flex items-center justify-center rounded-lg h-10 transition duration-200 ease-in-out text-white bg-red-500 hover:bg-red-600 focus:outline-none"
                                    x-show="chat?.users.find(u => { return u.id === {{ auth()->id() }} }).pivot.is_admin"
                                    @click="deleteChat(chat); isSidebarOpen = false"
                                >
                                    Delete Chat
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

			    <div class="w-full px-5 flex flex-col justify-between overflow-y-auto">

			    	<div class="flex flex-col-reverse mt-5 overflow-y-auto">

                        <template x-for="message in messages.data" >
                            <div class="flex flex-col-reverse" style="overflow-anchor: auto;" >

                                <!-- If the current user sent that message  -->
                                <template x-if="message.user_id == {{ auth()->id() }}">
                                    <div class="flex justify-end items-center mb-4 group">


                                        <!-- Edit and delete buttons. They are invisible until parent hover. They they appear. Like facebook messenger. -->
                                        <div class="hidden group-hover:block space-4 mx-4">
                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center rounded-full h-8 w-8 transition duration-200 ease-in-out text-black dark:text-white dark:hover:text-red-500 hover:text-red-500 focus:outline-none"
                                                @click="deleteMessage(message)"
                                            >
                                                <i class="text-xl leading-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </i>
                                            </button>

                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center rounded-full h-8 w-8 transition duration-200 ease-in-out text-black dark:text-white dark:hover:text-blue-600 hover:text-blue-600 focus:outline-none"
                                                @click="$dispatch('open-modal', 'editMessageModal-' + message.id)"
                                            >
                                                <i class="text-xl leading-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                </i>
                                            </button>

                                        </div>

                                        <div
                                            x-data="{
                                                show: false,
                                                focusables() {
                                                    // All focusable element types...
                                                    let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
                                                    return [...$el.querySelectorAll(selector)]
                                                        // All non-disabled elements...
                                                        .filter(el => ! el.hasAttribute('disabled'))
                                                },
                                                firstFocusable() { return this.focusables()[0] },
                                                lastFocusable() { return this.focusables().slice(-1)[0] },
                                                nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
                                                prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
                                                nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
                                                prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
                                            }"
                                            x-init="$watch('show', value => {
                                                if (value) {
                                                    document.body.classList.add('overflow-y-hidden');
                                                } else {
                                                    document.body.classList.remove('overflow-y-hidden');
                                                }
                                            })"
                                            x-on:open-modal.window="$event.detail == 'editMessageModal-' + message.id ? show = true : null"
                                            x-on:close-modal.window="$event.detail == 'editMessageModal-' + message.id ? show = false : null"
                                            x-on:close.stop="show = false"
                                            x-on:keydown.escape.window="show = false"
                                            x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
                                            x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
                                            x-show="show"
                                            class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
                                            style="display: 'block';"
                                        >
                                            <div
                                                x-show="show"
                                                class="fixed inset-0 transform transition-all"
                                                x-on:click="show = false"
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                            >
                                                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                                            </div>

                                            <div
                                                x-show="show"
                                                class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto"
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            >
                                                <form method="post" class="p-6" @submit.prevent="editMessage(message, $formData)" x-ref="create_chat_form">
                                                    @csrf
                                                    @method('post')

                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                        {{ __('Edit message')}}
                                                    </h2>

                                                    <div class="mt-6">
                                                        <x-input-label for="content" :value="__('Content')" />
                                                        <x-text-input id="content" class="block mt-1 w-full" type="text" name="content" x-bind:value="message.content" required autofocus />
                                                    </div>

                                                    <div class="mt-6 flex justify-end">
                                                        <x-secondary-button x-on:click="$dispatch('close')">
                                                            {{ __('Cancel') }}
                                                        </x-secondary-button>

                                                        <x-primary-button class="ms-3">
                                                            {{ __('Edit Message') }}
                                                        </x-primary-button>

                                                    </div>
                                                </form>

                                            </div>
                                        </div>

                                        <!-- Message content  -->

                                        <div class="mr-2 py-3 px-4 bg-blue-400 rounded-bl-3xl rounded-tl-3xl rounded-tr-xl text-white">
                                            <span x-text="message.content"></span>
                                        </div>

                                        <div class="relative inline-flex items-center justify-center w-8 h-8 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                            <span class="font-medium text-gray-600 dark:text-gray-300" x-data="{ name: '{{ auth()->user()->name }}' }" x-text='name.charAt(0).toUpperCase()'></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- If the current user did not send that message  -->
                                <template x-if="message.user_id != {{ auth()->id() }}">
                                    <div class="flex justify-start items-center mb-4">
                                        <!-- Message Icon -->

                                        <div class="relative inline-flex items-center justify-center w-8 h-8 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                            <span class="font-medium text-gray-600 dark:text-gray-300" x-text="message.user.name.charAt(0).toUpperCase()"></span>
                                        </div>

                                        <!-- Message content  -->
                                        <div class="flex flex-col">
                                            <div class="mx-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span x-text="message.user.name"></span>
                                            </div>
                                            <div class="ml-2 py-3 px-4 bg-gray-400 rounded-br-3xl rounded-tr-3xl rounded-tl-xl text-white">
                                                <span x-text="message.content"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="" x-intersect="paginateMessages()">
                            <!-- This is a blank div that triggers getting more paginated data -->
                        </div>

			    	</div>

			    	<div class="py-5">
                        <div class="relative flex">
                            <input type="text" placeholder="Aa" autocomplete="off" autofocus="true" @keydown.enter="sendMessage($event.target)"
                                class="text-md w-full focus:outline-none focus:placeholder-gray-400 text-gray-600 dark:text-white placeholder-gray-600 pl-5 pr-16 bg-gray-100 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 focus:border-blue-500 rounded-full py-2" x-ref="input" />
                            <div class="absolute right-2 items-center inset-y-0 hidden sm:flex">
                                <button type="button" class="inline-flex items-center justify-center rounded-full h-8 w-8 transition duration-200 ease-in-out text-white bg-blue-500 hover:bg-blue-600 focus:outline-none" @click.prevent="sendMessage($refs.input)">
                                    <i class="text-xl leading-none">
                                        <svg
                                           class="size-5 rtl:rotate-180"
                                           xmlns="http://www.w3.org/2000/svg"
                                           fill="none"
                                           viewBox="0 0 24 24"
                                           stroke="currentColor"
                                         >
                                           <path
                                             stroke-linecap="round"
                                             stroke-linejoin="round"
                                             stroke-width="2"
                                             d="M17 8l4 4m0 0l-4 4m4-4H3"
                                           />
                                         </svg>
                                    </i>
                                </button>
                            </div>
                        </div>
			    	</div>
			    </div>
            </div>
		</div>
	</div>

</x-app-layout>
