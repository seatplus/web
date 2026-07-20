<template>
  <div class="space-y-3">
    <PageHeader page-title="Reviews" />

    <p class="max-w-4xl text-sm leading-5 text-gray-500">
      Applications waiting for your decision. You only see applications currently at a stage your
      control group reviews.
    </p>

    <div
      v-if="pending.length === 0"
      class="text-center py-12"
    >
      <InboxStackIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-semibold text-gray-900">
        Nothing to review
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        No applications are waiting at a stage you handle.
      </p>
    </div>

    <ul
      v-else
      role="list"
      class="divide-y divide-gray-200 bg-white border border-gray-200 rounded-lg shadow-sm"
    >
      <li
        v-for="row in pending"
        :key="row.application_id"
        class="flex items-center gap-4 p-4"
      >
        <EveImage
          v-if="row.applicant.character_id"
          :object="{ character_id: row.applicant.character_id }"
          :size="64"
          tailwind_class="h-10 w-10 rounded-full"
        />
        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold text-gray-900 truncate">
            {{ row.applicant.name ?? 'Unknown applicant' }}
            <span class="text-xs font-normal text-gray-400">
              {{ row.applicant.is_user ? 'account-wide' : (row.covered_count > 1 ? `${row.covered_count} characters` : 'single character') }}
            </span>
          </p>
          <p class="text-sm text-gray-500 truncate">
            [{{ row.corporation.ticker }}] {{ row.corporation.name }}
          </p>
        </div>
        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 whitespace-nowrap">
          Stage {{ row.stage.position + 1 }} of {{ row.total_stages }}: {{ row.stage.label }}
        </span>
        <Link
          :href="row.review_url"
          class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
        >
          Review
        </Link>
      </li>
    </ul>

    <div
      v-if="history.length"
      class="space-y-2 pt-4"
    >
      <h2 class="text-sm font-semibold text-gray-900">
        History
      </h2>
      <p class="text-sm text-gray-500">
        Past decisions for the corporations you recruit for.
      </p>
      <ul
        role="list"
        class="divide-y divide-gray-200 bg-white border border-gray-200 rounded-lg shadow-sm"
      >
        <li
          v-for="row in history"
          :key="row.application_id"
          class="flex items-center gap-4 p-4"
        >
          <EveImage
            v-if="row.applicant.character_id"
            :object="{ character_id: row.applicant.character_id }"
            :size="64"
            tailwind_class="h-10 w-10 rounded-full"
          />
          <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-gray-900 truncate">
              {{ row.applicant.name ?? 'Unknown applicant' }}
              <span class="text-xs font-normal text-gray-400">
                {{ row.applicant.is_user ? 'account-wide' : (row.covered_count > 1 ? `${row.covered_count} characters` : 'single character') }}
              </span>
            </p>
            <p class="text-sm text-gray-500 truncate">
              [{{ row.corporation.ticker }}] {{ row.corporation.name }}
            </p>
          </div>
          <span
            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium whitespace-nowrap"
            :class="row.status === 'accepted' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600'"
          >
            {{ row.status }}
          </span>
          <Link
            :href="row.review_url"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-500 whitespace-nowrap"
          >
            View
          </Link>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { InboxStackIcon } from "@heroicons/vue/24/outline";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import EveImage from "@/Shared/EveImage.vue";

defineProps({
  pending: {
    required: true,
    type: Array,
  },
  history: {
    required: true,
    type: Array,
  },
});
</script>
