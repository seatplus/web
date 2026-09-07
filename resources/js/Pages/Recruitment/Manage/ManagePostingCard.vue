<template>
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 space-y-4">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <EveImage
          :object="{ corporation_id: posting.corporation.corporation_id }"
          :size="64"
          tailwind_class="h-10 w-10 rounded"
        />
        <div>
          <h3 class="text-base font-semibold text-gray-900">
            [{{ posting.corporation.ticker }}] {{ posting.corporation.name }}
          </h3>
          <p
            v-if="posting.corporation.alliance"
            class="text-sm text-gray-500"
          >
            {{ posting.corporation.alliance.name }}
          </p>
        </div>
      </div>
      <span
        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
        :class="posting.type === 'user' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-700'"
      >
        {{ posting.type === 'user' ? 'Whole account' : 'Single character' }}
      </span>
    </div>

    <div class="space-y-2">
      <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
        Review stages
      </p>
      <p class="text-xs text-gray-500">
        Applications advance through these stages in order. The dropdown sets <span class="font-medium">who reviews</span> each stage:
        <span class="font-medium">Any recruiter</span> lets anyone allowed to accept or deny applications for this corporation review it,
        or pick a control group to restrict that stage to its members (e.g. stage&nbsp;1 &ldquo;Junior HR&rdquo;, stage&nbsp;2 &ldquo;Senior HR&rdquo;).
      </p>

      <div
        v-for="(stage, index) in form.stages"
        :key="index"
        class="flex items-center gap-2"
      >
        <span class="text-sm text-gray-400 w-5 text-right">{{ index + 1 }}.</span>
        <input
          v-model="stage.label"
          type="text"
          placeholder="Stage name (e.g. Interview)"
          class="flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
        <select
          v-model="stage.role_id"
          aria-label="Reviewed by"
          title="Who can review this stage"
          class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option :value="null">
            Any recruiter (no group)
          </option>
          <option
            v-for="group in controlGroups"
            :key="group.id"
            :value="group.id"
          >
            {{ group.name }}
          </option>
        </select>
        <button
          v-if="form.stages.length > 1"
          type="button"
          class="text-gray-400 hover:text-red-600"
          title="Remove stage"
          @click="removeStage(index)"
        >
          <XMarkIcon class="h-5 w-5" />
        </button>
        <span
          v-else
          class="h-5 w-5"
          aria-hidden="true"
        />
      </div>

      <p
        v-if="form.stages.length === 1"
        class="text-xs text-gray-400 pl-7"
      >
        A posting keeps at least one stage.
      </p>

      <div
        v-if="form.errors.stages"
        class="text-sm text-red-600"
      >
        {{ form.errors.stages }}
      </div>

      <button
        type="button"
        class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
        @click="addStage"
      >
        + Add stage
      </button>
    </div>

    <div class="border-t border-gray-100 pt-3 space-y-3">
      <button
        type="button"
        class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-500"
        @click="watchlistOpen = !watchlistOpen"
      >
        {{ watchlistOpen ? 'Hide' : 'Manage' }} watchlist
        <span
          v-if="watchlistCount > 0"
          class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700"
          title="Items, regions and systems on this watchlist"
        >
          {{ watchlistCount }} set
        </span>
      </button>

      <div
        v-if="watchlistOpen"
        class="space-y-4"
      >
        <p class="text-xs text-gray-500">
          Items, assets and contracts matching this watchlist are highlighted while reviewing an
          applicant (and, later, while observing an employee). Saved together with the review stages.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <EsiMultiselect
            v-model="form.regions"
            :categories="['region']"
            label="Regions"
            placeholder="Search for a region"
          />
          <EsiMultiselect
            v-model="form.systems"
            :categories="['solar_system']"
            label="Solar systems"
            placeholder="Search for a solar system"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Item types, groups or categories</label>
          <Autosuggest
            :key="itemsKey"
            placeholder="Search for items"
            @selected-object="addItem"
          />
          <div class="mt-2 flex flex-wrap gap-2">
            <DismissibleButton
              v-for="item in form.items"
              :id="item.id"
              :key="item.id"
              :name="item.name"
              @remove="removeItem"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="border-t border-gray-100 pt-3 space-y-3">
      <button
        type="button"
        class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-500"
        @click="scopesOpen = !scopesOpen"
      >
        {{ scopesOpen ? 'Hide' : 'Manage' }} corporation-wide SSO scopes
        <span
          v-if="form.selected_scopes.length > 0"
          class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700"
          title="ESI scopes characters must grant for this corporation"
        >
          {{ form.selected_scopes.length }} required
        </span>
      </button>

      <div
        v-if="scopesOpen"
        class="space-y-4"
      >
        <div class="rounded-md border border-amber-200 bg-amber-50 p-3 space-y-1">
          <p class="text-xs font-medium text-amber-900">
            This edits the corporation's SSO requirement, not a posting-only setting.
          </p>
          <p class="text-xs text-amber-800">
            The same record is used by Configuration &rarr; Scopes (superuser only), by member
            compliance and by Observation. Characters must have granted these ESI scopes to be
            compliant for this corporation, so changes here apply to
            <span class="font-medium">every</span> character in it, not only to applicants.
          </p>
          <p class="text-xs text-amber-800">
            Applies to: <span class="font-medium">{{ scopeTypeLabel }}</span>
            <template v-if="posting.required_scopes_type">
              &mdash; kept as configured; only Configuration &rarr; Scopes can change the level.
            </template>
          </p>
        </div>

        <!--
          Both pickers snapshot their props during setup and emit the whole selection, so the one
          that was not touched would otherwise write back its stale array and drop the other's
          scopes. Re-key them on the shared selection to force a resync — the same pattern as
          Configuration/Scopes/ScopeSettings.vue.
        -->
        <CharacterScopes
          :key="`character ${scopesAsString}`"
          v-model:selected-scopes="form.selected_scopes"
          :scopes="availableScopes.character"
        />
        <CorporationScopes
          :key="`corporation ${scopesAsString}`"
          v-model:selected-scopes="form.selected_scopes"
          :scopes="availableScopes.corporation"
        />
      </div>
    </div>

    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
      <button
        type="button"
        :disabled="closeForm.processing"
        class="text-sm font-medium text-red-600 hover:text-red-500 disabled:opacity-50"
        @click="close"
      >
        Close posting
      </button>
      <button
        type="button"
        :disabled="form.processing"
        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50"
        @click="save"
      >
        Save posting
      </button>
    </div>

    <teleport to="#destination">
      <ModalWithFooter v-model="confirmScopeClearing">
        <template #title>
          Remove the SSO requirement of {{ posting.corporation.name }}?
        </template>
        <template #description>
          Saving with nothing selected deletes the corporation's SSO requirement outright: the
          corporation leaves Observation and member compliance, no scope is asked of its applicants or
          members any more, and the configured level ({{ scopeTypeLabel }}) is lost — adding scopes
          back later starts again from the default level.
        </template>
        <template #buttons>
          <button
            type="button"
            class="inline-flex w-full justify-center rounded-md bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 sm:w-auto"
            @click="submit"
          >
            Remove and save
          </button>
        </template>
      </ModalWithFooter>
    </teleport>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import EveImage from "@/Shared/EveImage.vue";
import EsiMultiselect from "@/Shared/Components/EsiMultiselect.vue";
import Autosuggest from "@/Shared/Components/Autosuggest.vue";
import DismissibleButton from "@/Shared/Layout/Buttons/DismissibleButton.vue";
import CharacterScopes from "@/Pages/Configuration/Scopes/CharacterScopes.vue";
import CorporationScopes from "@/Pages/Configuration/Scopes/CorporationScopes.vue";
import ModalWithFooter from "@/Shared/Modals/ModalWithFooter.vue";

// How far a configured requirement reaches, in the words of Configuration -> Scopes.
const SCOPE_TYPE_LABELS = {
  default: "only the characters in this corporation",
  user: "every character of an account with a character in this corporation",
  global: "every character in this installation",
};

const props = defineProps({
  posting: {
    required: true,
    type: Object,
  },
  controlGroups: {
    required: true,
    type: Array,
  },
  availableScopes: {
    required: true,
    type: Object,
  },
});

// One form covers the whole posting configuration: its review stages, watchlist and required scopes.
const form = useForm({
  stages: props.posting.stages.map((stage) => ({
    label: stage.label,
    role_id: stage.role_id,
  })),
  systems: props.posting.watched.systems,
  regions: props.posting.watched.regions,
  items: props.posting.watched.items,
  selected_scopes: props.posting.required_scopes ?? [],
});

const closeForm = useForm({});

const watchlistOpen = ref(false);
const scopesOpen = ref(false);
const confirmScopeClearing = ref(false);
// Re-key the items autosuggest after each selection so it clears its input.
const itemsKey = ref(0);

// Surfaced on the (collapsed) toggle so a configured watchlist is visible at a glance.
const watchlistCount = computed(() => form.systems.length + form.regions.length + form.items.length);

// Re-key for the two scope pickers (see the template).
const scopesAsString = computed(() => form.selected_scopes.join(","));

const scopeTypeLabel = computed(
  () => SCOPE_TYPE_LABELS[props.posting.required_scopes_type] ?? "nothing yet — no requirement is configured",
);

// Only a save that removes an existing requirement is destructive; saving a corporation that never
// had one is not worth a modal.
const clearsRequiredScopes = computed(
  () => form.selected_scopes.length === 0 && (props.posting.required_scopes?.length ?? 0) > 0,
);

function addStage() {
  form.stages.push({ label: "", role_id: null });
}

function removeStage(index) {
  form.stages.splice(index, 1);
}

function addItem(selection) {
  if (!form.items.some((item) => item.id === selection.id)) {
    form.items.push(selection);
  }
  itemsKey.value++;
}

function removeItem(id) {
  form.items = form.items.filter((item) => item.id !== id);
}

function save() {
  if (clearsRequiredScopes.value) {
    confirmScopeClearing.value = true;

    return;
  }

  submit();
}

function submit() {
  confirmScopeClearing.value = false;

  form.put(props.posting.save_url, {
    preserveScroll: true,
  });
}

function close() {
  closeForm.delete(props.posting.close_url, {
    preserveScroll: true,
  });
}
</script>
