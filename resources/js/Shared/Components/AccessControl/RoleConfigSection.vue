<template>
  <div class="space-y-6">
    <!-- Name -->
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <NameField
        v-model="form.name"
        :error="form.errors.name"
      />
    </div>

    <!-- Membership: join method + eligibility -->
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <h3 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.sections.membership') }}
      </h3>
      <div class="mt-4">
        <JoinMethodField
          v-model="form.method"
          :join-methods="joinMethods"
        />
      </div>
      <div
        v-if="selectedMethod?.uses_eligibility"
        class="mt-6 border-t border-gray-100 pt-6"
      >
        <EligibilityField v-model="form.eligibility" />
      </div>
    </div>

    <!-- Authorization: applies-to -->
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <h3 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.sections.authorization') }}
      </h3>
      <div class="mt-4">
        <AppliesToField
          v-model:mode="form.mode"
          v-model:included="form.included"
          v-model:excluded="form.excluded"
        />
      </div>
    </div>

    <!-- Permissions granted -->
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <h3 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.sections.permissions') }}
      </h3>
      <div class="mt-4">
        <PermissionsField
          v-model="form.permissions"
          :available-permissions="availablePermissions"
        />
      </div>
    </div>

    <div class="flex justify-end">
      <button
        type="button"
        :disabled="form.processing"
        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
        @click="save"
      >
        {{ trans('web::access_control.actions.save') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import NameField from "./fields/NameField.vue";
import JoinMethodField from "./fields/JoinMethodField.vue";
import EligibilityField from "./fields/EligibilityField.vue";
import AppliesToField from "./fields/AppliesToField.vue";
import PermissionsField from "./fields/PermissionsField.vue";
import { useTranslations } from "@/composables/useTranslations";
import { buildRolePayload, entitiesToSelections } from "@/composables/useRolePayload";
import { automatic, manual, onRequest, optIn } from "@/routes/acl/update";

const props = defineProps({
    role: { type: Object, required: true },
    joinMethods: { type: Array, default: () => [] },
    availablePermissions: { type: Array, default: () => [] },
});

const { trans } = useTranslations();

const form = useForm({
    name: props.role.name,
    method: props.role.type,
    mode: props.role.applies_to?.mode ?? "only_these",
    included: entitiesToSelections(props.role.applies_to?.included),
    excluded: entitiesToSelections(props.role.applies_to?.excluded),
    eligibility: entitiesToSelections(props.role.eligibility),
    permissions: [...(props.role.permissions ?? [])],
});

const selectedMethod = computed(() => props.joinMethods.find((method) => method.key === form.method));

const updateActions = { automatic, manual, "on-request": onRequest, "opt-in": optIn };

const save = () => {
    form
        .transform((data) => buildRolePayload(data, selectedMethod.value))
        .post(updateActions[form.method].url({ role_id: props.role.id }), { preserveScroll: true });
};
</script>
