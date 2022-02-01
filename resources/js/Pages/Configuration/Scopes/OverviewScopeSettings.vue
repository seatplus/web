<template>
  <Settings>
    <teleport to="#head">
      <title>{{ title('SSO Settings') }}</title>
    </teleport>
    <ul class="divide-y divide-gray-200">
      <WideListElement
        v-if="hasGlobalScopes"
        :url="$route('view.global.scopes')"
      >
        <template #avatar>
          <svg
            class="h-12 w-12"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </template>
        <template #upper_left>
          Global SSO Scopes
        </template>
        <template #navigation>
          <svg
            class="text-gray-400 h-5 w-5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
      </WideListElement>
      <WideListElement
        v-for="(entry, index) in entities"
        :key="entry.selectedEntity.id"
        :url="$route('view.scopes.settings', entry.selectedEntity.id)"
      >
        <template #avatar>
          <eve-image
            :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'"
            :object="entry.selectedEntity"
            :size="128"
          />
        </template>
        <template #upper_left>
          {{ entry.selectedEntity.name }}
        </template>
        <template #lower_right />
        <template #navigation>
          <svg
            class="text-gray-400 h-5 w-5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
      </WideListElement>
      <li>
        <inertia-link
          :href="$route('view.create.scopes')"
          class="block hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out"
        >
          <div class="flex items-center px-4 py-4 sm:px-6">
            <div class="min-w-0 flex-1 flex items-center">
              <div class="flex overflow-x-visible">
                <svg
                  fill="none"
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                  class="w-8 h-8"
                ><path d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </div>
              <div class="px-4 capitalize">
                create
              </div>
            </div>
            <div>
              <svg
                class="text-gray-400 h-5 w-5"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </inertia-link>
      </li>
    </ul>
  </Settings>
</template>

<script>
    import Settings from "../Settings"
    import WideListElement from "../../../Shared/WideListElement"
    import EveImage from "../../../Shared/EveImage"

    export default {
        name: "OverviewScopeSettings",
        components: {WideListElement, EveImage, Settings},
        props: {
            available_scopes: {
                type: Object,
                required: true
            },
            entries: {
                type: Array,
                required: true
            },
        },
        data() {
            return {
                layoutObject: {
                    pageHeader: 'Server Settings',
                    pageDescription: 'Scope',
                    activeSidebarElement: route('server.settings')
                },
            }
        },
        computed: {
            entities() {
                return _.filter(this.entries, (entry) => entry.selectedEntity.id)
            },
            hasGlobalScopes() {
                return _.size(this.entities) !== _.size(this.entries)
            }
        },
        methods: {

        },
    }
</script>

<style scoped>

</style>
