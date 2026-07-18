<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle" />

    <!-- Only corporations that already have a Job Posting are listed. -->
    <CorporationRecruitment
      v-for="enlistment in enlistments"
      :key="enlistment.corporation_id"
      :enlistment="enlistment"
    />

    <!-- Empty state: nothing posted yet. -->
    <div
      v-if="enlistments.length === 0"
      class="rounded-lg border-2 border-dashed border-gray-300 px-6 py-10 text-center"
    >
      <h3 class="text-sm font-medium text-gray-900">
        No job postings yet
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ canManageRecruitment
          ? 'Create one below to start recruiting.'
          : 'No corporations are currently recruiting.' }}
      </p>
    </div>

    <!-- Create surface (managers only): a "Create job posting" button reveals an inline panel. -->
    <section
      v-if="canManageRecruitment"
      class="space-y-3"
    >
      <div
        v-if="! showCreate"
        class="flex justify-end"
      >
        <Button
          :is-inertia-button="false"
          @click="showCreate = true"
        >
          <PlusIcon
            class="mr-1.5 h-5 w-5 text-gray-400"
            aria-hidden="true"
          />
          Create job posting
        </Button>
      </div>

      <CardWithHeader v-else>
        <template #header>
          <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">
              Create a job posting
            </h2>
            <button
              type="button"
              class="text-sm font-medium text-gray-500 hover:text-gray-700"
              @click="cancelCreate"
            >
              Cancel
            </button>
          </div>
        </template>

        <form
          class="space-y-6 px-4 py-5 sm:p-6"
          @submit.prevent="submit"
        >
          <!-- 1. Corporation search — reuses the ACL "applies to" ESI picker; shows nothing until
               the manager types (≥3 chars) and searches any corporation via ESI. -->
          <div>
            <label class="block text-sm font-medium text-gray-700">
              Corporation
            </label>
            <p class="text-sm text-gray-500">
              Search for any corporation by name.
            </p>

            <div
              v-if="! selectedCorporation"
              class="mt-2"
            >
              <EsiAutosuggest
                label="Corporation"
                placeholder="Search for a corporation…"
                :categories="['corporation']"
                :show-label="false"
                reset-after-select
                @selected-object="onCorporationSelected"
              />
            </div>
            <div
              v-else
              class="mt-2"
            >
              <DismissibleEntityButton
                :entity="selectedCorporation"
                @remove="clearCorporation"
              />
            </div>

            <p
              v-if="form.errors.corporation_id"
              class="mt-1 text-sm text-red-600"
            >
              {{ form.errors.corporation_id }}
            </p>
          </div>

          <!-- 2. Who can apply — a labelled choice, not two cryptic buttons. -->
          <fieldset
            :disabled="! selectedCorporation"
            :class="{ 'opacity-50': ! selectedCorporation }"
          >
            <legend class="text-sm font-medium text-gray-700">
              Who can apply?
            </legend>
            <div class="mt-2 space-y-3">
              <label class="flex items-start gap-3">
                <input
                  v-model="form.type"
                  type="radio"
                  value="character"
                  class="mt-1 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                >
                <span>
                  <span class="block text-sm font-medium text-gray-900">Recruits only</span>
                  <span class="block text-sm text-gray-500">
                    Individual characters apply to this corporation.
                  </span>
                </span>
              </label>
              <label class="flex items-start gap-3">
                <input
                  v-model="form.type"
                  type="radio"
                  value="user"
                  class="mt-1 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                >
                <span>
                  <span class="block text-sm font-medium text-gray-900">All characters</span>
                  <span class="block text-sm text-gray-500">
                    Applicants apply with their whole account.
                  </span>
                </span>
              </label>
            </div>
            <p
              v-if="form.errors.type"
              class="mt-1 text-sm text-red-600"
            >
              {{ form.errors.type }}
            </p>
          </fieldset>

          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="! selectedCorporation || form.processing"
              class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-xs hover:bg-indigo-700 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            >
              {{ form.processing ? 'Creating…' : 'Create job posting' }}
            </button>
          </div>
        </form>
      </CardWithHeader>
    </section>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { PlusIcon } from "@heroicons/vue/20/solid";
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import CorporationRecruitment from "@/Pages/Corporation/Recruitment/CorporationRecruitment.vue";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import Button from "@/Shared/Layout/Button.vue";
import EsiAutosuggest from "@/Shared/Components/EsiAutosuggest.vue";
import DismissibleEntityButton from "@/Shared/Layout/Buttons/DismissibleEntityButton.vue";
import { create as openRecruitment } from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/EnlistmentsController";

defineProps({
    canManageRecruitment: {
        type: Boolean,
        required: true,
    },
    enlistments: {
        type: Array,
        required: true,
    },
});

const pageTitle = "Corporation Recruitment";

const showCreate = ref(false);
// The corporation chosen from the ESI search ({ id, name, category, … }); null until one is picked.
const selectedCorporation = ref(null);

// type: 'character' = "Recruits only", 'user' = "All characters". Default to the narrower choice.
const form = useForm({
    corporation_id: null,
    type: "character",
});

function onCorporationSelected(entity) {
    if (! entity) {
        return;
    }

    selectedCorporation.value = entity;
    form.corporation_id = entity.id;
    form.clearErrors("corporation_id");
}

function clearCorporation() {
    selectedCorporation.value = null;
    form.corporation_id = null;
}

function resetPanel() {
    showCreate.value = false;
    selectedCorporation.value = null;
    form.reset();
    form.clearErrors();
}

function cancelCreate() {
    resetPanel();
}

function submit() {
    // On success the controller redirects back, so the new posting arrives in `enlistments` and its
    // card renders; collapse and reset the panel.
    form.post(openRecruitment.url(), {
        preserveScroll: true,
        onSuccess: () => resetPanel(),
    });
}
</script>
