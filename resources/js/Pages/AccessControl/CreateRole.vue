<template>
  <div class="space-y-6">
    <PageHeader
      :page-title="trans('web::access_control.wizard.title')"
      :breadcrumbs="breadcrumbs"
    />

    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
      <WizardSteps
        :steps="visibleSteps"
        :current="currentVisibleIndex"
      />

      <div class="mt-8 min-h-[16rem]">
        <NameField
          v-if="currentStep.key === 'name'"
          v-model="form.name"
          :error="form.errors.name"
          @enter="onEnter"
        />
        <JoinMethodField
          v-else-if="currentStep.key === 'join_method'"
          v-model="form.method"
          :join-methods="joinMethods"
        />
        <EligibilityField
          v-else-if="currentStep.key === 'eligibility'"
          v-model="form.eligibility"
          v-model:anyone="form.anyone"
        />
        <AppliesToField
          v-else-if="currentStep.key === 'applies_to'"
          v-model:allowed="form.allowed"
          v-model:inverse="form.inverse"
          v-model:excluded="form.excluded"
          v-model:everything="form.everything"
        />
        <PermissionsField
          v-else-if="currentStep.key === 'permissions'"
          v-model="form.permissions"
          :available-permissions="availablePermissions"
        />

        <!-- Review -->
        <dl
          v-else
          class="space-y-4 text-sm"
        >
          <p class="text-gray-500">
            {{ trans('web::access_control.wizard.review') }}
          </p>

          <div class="flex justify-between border-t border-gray-100 pt-3">
            <dt class="text-gray-500">
              {{ trans('web::access_control.fields.name') }}
            </dt>
            <dd class="font-medium text-gray-900">
              {{ form.name }}
            </dd>
          </div>

          <div class="flex justify-between">
            <dt class="text-gray-500">
              {{ trans('web::access_control.join_method.label') }}
            </dt>
            <dd class="font-medium text-gray-900">
              {{ selectedMethod?.label }}
            </dd>
          </div>

          <!-- Eligibility -->
          <div
            v-if="selectedMethod?.uses_eligibility"
            class="flex items-start justify-between gap-4 border-t border-gray-100 pt-3"
          >
            <dt class="text-gray-500">
              {{ trans('web::access_control.eligibility.label') }}
            </dt>
            <dd class="flex-1">
              <div
                v-if="form.anyone"
                class="text-right font-medium text-indigo-700"
              >
                {{ trans('web::access_control.eligibility.everyone') }}
              </div>
              <div
                v-else-if="form.eligibility.length"
                class="flex flex-wrap justify-end gap-1.5"
              >
                <EntityChip
                  v-for="entity in form.eligibility"
                  :key="`eligibility-${entity.id}`"
                  :entity="entity"
                  chip-class="bg-indigo-50 text-indigo-700"
                />
              </div>
              <div
                v-else
                class="text-right text-gray-400"
              >
                {{ trans('web::access_control.eligibility.none') }}
              </div>
            </dd>
          </div>

          <!-- Applies to -->
          <div class="flex items-start justify-between gap-4 border-t border-gray-100 pt-3">
            <dt class="text-gray-500">
              {{ trans('web::access_control.applies_to.label') }}
            </dt>
            <dd class="flex-1">
              <div
                v-if="form.everything"
                class="text-right font-medium text-indigo-700"
              >
                {{ trans('web::access_control.applies_to.everything') }}
              </div>
              <template v-else>
                <AppliesToGroup
                  align="end"
                  :label="trans('web::access_control.applies_to.mode.only_these')"
                  :entities="form.allowed"
                  chip-class="bg-indigo-50 text-indigo-700"
                />
                <AppliesToGroup
                  align="end"
                  :label="trans('web::access_control.applies_to.mode.everyone_except')"
                  :entities="form.inverse"
                  chip-class="bg-amber-50 text-amber-700"
                />
                <AppliesToGroup
                  align="end"
                  :label="trans('web::access_control.applies_to.exclude')"
                  :entities="form.excluded"
                  chip-class="bg-rose-50 text-rose-700"
                />
                <div
                  v-if="! form.allowed.length && ! form.inverse.length && ! form.excluded.length"
                  class="text-right text-gray-400"
                >
                  {{ trans('web::access_control.applies_to.none') }}
                </div>
              </template>
            </dd>
          </div>

          <!-- Permissions -->
          <div class="flex items-start justify-between gap-4 border-t border-gray-100 pt-3">
            <dt class="text-gray-500">
              {{ trans('web::access_control.sections.permissions') }}
            </dt>
            <dd class="flex-1">
              <div
                v-if="form.permissions.length"
                class="flex flex-wrap justify-end gap-1.5"
              >
                <span
                  v-for="permission in form.permissions"
                  :key="permission"
                  class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700"
                >{{ permission }}</span>
              </div>
              <div
                v-else
                class="text-right text-gray-400"
              >
                {{ trans('web::access_control.permissions.none_selected') }}
              </div>
            </dd>
          </div>
        </dl>
      </div>

      <div class="mt-8 flex items-center justify-between border-t border-gray-100 pt-6">
        <button
          type="button"
          :disabled="currentVisibleIndex === 0"
          class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 disabled:opacity-40"
          @click="prev"
        >
          {{ trans('web::access_control.wizard.back') }}
        </button>

        <button
          v-if="! isLast"
          type="button"
          :disabled="! canAdvance"
          class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
          @click="next"
        >
          {{ trans('web::access_control.wizard.next') }}
        </button>
        <button
          v-else
          type="button"
          :disabled="form.processing || ! form.name"
          class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
          @click="submit"
        >
          {{ trans('web::access_control.actions.create') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import WizardSteps from "@/Shared/Components/AccessControl/WizardSteps.vue";
import AppliesToGroup from "@/Shared/Components/AccessControl/AppliesToGroup.vue";
import EntityChip from "@/Shared/Components/AccessControl/EntityChip.vue";
import NameField from "@/Shared/Components/AccessControl/fields/NameField.vue";
import JoinMethodField from "@/Shared/Components/AccessControl/fields/JoinMethodField.vue";
import EligibilityField from "@/Shared/Components/AccessControl/fields/EligibilityField.vue";
import AppliesToField from "@/Shared/Components/AccessControl/fields/AppliesToField.vue";
import PermissionsField from "@/Shared/Components/AccessControl/fields/PermissionsField.vue";
import { useTranslations } from "@/composables/useTranslations";
import { buildRolePayload } from "@/composables/useRolePayload";
import RoleHubIndexController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/RoleHubIndexController";
import { store as storeGroup } from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/CreateControlGroupController";

const props = defineProps({
    joinMethods: { type: Array, default: () => [] },
    availablePermissions: { type: Array, default: () => [] },
});

const { trans } = useTranslations();

const form = useForm({
    name: "",
    method: "manual",
    allowed: [],
    inverse: [],
    excluded: [],
    everything: false,
    eligibility: [],
    anyone: false,
    permissions: [],
});

const selectedMethod = computed(() => props.joinMethods.find((method) => method.key === form.method));

// Fixed step order; the eligibility step is skipped for join methods that don't use it.
const allSteps = computed(() => [
    { key: "name", label: trans("web::access_control.wizard.steps.name") },
    { key: "join_method", label: trans("web::access_control.wizard.steps.join_method") },
    { key: "eligibility", label: trans("web::access_control.wizard.steps.eligibility") },
    { key: "applies_to", label: trans("web::access_control.wizard.steps.applies_to") },
    { key: "permissions", label: trans("web::access_control.wizard.steps.permissions") },
    { key: "review", label: trans("web::access_control.wizard.steps.review") },
]);

const isEligibilityStep = (step) => step.key === "eligibility";
const visibleSteps = computed(() => allSteps.value.filter(
    (step) => ! isEligibilityStep(step) || selectedMethod.value?.uses_eligibility,
));

const cursor = ref(0); // index into allSteps
const currentStep = computed(() => allSteps.value[cursor.value]);
const currentVisibleIndex = computed(() => visibleSteps.value.findIndex((step) => step.key === currentStep.value.key));
const isLast = computed(() => currentStep.value.key === "review");

// Name is required before leaving step 1; other steps are optional.
const canAdvance = computed(() => currentStep.value.key !== "name" || form.name.trim().length > 0);

const shouldSkip = (step) => isEligibilityStep(step) && ! selectedMethod.value?.uses_eligibility;

const next = () => {
    let i = cursor.value + 1;
    while (i < allSteps.value.length && shouldSkip(allSteps.value[i])) {
        i++;
    }
    cursor.value = Math.min(i, allSteps.value.length - 1);
};

const prev = () => {
    let i = cursor.value - 1;
    while (i > 0 && shouldSkip(allSteps.value[i])) {
        i--;
    }
    cursor.value = Math.max(i, 0);
};

// Enter on a text step advances (or submits on the last step) when allowed.
const onEnter = () => {
    if (isLast.value) {
        if (! form.processing && form.name) {
            submit();
        }

        return;
    }

    if (canAdvance.value) {
        next();
    }
};

const submit = () => {
    form
        .transform((data) => ({ ...buildRolePayload(data, selectedMethod.value), type: data.method }))
        .post(storeGroup.url());
};

const breadcrumbs = computed(() => [
    { name: trans("web::access_control.groups"), route: RoleHubIndexController.url() },
    { name: trans("web::access_control.wizard.title"), route: "" },
]);
</script>
