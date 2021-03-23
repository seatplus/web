<template>
  <div class="space-y-3">

    <HorizonStats class="mb-3" />
    <Commands class="mb-3" />

    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="border-b border-gray-200 px-4 pt-5 sm:px-6">
        <!-- Content goes here -->
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Available Settings
        </h3>
        <div>
          <div class="sm:hidden">
            <select
              v-model="currentRoute"
              class="form-select block w-full"
            >
              <option
                v-for="navTab in navTabs"
                :value="navTab.route"
              >
                {{ navTab.name }}
              </option>
            </select>
          </div>
          <div class="hidden sm:block">
            <div>
              <!--class="border-b border-gray-200"-->
              <nav class="flex -mb-px">
                <div
                  v-for="(navTab,index) in navTabs"
                  :key="index"
                  :class="[{'ml-8': index >0}, isActive(navTab.route) ? 'border-indigo-500 text-indigo-600 focus:text-indigo-800 focus:border-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300','group focus:outline-none inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm leading-5 cursor-pointer']"
                  @click="visitRoute(navTab.route)"
                >
                  <svg
                    :class="['-ml-0.5 mr-2 h-5 w-5', isActive(navTab.route) ? 'text-indigo-500 group-focus:text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 group-focus:text-gray-600']"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    v-html="navTab.logo"
                  />
                  <span>{{ navTab.name }}</span>
                </div>
              </nav>
            </div>
          </div>
        </div>
        <!-- We use less vertical padding on card headers on desktop than on body sections -->
      </div>
      <div>
        <!--class="px-4 py-5 sm:p-6"-->
        <!-- Content goes here -->
        <slot />
      </div>
      <div class="border-t border-gray-200 px-4 py-4 sm:px-6">
        <!-- Content goes here -->
        <slot name="footer" />
        <!-- We use less vertical padding on card footers at all sizes than on headers or body sections -->
      </div>
    </div>
  </div>
</template>

<script>
import Layout from "@/Shared/SidebarLayout/Layout"
import Commands from "@/Pages/Configuration/Commands"
import HorizonStats from "./HorizonStats"
import {Inertia} from "@inertiajs/inertia"

export default {
    name: "Settings",
    components: {HorizonStats, Commands},
    data() {
        return {
            pageTitle: 'Server Settings',
            currentRoute: '',
            navTabs: []
        }
    },
    layout: (h, page) => h(Layout, { activeSidebarElement: page.props.activeSidebarElement }, [page]),
    watch: {
        navTabs(Tabs) {
            _.each(Tabs, navTab => {
                if(this.isActive(navTab.route))
                    this.currentRoute = navTab.route
            })
        },
        currentRoute(route) {
            if(this.isActive(route))
                return

            this.visitRoute(route)
        }
    },
    mounted() {
        this.$nextTick(function () {
            axios.get(route('settings.navigation')).then(result => {
                this.navTabs = result.data
            })
        })
    },
    methods: {
        isActive(string) {
            return route().current(string);
        },
        getRoute(name) {
            return route(name)
        },
        visitRoute(name) {

            const url = route(name)

            Inertia.visit(url,{
                method: 'get',
                preserveScroll: true
            })
        }
    }
}
</script>

