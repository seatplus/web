<template>
  <div class="space-y-8">
    <PageHeader :page-title="trans('web::access_control.groups')">
      <template
        v-if="canCreate"
        #primary
      >
        <Link
          :href="createUrl"
          class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          {{ trans('web::access_control.actions.create') }}
        </Link>
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

    <!-- All groups (managers only) -->
    <section
      v-if="allGroups.length"
      class="space-y-3"
    >
      <h2 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.discover.all_groups') }}
      </h2>
      <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <li
          v-for="role in allGroups"
          :key="role.id"
        >
          <RoleCard :role="role" />
        </li>
      </ul>
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
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import RoleCard from "@/Shared/Components/AccessControl/RoleCard.vue";
import { useTranslations } from "@/composables/useTranslations";
import { create as createGroupRoute } from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/CreateControlGroupController";

defineProps({
    myGroups: {
        type: Array,
        default: () => [],
    },
    availableGroups: {
        type: Array,
        default: () => [],
    },
    allGroups: {
        type: Array,
        default: () => [],
    },
    canCreate: {
        type: Boolean,
        default: false,
    },
    canManage: {
        type: Boolean,
        default: false,
    },
});

const { trans } = useTranslations();

const createUrl = computed(() => createGroupRoute.url());
</script>
