<template>
  <nav class="md:flex-1 px-2 py-4 md:bg-gray-800">
    <div v-for="(category, name, index) in sidebarEntries">
      <h3 :class="['text-xs leading-4 font-semibold text-white uppercase tracking-wider',{'mt-3' : index > 0}]">
        {{ category.name }}
      </h3>
      <Link
        v-for="(entry, index) in category.entries"
        :key="entry.name"
        :href="route(entry.route)"
        :class="[{'mt-1': index > 0, 'text-white bg-gray-900': isActive(entry.route), 'text-gray-300 hover:text-white hover:bg-gray-700 focus:text-white': !isActive(entry.route)},'group flex items-center px-2 py-2 md:text-sm text-base leading-5 md:leading-6 font-medium rounded-md focus:outline-none focus:bg-gray-700 transition ease-in-out duration-150']"
      >
        <svg
          v-if="entry.content"
          class="mr-3 h-6 w-6 text-gray-300 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
          stroke="currentColor"
          fill="none"
          :view-box="entry.viewbox"
          v-html="entry.content"
        />
        <component
          :is="entry.icon"
          v-if="entry.icon"
          class="mr-3 h-6 w-6 text-gray-300 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
        />

        {{ entry.name }}
      </Link>
    </div>
  </nav>
</template>

<script>
  import * as SolidHeroicons from '@heroicons/vue/outline'
  import { Link } from '@inertiajs/inertia-vue3'
    export default {
        name: "Sidebar",
        components: {...SolidHeroicons, Link},
        props: {
            main_character: Object,
            activeEntryUrl: {
                type: String,
                required: true,
            }
        },
        data() {
            return {
                sidebarEntries: this.$page.props.sidebar
            }
        },
        computed: {
            impersonation() {
                return this.$page.props.user.data.impersonating
            }
        },
        watch: {
            impersonation() {
                this.sidebarEntries = this.$page.props.sidebar
            }
        },
        methods: {
            isActive(routename) {

                return this.activeEntryUrl === this.route(routename)
            }
        }
    }
</script>

<style scoped>

</style>
