<template>
  <div class="space-y-6">
    <!-- Name -->
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <label
        for="role-name"
        class="block text-sm font-medium text-gray-700"
      >{{ trans('web::access_control.fields.name') }}</label>
      <input
        id="role-name"
        v-model="form.name"
        type="text"
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
      >
      <p
        v-if="form.errors.name"
        class="mt-1 text-sm text-rose-600"
      >
        {{ form.errors.name }}
      </p>
    </div>

    <!-- Membership: join method + eligibility -->
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <h3 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.sections.membership') }}
      </h3>

      <div class="mt-4 grid gap-3 sm:grid-cols-2">
        <button
          v-for="method in joinMethods"
          :key="method.key"
          type="button"
          class="rounded-lg border p-4 text-left transition"
          :class="form.method === method.key
            ? 'border-indigo-600 ring-1 ring-indigo-600'
            : 'border-gray-200 hover:border-gray-300'"
          @click="form.method = method.key"
        >
          <span class="block text-sm font-medium text-gray-900">{{ method.label }}</span>
          <span class="mt-1 block text-xs text-gray-500">{{ method.description }}</span>
        </button>
      </div>

      <!-- Eligibility (criteria) — only when the join method gates on it -->
      <div
        v-if="selectedMethod?.uses_eligibility"
        class="mt-6 border-t border-gray-100 pt-6"
      >
        <label class="block text-sm font-medium text-gray-700">
          {{ trans('web::access_control.eligibility.label') }}
        </label>
        <p class="text-sm text-gray-500">
          {{ trans('web::access_control.eligibility.help') }}
        </p>
        <div class="mt-2">
          <EsiMultiselect
            v-model="form.eligibility"
            :categories="['corporation', 'alliance']"
            :label="trans('web::access_control.eligibility.label')"
            :placeholder="trans('web::access_control.eligibility.label')"
          />
        </div>
      </div>
    </div>

    <!-- Authorization: what the permissions apply to -->
    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <h3 class="text-sm font-semibold text-gray-900">
        {{ trans('web::access_control.sections.authorization') }}
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ trans('web::access_control.applies_to.help') }}
      </p>

      <!-- Mode -->
      <div class="mt-4 flex flex-wrap gap-2">
        <button
          v-for="mode in ['only_these', 'everyone_except']"
          :key="mode"
          type="button"
          class="rounded-md px-3 py-1.5 text-sm font-medium"
          :class="form.mode === mode ? 'bg-indigo-600 text-white' : 'text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50'"
          @click="form.mode = mode"
        >
          {{ trans(`web::access_control.applies_to.mode.${mode}`) }}
        </button>
      </div>

      <div class="mt-4">
        <EsiMultiselect
          v-model="form.included"
          :categories="['character', 'corporation', 'alliance']"
          :label="trans('web::access_control.applies_to.label')"
          :placeholder="trans('web::access_control.applies_to.label')"
        />
      </div>

      <!-- Never (forbidden) carve-outs -->
      <div class="mt-6 border-t border-gray-100 pt-6">
        <label class="block text-sm font-medium text-gray-700">
          {{ trans('web::access_control.applies_to.exclude') }}
        </label>
        <p class="text-sm text-gray-500">
          {{ trans('web::access_control.applies_to.exclude_help') }}
        </p>
        <div class="mt-2">
          <EsiMultiselect
            v-model="form.excluded"
            :categories="['character', 'corporation', 'alliance']"
            :label="trans('web::access_control.applies_to.exclude')"
            :placeholder="trans('web::access_control.applies_to.exclude')"
          />
        </div>
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
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import { useTranslations } from "@/composables/useTranslations";
import { automatic, manual, onRequest, optIn } from "@/routes/acl/update";

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

// Map the resource's {id, entity_type, name} entities into EsiMultiselect's {id, name, category} shape.
const toSelections = (entities) => (entities ?? []).map((entity) => ({
    id: entity.id,
    name: entity.name,
    category: entity.entity_type,
}));

const form = useForm({
    name: props.role.name,
    method: props.role.type,
    mode: props.role.applies_to?.mode ?? "only_these",
    included: toSelections(props.role.applies_to?.included),
    excluded: toSelections(props.role.applies_to?.excluded),
    eligibility: toSelections(props.role.eligibility),
});

const selectedMethod = computed(() => props.joinMethods.find((method) => method.key === form.method));

const updateActions = { automatic, manual, "on-request": onRequest, "opt-in": optIn };

const save = () => {
    const action = updateActions[form.method];

    const toEntities = (selections, affiliationType) => selections.map((selection) => ({
        entity_id: selection.id,
        entity_type: selection.category,
        ...(affiliationType ? { affiliation_type: affiliationType } : {}),
    }));

    form
        .transform((data) => ({
            name: data.name,
            affiliated: [
                ...toEntities(data.included, data.mode === "everyone_except" ? "inverse" : "allowed"),
                ...toEntities(data.excluded, "forbidden"),
            ],
            assigned: selectedMethod.value?.uses_eligibility ? toEntities(data.eligibility) : [],
        }))
        .post(action.url({ role_id: props.role.id }), { preserveScroll: true });
};
</script>
