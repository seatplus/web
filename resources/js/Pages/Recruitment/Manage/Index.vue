<template>
  <div class="space-y-3">
    <PageHeader page-title="Manage Recruitment">
      <template #primary>
        <HeaderButton @click="showOpenForm = !showOpenForm">
          Open posting
        </HeaderButton>
      </template>
    </PageHeader>

    <div
      v-if="showOpenForm"
      class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 space-y-4"
    >
      <h3 class="text-base font-semibold text-gray-900">
        Open a corporation for recruitment
      </h3>

      <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
        <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
          Corporation
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <EsiAutosuggest
            :categories="['corporation']"
            label="Corporation"
            :show-label="false"
            placeholder="Search any corporation"
            @selectedObject="selection => openForm.corporation = selection"
          />
          <div
            v-if="openForm.errors.corporation_id"
            class="mt-2 text-sm text-red-600"
          >
            {{ openForm.errors.corporation_id }}
          </div>
        </div>
      </div>

      <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
        <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
          Type
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2 space-y-1">
          <label class="flex items-center space-x-2 text-sm text-gray-900">
            <input
              v-model="openForm.type"
              type="radio"
              value="user"
              class="text-indigo-600 focus:ring-indigo-500"
            >
            <span>Whole account (every character must meet the scope requirements)</span>
          </label>
          <label class="flex items-center space-x-2 text-sm text-gray-900">
            <input
              v-model="openForm.type"
              type="radio"
              value="character"
              class="text-indigo-600 focus:ring-indigo-500"
            >
            <span>Single character</span>
          </label>
        </div>
      </div>

      <div class="flex justify-end">
        <button
          type="button"
          :disabled="openForm.processing || !openForm.corporation"
          class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
          @click="submitOpen"
        >
          Open posting
        </button>
      </div>
    </div>

    <div
      v-if="postings.length === 0"
      class="text-center py-12"
    >
      <ClipboardDocumentListIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-semibold text-gray-900">
        No postings yet
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        Open a corporation for recruitment to start receiving applications.
      </p>
    </div>

    <ManagePostingCard
      v-for="posting in postings"
      :key="posting.corporation_id"
      :posting="posting"
      :control-groups="controlGroups"
    />
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { ClipboardDocumentListIcon } from "@heroicons/vue/24/outline";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import HeaderButton from "@/Shared/Layout/HeaderButton.vue";
import EsiAutosuggest from "@/Shared/Components/EsiAutosuggest.vue";
import ManagePostingCard from "./ManagePostingCard.vue";

const props = defineProps({
  postings: {
    required: true,
    type: Array,
  },
  controlGroups: {
    required: true,
    type: Array,
  },
  openUrl: {
    required: true,
    type: String,
  },
});

const showOpenForm = ref(false);

const openForm = useForm({
  corporation: null,
  type: "user",
});

function submitOpen() {
  openForm
    .transform((data) => ({
      // EsiAutosuggest returns the ESI entity as { id, name, category }.
      corporation_id: data.corporation?.id,
      type: data.type,
    }))
    .post(props.openUrl, {
      preserveScroll: true,
      onSuccess: () => {
        openForm.reset();
        showOpenForm.value = false;
      },
    });
}
</script>
