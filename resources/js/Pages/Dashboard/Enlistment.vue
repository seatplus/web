<template>
  <div
    class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 cursor-pointer  hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
    @click="openModal = true"
  >
    <div class="relative -mr-px w-0 flex-1 inline-flex items-center justify-between py-4 text-sm leading-5 text-gray-700 font-medium transition ease-in-out duration-150">
      <EntityBlock :entity="enlistment.corporation" />
      <span
        v-if="hasApplications"
        class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800"
      >
        Applied
      </span>
    </div>
  </div>
  <teleport to="#destination">
    <ModalWithFooter v-model="openModal">
      <template #symbol>
        <div class="shrink-0 flex items-center justify-center">
          <EveImage
            :object="enlistment"
            :size="256"
            tailwind_class="h-12 w-12 rounded-full" 
          />
        </div>
      </template>
      <template #title>
        {{ enlistment.corporation.name }}
      </template>
      <template #description>
        <CharacterApplication
          v-if="enlistment.type === 'character'"
          :enlistment="enlistment"
          :application-results="applicationResults.results.value"
        />
        <span v-else>
          This corporation accepts recruits on a user level. This means once you enlist, every character within your user account must comply with a possible set of scopes at all time.
        </span>
      </template>
      <template #buttons>
        <div v-if="enlistment.type === 'character'" />
        <span
          v-else
          class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"
        >
          <Link
            v-if="hasApplications"
            :href="route('delete.user.application')"
            method="delete"
            :preserve-state="false"
            as="button"
            class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring-red transition ease-in-out duration-150 sm:text-sm sm:leading-5"
          >
            <UserMinusIcon
              class="-ml-1 mr-2 h-5 w-5"
              aria-hidden="true"
            />
            Remove Application
          </Link>
          <Link
            v-else
            :href="route('post.application')"
            method="post"
            :preserve-state="false"
            as="button"
            :data="{corporation_id: enlistment.corporation_id}"
            class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-emerald-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-emerald-500 focus:outline-none focus:border-emerald-700 focus:ring-emerald transition ease-in-out duration-150 sm:text-sm sm:leading-5"
          >
            <UserPlusIcon
              class="-ml-1 mr-2 h-5 w-5"
              aria-hidden="true"
            />
            Apply
          </Link>
        </span>
      </template>
    </ModalWithFooter>
  </teleport>
</template>

<script>
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import {computed, ref} from "vue";
import ModalWithFooter from "@/Shared/Modals/ModalWithFooter.vue";
import EveImage from "@/Shared/EveImage.vue";
import CharacterApplication from "./CharacterApplication.vue";
import {useLoadCompleteResource} from "@/Functions/useLoadCompleteResource";
import {UserPlusIcon, UserMinusIcon} from "@heroicons/vue/20/solid";
import { Link } from '@inertiajs/vue3';

export default {
    name: "Enlistment",
    components: {CharacterApplication, EveImage, ModalWithFooter, EntityBlock, UserPlusIcon, UserMinusIcon, Link},
    props: {
        enlistment: {
            type: Object,
            required: true
        }
    },
    setup(props) {

        const applicationResults = useLoadCompleteResource('list.existing.applications', { corporation_id: props.enlistment.corporation_id })
        const openModal = ref(false)

        const hasApplications = computed(() => !_.isEmpty(applicationResults.results.value))

        return {
            hasApplications,
            openModal,
            applicationResults
        }
    }
}
</script>

<style scoped>

</style>