<template>
  <!-- Be sure to use this with a layout container that is full-width on mobile -->
  <div class="bg-white overflow-hidden shadow sm:rounded-lg my-5">
    <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
      <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
        <div class="ml-4 mt-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <EveImage
                tailwind_class="h-12 w-12 rounded-full"
                :size="256"
                :object="corporation"
              />
            </div>
            <div class="ml-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ corporation.name }}
              </h3>
              <p class="text-sm leading-5 text-gray-500">
                {{ corporation.alliance ? corporation.alliance.name : '' }}
              </p>
            </div>
          </div>
        </div>
        <div
          v-if="corporation.can_manage"
          class="ml-4 mt-4 flex-shrink-0 flex space-x-3"
        >
          <span class="inline-flex rounded-md shadow-sm">

            <Link
              :href="$route('edit.enlistment', corporation.corporation_id)"
              method="get"
              as="button"
              type="button"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:border-indigo-300 focus:ring-offset-2 focus:ring-indigo active:bg-indigo-200"
            >
              Edit Enlistment
            </Link>
          </span>

          <!-- <span class="inline-flex rounded-md shadow-sm">
            <button
              type="button"
              class="relative inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-50 focus:outline-none focus:border-indigo-300 focus:ring-indigo active:bg-indigo-200"
              @click="remove(corporation)"
            >
              Delete Enlistment
            </button>
          </span>-->
        </div>
      </div>
    </div>

    <div class="px-4 py-5 sm:p-6">
      <Applications :parameters="{corporation_id: corporation.corporation_id}" />
    </div>
  </div>
</template>

<script>
import EveImage from "@/Shared/EveImage"
import Applications from "./Applications"
import {Link} from "@inertiajs/inertia-vue3";

export default {
    name: "CorporationRecruitment",
    components: {Applications, EveImage, Link},
    props: {
        corporation: {
            required: true
        },
    },
  computed: {
      applicantsUrl() {
        return this.$route('open.corporation.applications', this.corporation.corporation_id)
      }
  },
    methods: {
        remove(corporation) {
            this.$inertia.delete(this.$route('delete.corporation.recruitment', corporation.corporation_id))
        }
    }
}
</script>

<style scoped>

</style>
