<template>
  <div class="space-y-3">
    <PageHeader page-title="Observation" />

    <p class="max-w-4xl text-sm leading-5 text-gray-500">
      Employees of the corporations you manage: their SSO-scope compliance, last in-game activity,
      and lifecycle status.
    </p>

    <div
      v-if="corporations.length === 0"
      class="text-center py-12"
    >
      <EyeIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-semibold text-gray-900">
        Nothing to observe
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        No corporation or alliance SSO scopes are configured for the corporations you manage.
      </p>
    </div>

    <ObservationComponent
      v-for="corporation in corporations"
      :key="corporation.corporation_id"
      :corporation="corporation"
    />
  </div>
</template>

<script setup>
import { EyeIcon } from "@heroicons/vue/24/outline";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import ObservationComponent from "./ObservationComponent.vue";

defineProps({
  corporations: {
    required: true,
    type: Array,
  },
});
</script>
