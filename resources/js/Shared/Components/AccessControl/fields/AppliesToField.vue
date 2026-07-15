<template>
  <div>
    <p class="text-sm text-gray-500">
      {{ trans('web::access_control.applies_to.help') }}
    </p>

    <!-- Everything toggle: permissions apply to all entities (Doomheim inverse). -->
    <div class="mt-3 flex items-center gap-3">
      <SimpleToggle v-model="everything" />
      <div
        class="cursor-pointer"
        @click="everything = ! everything"
      >
        <span class="text-sm font-medium text-gray-700">
          {{ trans('web::access_control.applies_to.everything') }}
        </span>
        <p class="text-sm text-gray-500">
          {{ trans('web::access_control.applies_to.everything_help') }}
        </p>
      </div>
    </div>

    <!-- The three affiliation types are independent and may be combined. -->
    <div
      v-show="! everything"
      class="mt-4 space-y-6"
    >
      <!-- Only these (allowed) -->
      <div>
        <label class="block text-sm font-medium text-gray-700">
          {{ trans('web::access_control.applies_to.mode.only_these') }}
        </label>
        <div class="mt-2">
          <EsiMultiselect
            v-model="allowed"
            :categories="['character', 'corporation', 'alliance']"
            :label="trans('web::access_control.applies_to.mode.only_these')"
            :show-label="false"
            :placeholder="trans('web::access_control.applies_to.mode.only_these')"
          />
        </div>
      </div>

      <!-- Everyone except (inverse) -->
      <div class="border-t border-gray-100 pt-6">
        <label class="block text-sm font-medium text-gray-700">
          {{ trans('web::access_control.applies_to.mode.everyone_except') }}
        </label>
        <div class="mt-2">
          <EsiMultiselect
            v-model="inverse"
            :categories="['character', 'corporation', 'alliance']"
            :label="trans('web::access_control.applies_to.mode.everyone_except')"
            :show-label="false"
            :placeholder="trans('web::access_control.applies_to.mode.everyone_except')"
          />
        </div>
      </div>

      <!-- Never (forbidden) carve-outs -->
      <div class="border-t border-gray-100 pt-6">
        <label class="block text-sm font-medium text-gray-700">
          {{ trans('web::access_control.applies_to.exclude') }}
        </label>
        <p class="text-sm text-gray-500">
          {{ trans('web::access_control.applies_to.exclude_help') }}
        </p>
        <div class="mt-2">
          <EsiMultiselect
            v-model="excluded"
            :categories="['character', 'corporation', 'alliance']"
            :label="trans('web::access_control.applies_to.exclude')"
            :show-label="false"
            :placeholder="trans('web::access_control.applies_to.exclude')"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import SimpleToggle from "@/Shared/SimpleToggle.vue";
import { useTranslations } from "@/composables/useTranslations";

// The three affiliation types map straight to the backend `affiliation_type` on submit and are
// independent — a group can grant "only these" AND "everyone except" AND "never" at once.
const allowed = defineModel("allowed", { type: Array, default: () => [] });
const inverse = defineModel("inverse", { type: Array, default: () => [] });
const excluded = defineModel("excluded", { type: Array, default: () => [] });
// everything → a single INVERSE Doomheim affiliation (applies to everyone).
const everything = defineModel("everything", { type: Boolean, default: false });

const { trans } = useTranslations();
</script>
