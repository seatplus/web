<template>
    <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
        <li :key="role.id" v-for="role in this.roles" class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow">
            <div class="flex-1 flex flex-col p-8">
                <Avatar :name="role.name"></Avatar>
                <h3 class="mt-6 text-gray-900 text-sm leading-5 font-medium">{{ role.name }}</h3>
                <dl class="mt-1 flex-grow flex flex-col justify-between">
                    <dd class="text-gray-500 text-sm leading-5">{{ role.members }} {{ role.members > 1 ? 'Members' : 'Member'}} </dd>
                    <dd class="mt-3">
                        <span :class="[isPausedOrWaitlist(role) ? 'bg-yellow-100 text-yellow-800': 'bg-teal-100 text-teal-800','px-2 py-1  text-xs leading-4 font-medium  rounded-full']">{{ role.status ? role.status : role.type }}</span>
                    </dd>
                </dl>
            </div>
            <div class="border-t border-gray-200">
                <div class="-mt-px flex">
                    <div v-if="isJoinable(role)" class="w-0 flex-1 flex border-r border-gray-200">
                        <button @click="join(role)" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                            </svg>
                            <span class="ml-3">Join</span>
                        </button>
                    </div>
                    <div v-if="isLeavable(role)" class="w-0 flex-1 flex border-r border-gray-200">
                        <button @click="leave(role)" class="relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 6a3 3 0 11-6 0 3 3 0 016 0zM14 17a6 6 0 00-12 0h12zM13 8a1 1 0 100 2h4a1 1 0 100-2h-4z"></path>
                            </svg>
                            <span class="ml-3">Leave</span>
                        </button>
                    </div>
                    <div v-if="canModerate(role)" class="-ml-px w-0 flex-1 flex border-r border-gray-200">
                        <inertia-link :href="$route('manage.acl.members', role.id)"  class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                            </svg>
                            <svg fill="currentColor" viewBox="0 0 20 20"></svg>
                            <span class="ml-3">Moderate</span>
                        </inertia-link>
                    </div>
                    <div v-if="role.can_edit" class="-ml-px w-0 flex-1 flex border-r border-gray-200">
                        <inertia-link :href="$route('acl.edit', role.id)" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"/>
                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-3">Edit</span>
                        </inertia-link>
                    </div>
                    <div v-if="role.can_edit" class="-ml-px w-0 flex-1 flex">
                        <inertia-link :href="$route('acl.manage', role.id)" class="relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm leading-5 text-gray-700 font-medium border border-transparent rounded-br-lg hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                            </svg>
                            <span class="ml-3">Manage</span>
                        </inertia-link>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</template>

<script>

    import Avatar from "@/Shared/Avatar"
    export default {
        name: "ControlGroups",
        components: {Avatar},
        props: {
            roles: {
                required: true
            }
        },
        methods: {
            isJoinable(role) {
                return (['on-request', 'opt-in'].indexOf(role.type) > -1) && !this.isLeavable(role)
            },
            isLeavable(role) {
                return !!role.status
            },
            canModerate(role) {
                return role.can_moderate && !role.can_edit
            },
            join(role) {
                let data = {
                    role_id: role.id
                };

                this.$inertia.post(this.$route('acl.join'), data, {
                    replace: false,
                    preserveState: false,
                    preserveScroll: false,
                    only: [],
                })
            },
            isPausedOrWaitlist(role) {
                return ['paused', 'waitlist'].indexOf(role.status) > -1
            },
            leave(role) {

                let user_id = this.$page.props.user.data.id;

                return this.$inertia.delete(this.$route('acl.leave', {role_id: role.id, user_id: user_id}), {
                    replace: false,
                    preserveState: false,
                    preserveScroll: false,
                    only: [],
                })
            },
            moderate(role) {

            }
        }
    }
</script>

<style scoped>

</style>
