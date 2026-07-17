<template>
  <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-black/5">
    <h3 class="text-sm font-semibold text-gray-900">
      {{ trans('web::access_control.sections.applications') }}
    </h3>

    <ul
      v-if="applicants.length"
      class="mt-2 divide-y divide-gray-100"
    >
      <MemberRow
        v-for="applicant in applicants"
        :key="applicant.user_id"
        :member="applicant"
        :badge="trans('web::access_control.status.pending')"
      >
        <button
          type="button"
          class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700"
          @click="actions.approve(roleId, applicant.user_id)"
        >
          {{ trans('web::access_control.actions.approve') }}
        </button>
        <button
          type="button"
          class="rounded-md px-3 py-1.5 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50"
          @click="actions.deny(roleId, applicant.user_id)"
        >
          {{ trans('web::access_control.actions.deny') }}
        </button>
      </MemberRow>
    </ul>
    <p
      v-else
      class="mt-3 text-sm text-gray-400"
    >
      {{ trans('web::access_control.moderate.no_applications') }}
    </p>
  </section>
</template>

<script setup>
import MemberRow from "./MemberRow.vue";
import { useRoleActions } from "@/composables/useRoleActions";
import { useTranslations } from "@/composables/useTranslations";

defineProps({
    roleId: {
        type: Number,
        required: true,
    },
    applicants: {
        type: Array,
        default: () => [],
    },
});

const actions = useRoleActions();
const { trans } = useTranslations();
</script>
