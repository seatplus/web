<template>
  <div class="space-y-6">
    <PageHeader
      :page-title="role.name"
      :breadcrumbs="breadcrumbs"
    >
      <template #primary>
        <button
          type="button"
          class="rounded-md px-3 py-2 text-sm font-medium text-rose-600 ring-1 ring-rose-200 hover:bg-rose-50"
          @click="destroy"
        >
          {{ trans('web::access_control.actions.delete') }}
        </button>
      </template>
    </PageHeader>

    <RoleConfigSection
      :role="role"
      :join-methods="joinMethods"
    />
  </div>
</template>

<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import RoleConfigSection from "@/Shared/Components/AccessControl/RoleConfigSection.vue";
import { useTranslations } from "@/composables/useTranslations";
import ShowControlGroupsController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ShowControlGroupsController";
import DeleteControlGroupController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/DeleteControlGroupController";

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    joinMethods: {
        type: Array,
        default: () => [],
    },
});

const { trans } = useTranslations();

const breadcrumbs = computed(() => [
    { name: trans("web::access_control.groups"), route: ShowControlGroupsController.url() },
    { name: props.role.name, route: "" },
]);

const destroy = () => {
    if (window.confirm(trans("web::access_control.actions.delete") + "?")) {
        router.delete(DeleteControlGroupController.url({ role_id: props.role.id }));
    }
};
</script>
