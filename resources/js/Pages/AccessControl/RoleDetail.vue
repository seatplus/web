<template>
  <div class="space-y-6">
    <PageHeader
      :page-title="role.name"
      :breadcrumbs="breadcrumbs"
    />
    <RoleOverviewSection :role="role" />
  </div>
</template>

<script setup>
import { computed } from "vue";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import RoleOverviewSection from "@/Shared/Components/AccessControl/RoleOverviewSection.vue";
import { useTranslations } from "@/composables/useTranslations";
import ShowControlGroupsController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ShowControlGroupsController";

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
</script>
