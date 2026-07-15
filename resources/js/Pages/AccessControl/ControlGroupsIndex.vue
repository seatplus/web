<template>
  <div class="space-y-8">
    <PageHeader :page-title="trans('web::access_control.groups')" />

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
  </div>
</template>

<script setup>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import RoleCard from "@/Shared/Components/AccessControl/RoleCard.vue";
import { useTranslations } from "@/composables/useTranslations";

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
</script>
