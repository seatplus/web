<template>
    <Layout page="Access Control" page-description="Manage Members">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <!--Header-->
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <!-- Content goes here -->
                <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-no-wrap">
                    <div class="ml-4 mt-2">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Manage Members {{this.role.name}}
                        </h3>
                    </div>
                </div>
                <!-- We use less vertical padding on card headers on desktop than on body sections -->
            </div>

            <!--Content below-->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    <li :key="member.id" v-for="member in sortedMembers">
                        <div class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="inline-flex items-center space-x-3">
                                            <EveImage :object="member.main_character" :size="256" tailwind_class="h-8 w-8 rounded-full" />
                                            <div class="text-sm leading-5 font-medium text-indigo-600 truncate">
                                                {{member.main_character.name}}
                                            </div>
                                            <span :class="[{'bg-green-100 text-green-800': member.status === 'member', 'bg-blue-100 text-blue-800': member.status === 'waitlist', 'bg-yellow-100 text-yellow-800': member.status === 'paused'},'px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize']">
                                                {{member.status}}
                                            </span>
                                        </div>
                                        <div v-if="member.characters.length > 0" class="truncate">
                                            <AvatarGroupTopToBottom :objects="member.characters" :size="256"/>
                                        </div>
                                    </div>

                                    <div class="ml-2 flex-shrink-0 flex">
                                        <span v-if="member.status === 'waitlist'" class="relative z-0 inline-flex shadow-sm rounded-md">
                                            <button @click="approve(member)" type="button" class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Approve
                                            </button>
                                            <button @click="removeMember(member)" type="button" class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                Deny
                                            </button>
                                        </span>
                                        <span v-if="member.status === 'member'" class="inline-flex rounded-md shadow-sm">
                                            <button @click="removeMember(member)" type="button" class="inline-flex items-center px-3 py-2 border border-gray-300t text-sm leading-4 font-medium rounded-md bg-white hover:text-gray-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:text-gray-700 active:bg-gray-100 transition ease-in-out duration-150">
                                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                Kick member
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>


            <!--<ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">



                <li class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow">
                    <div class="flex-1 flex flex-col p-8">
                        <img class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" src="https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">
                        <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">Cody Fisher</h3>
                        <dl class="mt-1 flex-grow flex flex-col justify-between">
                            <dt class="sr-only">Title</dt>
                            <dd class="text-gray-500 text-sm leading-5">Lead Security Associate</dd>
                            <dt class="sr-only">Role</dt>
                            <dd class="mt-3">
                                <span class="px-2 py-1 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Admin</span>
                            </dd>
                        </dl>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="-mt-px flex">
                            <div class="w-0 flex-1 flex border-r border-gray-200">
                                <a href="#" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="ml-3">Email</span>
                                </a>
                            </div>
                            <div class="-ml-px w-0 flex-1 flex">
                                <a href="#" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="ml-3">Call</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow">
                    <div class="flex-1 flex flex-col p-8">
                        <img class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" src="https://images.unsplash.com/photo-1520813792240-56fc4a3765a7?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">
                        <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">Esther Howard</h3>
                        <dl class="mt-1 flex-grow flex flex-col justify-between">
                            <dt class="sr-only">Title</dt>
                            <dd class="text-gray-500 text-sm leading-5">Assurance Administrator</dd>
                            <dt class="sr-only">Role</dt>
                            <dd class="mt-3">
                                <span class="px-2 py-1 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Admin</span>
                            </dd>
                        </dl>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="-mt-px flex">
                            <div class="w-0 flex-1 flex border-r border-gray-200">
                                <a href="#" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="ml-3">Email</span>
                                </a>
                            </div>
                            <div class="-ml-px w-0 flex-1 flex">
                                <a href="#" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="ml-3">Call</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow">
                    <div class="flex-1 flex flex-col p-8">
                        <img class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" src="https://images.unsplash.com/photo-1498551172505-8ee7ad69f235?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">
                        <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">Jenny Wilson</h3>
                        <dl class="mt-1 flex-grow flex flex-col justify-between">
                            <dt class="sr-only">Title</dt>
                            <dd class="text-gray-500 text-sm leading-5">Chief Accountability Analyst</dd>
                            <dt class="sr-only">Role</dt>
                            <dd class="mt-3">
                                <span class="px-2 py-1 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Admin</span>
                            </dd>
                        </dl>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="-mt-px flex">
                            <div class="w-0 flex-1 flex border-r border-gray-200">
                                <a href="#" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="ml-3">Email</span>
                                </a>
                            </div>
                            <div class="-ml-px w-0 flex-1 flex">
                                <a href="#" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="ml-3">Call</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow">
                    <div class="flex-1 flex flex-col p-8">
                        <img class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" src="https://images.unsplash.com/photo-1532417344469-368f9ae6d187?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">
                        <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">Kristin Watson</h3>
                        <dl class="mt-1 flex-grow flex flex-col justify-between">
                            <dt class="sr-only">Title</dt>
                            <dd class="text-gray-500 text-sm leading-5">Investor Data Orchestrator</dd>
                            <dt class="sr-only">Role</dt>
                            <dd class="mt-3">
                                <span class="px-2 py-1 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Admin</span>
                            </dd>
                        </dl>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="-mt-px flex">
                            <div class="w-0 flex-1 flex border-r border-gray-200">
                                <a href="#" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="ml-3">Email</span>
                                </a>
                            </div>
                            <div class="-ml-px w-0 flex-1 flex">
                                <a href="#" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="ml-3">Call</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow">
                    <div class="flex-1 flex flex-col p-8">
                        <img class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" src="https://images.unsplash.com/photo-1566492031773-4f4e44671857?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">
                        <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">Cameron Williamson</h3>
                        <dl class="mt-1 flex-grow flex flex-col justify-between">
                            <dt class="sr-only">Title</dt>
                            <dd class="text-gray-500 text-sm leading-5">Product Infrastructure Executive</dd>
                            <dt class="sr-only">Role</dt>
                            <dd class="mt-3">
                                <span class="px-2 py-1 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Admin</span>
                            </dd>
                        </dl>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="-mt-px flex">
                            <div class="w-0 flex-1 flex border-r border-gray-200">
                                <a href="#" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="ml-3">Email</span>
                                </a>
                            </div>
                            <div class="-ml-px w-0 flex-1 flex">
                                <a href="#" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="ml-3">Call</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow">
                    <div class="flex-1 flex flex-col p-8">
                        <img class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" src="https://images.unsplash.com/photo-1534751516642-a1af1ef26a56?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">
                        <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">Courtney Henry</h3>
                        <dl class="mt-1 flex-grow flex flex-col justify-between">
                            <dt class="sr-only">Title</dt>
                            <dd class="text-gray-500 text-sm leading-5">Investor Factors Associate</dd>
                            <dt class="sr-only">Role</dt>
                            <dd class="mt-3">
                                <span class="px-2 py-1 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Admin</span>
                            </dd>
                        </dl>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="-mt-px flex">
                            <div class="w-0 flex-1 flex border-r border-gray-200">
                                <a href="#" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="ml-3">Email</span>
                                </a>
                            </div>
                            <div class="-ml-px w-0 flex-1 flex">
                                <a href="#" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="ml-3">Call</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow">
                    <div class="flex-1 flex flex-col p-8">
                        <img class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" src="https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">
                        <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">Theresa Webb</h3>
                        <dl class="mt-1 flex-grow flex flex-col justify-between">
                            <dt class="sr-only">Title</dt>
                            <dd class="text-gray-500 text-sm leading-5">Global Division Officer</dd>
                            <dt class="sr-only">Role</dt>
                            <dd class="mt-3">
                                <span class="px-2 py-1 text-teal-800 text-xs leading-4 font-medium bg-teal-100 rounded-full">Admin</span>
                            </dd>
                        </dl>
                    </div>
                    <div class="border-t border-gray-200">
                        <div class="-mt-px flex">
                            <div class="w-0 flex-1 flex border-r border-gray-200">
                                <a href="#" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="ml-3">Email</span>
                                </a>
                            </div>
                            <div class="-ml-px w-0 flex-1 flex">
                                <a href="#" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="ml-3">Call</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>-->

        </div>
    </Layout>

</template>

<script>
    import Layout from "../../Shared/Layout"
    import InfiniteLoading from "vue-infinite-loading"
    import EveImage from "../../Shared/EveImage"
    import AvatarGroupTopToBottom from "../../Shared/AvatarGroupTopToBottom"
    export default {
        name: "ManageMembers",
        components: {AvatarGroupTopToBottom, EveImage, Layout, InfiniteLoading},
        props: {
            role: {
                required: true,
                type: Object
            }
        },
        data()  {
            return {
                membersList: [],
                page: 1
            }
        },
        methods: {
            getMembers() {
                axios.get(this.$route('acl.members', this.role.id), {
                    params: {
                        page: this.page,
                    },
                }).then(({ data }) => {
                    if (data.data.length) {
                        this.page += 1;
                        this.membersList.push(...data.data);
                        if(data.next_page_url)
                            this.getMembers()
                    }
                });
            },
            approve(member) {

                let data = {
                    user_id: member.id,
                    role_id: this.role.id
                };

                this.$inertia.post(this.$route('acl.join'), data, {
                    replace: false,
                    preserveState: false,
                    preserveScroll: false,
                    only: [],
                })
            },
            removeMember(member) {

                this.$inertia.delete(this.$route('acl.leave', { role_id: this.role.id, user_id: member.id}), {
                    replace: false,
                    preserveState: false,
                    preserveScroll: false,
                    only: [],
                })
            },
        },
        mounted() {
            this.getMembers();
        },
        computed: {
            cleanMembers() {
                return _.map(this.membersList, function (member) {
                    member.characters = _.reject(member.characters, (character) => character.character_id === member.main_character.character_id)
                    return member
                })
            },
            applicants() {
                return _.filter(this.cleanMembers, (member) => member.status === 'waitlist')
            },
            pausedMembers() {
                return _.filter(this.cleanMembers, (member) => member.status === 'paused')
            },
            members() {
                return _.filter(this.cleanMembers, (member) => member.status === 'member')
            },
            sortedMembers() {
                return [...this.applicants, ...this.pausedMembers, ...this.members]
            }
        }
    }
</script>

<style scoped>

</style>
