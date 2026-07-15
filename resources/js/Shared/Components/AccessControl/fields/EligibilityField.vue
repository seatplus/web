<template>
  <div>
    <label class="block text-sm font-medium text-gray-700">
      {{ trans('web::access_control.eligibility.label') }}
    </label>
    <p class="text-sm text-gray-500">
      {{ trans('web::access_control.eligibility.help') }}
    </p>

    <!-- Anyone toggle: every user is eligible (Doomheim sentinel criterion). -->
    <div class="mt-3 flex items-center gap-3">
      <SimpleToggle v-model="anyone" />
      <div
        class="cursor-pointer"
        @click="anyone = ! anyone"
      >
        <span class="text-sm font-medium text-gray-700">
          {{ trans('web::access_control.eligibility.everyone') }}
        </span>
        <p class="text-sm text-gray-500">
          {{ trans('web::access_control.eligibility.everyone_help') }}
        </p>
      </div>
    </div>

    <div
      v-show="! anyone"
      class="mt-4"
    >
      <EsiMultiselect
        v-model="model"
        :categories="['corporation', 'alliance']"
        :label="trans('web::access_control.eligibility.label')"
        :show-label="false"
        :placeholder="trans('web::access_control.eligibility.label')"
      />
    </div>
  </div>
</template>

<script setup>
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import SimpleToggle from "@/Shared/SimpleToggle.vue";
import { useTranslations } from "@/composables/useTranslations";

// Array of { id, name, category } — corp/alliance criteria entities.
const model = defineModel({ type: Array, default: () => [] });
// anyone → the Doomheim sentinel criterion (open to all).
const anyone = defineModel("anyone", { type: Boolean, default: false });

const { trans } = useTranslations();
</script>
