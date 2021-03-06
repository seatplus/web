<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <div class="h-screen flex overflow-hidden bg-gray-100">
    <!-- sidebar for mobile -->
    <TransitionRoot
      as="template"
      :show="sidebarOpen"
    >
      <Dialog
        as="div"
        static
        class="fixed inset-0 flex z-40 md:hidden"
        :open="sidebarOpen"
        @close="sidebarOpen = false"
      >
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <DialogOverlay class="fixed inset-0 bg-gray-600 bg-opacity-75" />
        </TransitionChild>
        <TransitionChild
          as="template"
          enter="transition ease-in-out duration-300 transform"
          enter-from="-translate-x-full"
          enter-to="translate-x-0"
          leave="transition ease-in-out duration-300 transform"
          leave-from="translate-x-0"
          leave-to="-translate-x-full"
        >
          <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-800">
            <TransitionChild
              as="template"
              enter="ease-in-out duration-300"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="ease-in-out duration-300"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button
                  class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                  @click="sidebarOpen = false"
                >
                  <span class="sr-only">Close sidebar</span>
                  <XIcon
                    class="h-6 w-6 text-white"
                    aria-hidden="true"
                  />
                </button>
              </div>
            </TransitionChild>
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
              <div class="flex-shrink-0 flex items-center px-4">
                <img
                  class="h-8 w-auto"
                  :src="logo"
                  alt="SeAT plus"
                >
              </div>
              <nav class="mt-5 px-2 space-y-3">
                <div
                  v-for="(category, name) in navigation"
                  :key="name"
                >
                  <h3 class="text-xs leading-4 font-semibold text-white uppercase tracking-wider">
                    {{ category.name }}
                  </h3>
                  <div class="space-y-1">
                    <inertia-link
                      v-for="item in category.entries"
                      :key="item.name"
                      :href="$route(item.route)"
                      :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'group flex items-center px-2 py-2 text-base font-medium rounded-md']"
                    >
                      <component
                        :is="item.icon"
                        :class="[item.current ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300', 'mr-4 flex-shrink-0 h-6 w-6']"
                        aria-hidden="true"
                      />
                      {{ item.name }}
                    </inertia-link>
                  </div>
                </div>
              </nav>
            </div>
            <div class="flex-shrink-0 flex bg-gray-700 p-4">
              <inertia-link
                :href="$route('user.settings')"
                class="flex-shrink-0 group block"
              >
                <div class="flex items-center">
                  <div>
                    <EveImage
                      tailwind_class="inline-block h-10 w-10 rounded-full"
                      :object="mainCharacter"
                    />
                  </div>
                  <div class="ml-3">
                    <p class="text-base font-medium text-white">
                      {{ mainCharacter.name }}
                    </p>
                    <p class="text-sm font-medium text-gray-400 group-hover:text-gray-300">
                      View settings
                    </p>
                  </div>
                </div>
              </inertia-link>
            </div>
          </div>
        </TransitionChild>
        <div class="flex-shrink-0 w-14">
          <!-- Force sidebar to shrink to fit close icon -->
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Static sidebar for desktop -->
    <div class="hidden md:flex md:flex-shrink-0">
      <div class="flex flex-col w-64">
        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <div class="flex flex-col h-0 flex-1 bg-gray-800">
          <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
            <div class="flex items-center flex-shrink-0 px-4">
              <img
                class="h-8 w-auto"
                :src="logo"
                alt="SeAT plus"
              >
            </div>
            <nav class="mt-5 flex-1 px-2 bg-gray-800 space-y-3">
              <template
                v-for="(category, name) in navigation"
                :key="`${name}.${component}`"
              >
                <inertia-link
                  v-if="category.entries.length === 1"
                  :href="$route(category.entries[0].route)"
                  :class="[category.entries[0].current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'group w-full flex items-center px-2 pr-2 py-2 text-sm font-medium rounded-md']"
                >
                  <component
                    :is="category.entries[0].icon"
                    :class="[category.entries[0].current ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300', 'mr-3 flex-shrink-0 h-6 w-6']"
                    aria-hidden="true"
                  />
                  {{ category.entries[0].name }}
                </inertia-link>
                <Disclosure
                  v-else
                  v-slot="{ open }"
                  :default-open="category.current"
                  as="div"
                  class="space-y-1"
                >
                  <!--              :class="[category.current ? 'bg-gray-100 text-gray-900' : 'bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group w-full flex items-center pr-2 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500']"    -->
                  <DisclosureButton :class="['text-white font-semibold uppercase text-xs hover:text-gray-400', 'group w-full flex items-center pr-2 py-2 text-left text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500']">
                    <svg
                      :class="[open ? 'text-gray-400 rotate-90' : 'text-gray-300', 'mr-2 flex-shrink-0 h-5 w-5 transform group-hover:text-gray-400 transition-colors ease-in-out duration-150']"
                      viewBox="0 0 20 20"
                      aria-hidden="true"
                    >
                      <path
                        d="M6 6L14 10L6 14V6Z"
                        fill="currentColor"
                      />
                    </svg>
                    {{ category.name }}
                  </DisclosureButton>
                  <DisclosurePanel class="space-y-1">
                    <!--                    class="group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50"-->
                    <inertia-link
                      v-for="subItem in category.entries"
                      :key="subItem.name"
                      :href="$route(subItem.route)"
                      :class="[subItem.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'group w-full flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md']"
                    >
                      <component
                        :is="subItem.icon"
                        :class="[subItem.current ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300', 'mr-3 flex-shrink-0 h-6 w-6']"
                        aria-hidden="true"
                      />
                      {{ subItem.name }}
                    </inertia-link>
                  </DisclosurePanel>
                </disclosure>
                <!--                <h3 class="text-xs leading-4 font-semibold text-white uppercase tracking-wider">
                  {{ category.name }}
                </h3>
                <div class="space-y-1">
                  <inertia-link
                    v-for="item in category.entries"
                    :key="item.name"
                    :href="$route(item.route)"
                    :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'group flex items-center px-2 py-2 text-sm font-medium rounded-md']"
                  >
                    <component
                      :is="item.icon"
                      :class="[item.current ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300', 'mr-3 flex-shrink-0 h-6 w-6']"
                      aria-hidden="true"
                    />
                    {{ item.name }}
                  </inertia-link>
                </div>-->
              </template>
            </nav>
          </div>
          <!-- User Menu          -->
          <div class="flex-shrink-0 flex bg-gray-700 p-4">
            <inertia-link
              :href="$route('user.settings')"
              class="flex-shrink-0 w-full group block"
            >
              <div class="flex items-center">
                <div>
                  <EveImage
                    tailwind_class="inline-block h-9 w-9 rounded-full"
                    :object="mainCharacter"
                  />
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-white">
                    {{ mainCharacter.name }}
                  </p>
                  <p class="text-xs font-medium text-gray-300 group-hover:text-gray-200">
                    View settings
                  </p>
                </div>
              </div>
            </inertia-link>
          </div>
        </div>
      </div>
    </div>
    <!--    flex w-0 flex-1 overflow-hidden -->
    <div class="flex flex-col md:flex-row min-w-0 flex-1 overflow-hidden">
      <div class="md:hidden">
        <div class="flex items-center justify-between bg-gray-800 border-b border-gray-200 px-4 py-1.5">
          <div>
            <img
              class="h-8 w-auto"
              :src="logo"
              alt="SeAT plus"
            >
          </div>
          <div>
            <button
              type="button"
              class="-mr-3 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900"
              @click="sidebarOpen = true"
            >
              <span class="sr-only">Open sidebar</span>
              <svg
                class="h-6 w-6"
                x-description="Heroicon name: outline/menu"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
      <Notifications class="sm:mt-16 z-40" />
      <slot />
      <ImpersonatingBanner v-if="$page.props.user.data.impersonating" />
    </div>
  </div>
</template>

<script>
import {computed, onMounted, ref, watch} from 'vue'
import {
    Dialog,
    DialogOverlay,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    TransitionChild,
    TransitionRoot
} from '@headlessui/vue'
import {usePage} from "@inertiajs/inertia-vue3";
import * as OutlineHeroicons from '@heroicons/vue/outline'
import route from 'ziggy'
import ImpersonatingBanner from "./ImpersonatingBanner";
import Notifications from "../Notifications/Notifications";
import EveImage from "../EveImage";

/*const navigation2 = [
    { name: 'Dashboard', href: '#', icon: HomeIcon, current: true },
    { name: 'Team', href: '#', icon: UsersIcon, current: false },
    { name: 'Projects', href: '#', icon: FolderIcon, current: false },
    { name: 'Calendar', href: '#', icon: CalendarIcon, current: false },
    { name: 'Documents', href: '#', icon: InboxIcon, current: false },
    { name: 'Reports', href: '#', icon: ChartBarIcon, current: false },
]*/

export default {
    name: "DarkSidebar",
    components: {
        EveImage,
        Notifications,
        ImpersonatingBanner,
        Dialog,
        DialogOverlay,
        TransitionChild,
        TransitionRoot,
        Disclosure,
        DisclosureButton,
        DisclosurePanel,
        ...OutlineHeroicons,
    },
    setup() {
        const sidebarOpen = ref(false)
        const sidebar = usePage().props.value.sidebar
        const logo = usePage().props.value.images.logo
        const activeSidebarElement = ref(usePage().props.value.activeSidebarElement)
        const main = _.get(usePage().props.value.user, 'data.main_character')
        const component = usePage().component
        const navigation = ref([])

        const buildNavigation = function () {

            return _.map(sidebar, (category) => {

                let subItems = _.map(category.entries, (entry) => {

                    let current = activeSidebarElement.value != null ? activeSidebarElement.value : route().current()

                    return {
                        ...entry,
                        current: current === entry.route
                    }

                })

                return {
                    name: category.name,
                    entries: subItems,
                    current: !!_.find(subItems, {current: true})
                }
            })
        }

        const mainCharacter = computed(() => {
            return main !== null ? main : {name: 'unknown', character_id: null}
        })

        watch(component, () => navigation.value = buildNavigation())

        onMounted(() => navigation.value = buildNavigation())

        return {
            navigation,
            sidebarOpen,
            logo,
            mainCharacter,
            component
        }
    },
}
</script>

