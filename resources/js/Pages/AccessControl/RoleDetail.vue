<template>
  <div class="space-y-6">
    <PageHeader
      :page-title="role.name"
      :breadcrumbs="breadcrumbs"
    >
      <template
        v-if="can_edit"
        #primary
      >
        <Link
          :href="configureUrl"
          class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          {{ trans('web::access_control.actions.configure') }}
        </Link>
      </template>
    </PageHeader>
    <RoleOverviewSection :role="role" />
  </div>
</template>

<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import RoleOverviewSection from "@/Shared/Components/AccessControl/RoleOverviewSection.vue";
import { useTranslations } from "@/composables/useTranslations";
import ShowControlGroupsController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ShowControlGroupsController";
import { index as configureRoute } from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ManageControlGroupMembersController";

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    // Passed by the controller; drives the admin/moderator entry points added in PR 2/3.
    can_edit: {
        type: Boolean,
        default: false,
    },
});

const { trans } = useTranslations();

const breadcrumbs = computed(() => [
    { name: trans("web::access_control.groups"), route: ShowControlGroupsController.url() },
    { name: props.role.name, route: "" },
]);

const configureUrl = computed(() => configureRoute.url({ role_id: props.role.id }));
</script>
