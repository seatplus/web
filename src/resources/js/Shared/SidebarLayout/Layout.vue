<template>
  <!--TODO: Include Alert Component-->

  <div
    class="h-full min-h-screen flex overflow-hidden bg-gray-100"
    @keydown.escape="sidebarOpen = false"
  >
    <MobileMenu
      v-show="sidebarOpen"
      v-model:sidebarOpen="sidebarOpen"
      :active-sidebar-element="activeSidebarElement"
    />
    <DesktopSidebar :active-sidebar-element="activeSidebarElement" />

    <div class="flex flex-col w-0 flex-1 overflow-hidden">
      <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
        <button
          class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:bg-gray-100 focus:text-gray-600 md:hidden"
          @click.stop="sidebarOpen = true"
        >
          <svg
            class="h-6 w-6"
            stroke="currentColor"
            fill="none"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h7"
            />
          </svg>
        </button>
        <div class="flex-1 px-4 flex justify-between">
          <div class="flex-1 flex">
            <!--Search bar-->
            <!--<div class="w-full flex md:ml-0">
                            <label for="search_field" class="sr-only">Search</label>
                            <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                                    </svg>
                                </div>
                                <input id="search_field" class="block w-full h-full pl-8 pr-3 py-2 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 sm:text-sm" placeholder="Search" />
                            </div>
                        </div>-->
          </div>
          <div class="ml-4 flex items-center md:ml-6">
            <NotificationsBell />
            <div class="ml-3 relative">
              <!--User Menu-->
              <Menu />
            </div>
          </div>
        </div>
      </div>

      <main
        class="flex-1 relative z-0 overflow-y-auto py-6 focus:outline-none"
        tabindex="0"
      >
        <Alerts />

        <Notifications class="sm:mt-16 z-40" />

        <div
          v-if="isMissingRequiredScopes()"
          class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mb-3"
        >
          <RequiredScopesWarning :missing_characters_scopes="missing_characters_scopes" />
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
          <slot />
        </div>

        <!--        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mb-3">
          <slot name="title">
            <h1 class="text-2xl font-semibold text-gray-900">
              {{ page }}
            </h1>
          </slot>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
          &lt;!&ndash; Replace with your content &ndash;&gt;
          <slot />
          &lt;!&ndash; /End replace &ndash;&gt;
        </div>-->

        <ImpersonatingBanner v-if="$page.props.user.data.impersonating" />
      </main>

      <transition leave-active-class="duration-300">
        <slot name="modal" />
      </transition>

      <SlideOverComponent :dispatch_transfer_object="dispatch_transfer_object" />

      <!--      <PortalTarget name="layout" />-->
      <!--            <slot name="slideOver"></slot>-->
    </div>
  </div>
</template>

<script>
    import RequiredScopesWarning from "./RequiredScopesWarning"
    import Menu from "@/Shared/Menu"
    import Notifications from "../Notifications/Notifications"
    import NotificationsBell from "./NotificationsBell"
    import Alerts from "./Alerts"
    import ImpersonatingBanner from "./ImpersonatingBanner"
    import SlideOverComponent from "@/Shared/Components/SlideOverComponent";
    import MobileMenu from "./MobileMenu";
    import Sidebar from "./Sidebar";
    import DesktopSidebar from "./DesktopSidebar";
    //import { PortalTarget } from 'portal-vue'

    export default {
        name: "Layout",
        components: {
          DesktopSidebar,
          MobileMenu,
            SlideOverComponent,
            ImpersonatingBanner,
            Alerts,
            NotificationsBell,
            Notifications,
            Menu,
            Sidebar,
            RequiredScopesWarning,
            //PortalTarget
        },
        props   : {
            page: {
                type: String,
                default: 'PAGE HEADER',
                required: false
            },
            activeSidebarElement: {
                type: String,
                default: '',
                required: false,
            },
            dispatch_transfer_object: {
                type: Object,
                required: false
            }
        },
        data() {
            return {
                sidebarOpen: false,
                menuOpen: false,
                requiredScopes: this.dispatch_transfer_object ? this.dispatch_transfer_object.required_scopes : []
            }
        },
        computed: {
            missing_characters_scopes: function () {
                let returnValue = []
                let requiredScopes= this.requiredScopes

                _.forEach(this.$page.props.user.data.characters, function (character) {

                    let missing_scopes = _.difference(requiredScopes, character.scopes)

                    if(_.isEmpty(missing_scopes))
                        return

                    returnValue.push({
                        character_id: character.character_id,
                        name: character.name,
                        missing_scopes: missing_scopes
                    })
                })

                return returnValue;
            }
        },
        methods: {
            getActiveSidebarElement() {
                return this.activeSidebarElement != null ? this.activeSidebarElement : window.location.href
            },
            isMissingRequiredScopes() {
                return ! _.isEmpty(this.missing_characters_scopes)
            },
        },
    }
</script>

<style scoped>

</style>
