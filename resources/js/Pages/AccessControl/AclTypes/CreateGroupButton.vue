<template>
  <HeaderButton @click="open = true">
    Create new group
  </HeaderButton>
  <teleport to="#destination">
    <ModalWithFooter v-model="open">
      <template #symbol>
        <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
          <svg
            class="h-6 w-6 text-emerald-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
            />
          </svg>
        </div>
      </template>
      <template #title>
        <span>Create New Control Group </span>
      </template>
      <template #description>
        <div>
          <label
            for="role"
            class="block text-sm font-medium leading-5 text-gray-700"
          >Control Group</label>
          <div class="mt-1 relative rounded-md shadow-sm">
            <input
              id="role"
              v-model="name"
              type="text"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              placeholder="Some name"
            >
          </div>
          <p class="mt-2 text-sm text-gray-500">
            Chose a descriptive name for the group to be created
          </p>
        </div>
      </template>
      <template #buttons>
        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
          <Link
            method="post"
            as="button"
            :href="route('acl.create')"
            :data="{name: name}"
            type="button"
            class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-emerald-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-emerald-500 focus:outline-none focus:border-emerald-700 focus:ring-emerald transition ease-in-out duration-150 sm:text-sm sm:leading-5"
          >
            Create
          </Link>
        </span>
      </template>
    </ModalWithFooter>
  </teleport>
</template>

<script>
import HeaderButton from "@/Shared/Layout/HeaderButton.vue";
import ModalWithFooter from "@/Shared/Modals/ModalWithFooter.vue";
import { Link } from '@inertiajs/inertia-vue3'
export default {
  name: "CreateGroupButton",
  components: {ModalWithFooter, HeaderButton, Link},
  data() {
    return {
      open: false,
      name: ''
    }
  }
}
</script>

<style scoped>

</style>
