<template>
  <div class="space-y-8">
    <PageHeader :page-title="trans('web::access_control.groups')">
      <template
        v-if="canCreate"
        #primary
      >
        <button
          type="button"
          class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700"
          @click="createOpen = true"
        >
          {{ trans('web::access_control.actions.create') }}
        </button>
      </template>
    </PageHeader>

    <!-- My groups -->
    <section class="space-y-3">
      <h2 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.discover.my_groups') }}
      </h2>
      <ul
        v-if="myGroups.length"
        class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
      >
        <li
          v-for="role in myGroups"
          :key="role.id"
        >
          <RoleCard :role="role" />
        </li>
      </ul>
      <p
        v-else
        class="rounded-lg border border-dashed border-gray-200 py-8 text-center text-sm text-gray-500"
      >
        {{ trans('web::access_control.discover.no_groups') }}
      </p>
    </section>

    <!-- Available to join -->
    <section
      v-if="availableGroups.length"
      class="space-y-3"
    >
      <h2 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.discover.available') }}
      </h2>
      <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <li
          v-for="role in availableGroups"
          :key="role.id"
        >
          <RoleCard :role="role" />
        </li>
      </ul>
    </section>

    <!-- Create group -->
    <TransitionRoot
      :show="createOpen"
      as="template"
    >
      <Dialog
        class="relative z-30"
        @close="createOpen = false"
      >
        <div class="fixed inset-0 bg-black/30" />
        <div class="fixed inset-0 flex items-center justify-center p-4">
          <DialogPanel class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <DialogTitle class="text-base font-semibold text-gray-900">
              {{ trans('web::access_control.actions.create') }}
            </DialogTitle>
            <form
              class="mt-4 space-y-4"
              @submit.prevent="create"
            >
              <div>
                <label
                  for="new-group-name"
                  class="block text-sm font-medium text-gray-700"
                >{{ trans('web::access_control.fields.name') }}</label>
                <input
                  id="new-group-name"
                  v-model="createForm.name"
                  type="text"
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
              </div>
              <div class="flex justify-end gap-2">
                <button
                  type="button"
                  class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300"
                  @click="createOpen = false"
                >
                  {{ trans('web::access_control.actions.cancel') }}
                </button>
                <button
                  type="submit"
                  :disabled="createForm.processing"
                  class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                >
                  {{ trans('web::access_control.actions.create') }}
                </button>
              </div>
            </form>
          </DialogPanel>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from "@headlessui/vue";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import RoleCard from "@/Shared/Components/AccessControl/RoleCard.vue";
import { useTranslations } from "@/composables/useTranslations";
import CreateControlGroupController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/CreateControlGroupController";

defineProps({
    myGroups: {
        type: Array,
        default: () => [],
    },
    availableGroups: {
        type: Array,
        default: () => [],
    },
    canCreate: {
        type: Boolean,
        default: false,
    },
});

const { trans } = useTranslations();

const createOpen = ref(false);
const createForm = useForm({ name: "" });

const create = () => createForm.post(CreateControlGroupController.url(), {
    onSuccess: () => {
        createOpen.value = false;
        createForm.reset();
    },
});
</script>
