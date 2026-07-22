<template>
  <div>
    <Listbox
      v-model="selected"
      as="div"
    >
      <InputWithValidation
        v-model="term"
        :label="label"
        :placeholder="placeholder"
        :warning="showWarning ? 'No results found' : ''"
        @keydown.delete="handleBackspace"
      >
        <template #label>
          <!-- Always render (keeps InputWithValidation from falling back to its own label, which
               would duplicate a parent-provided field label); sr-only hides it visually. -->
          <ListboxLabel
            v-if="label"
            :class="showLabel ? 'block text-sm font-medium text-gray-700' : 'sr-only'"
          >
            {{ label }}
          </ListboxLabel>
        </template>

        <template #description>
          <TransitionRoot
            :show="showWarning"
            enter="transition-opacity duration-75"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="transition-opacity duration-150"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <div class="mt-2 text-sm">
              <div
                class="border-l-4 border-yellow-400 bg-yellow-50 p-4"
              >
                <div class="flex">
                  <div class="shrink-0">
                    <ExclamationTriangleIcon
                      class="h-5 w-5 text-yellow-400"
                      aria-hidden="true"
                    />
                  </div>
                  <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                      You have no character refresh token with required scope.
                      {{ ' ' }}
                      <Link
                        :href="enableEsiSearchUrl"
                        class="font-medium text-yellow-700 underline hover:text-yellow-600"
                      >
                        Upgrade one token to be able to use this search.
                      </Link>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </TransitionRoot>
        </template>
      </InputWithValidation>
      <div v-show="open">
        <div
          class="absolute inset-0 bg-transparent"
          @click="close"
        />
        <ListboxOptions
          static
          class="relative z-10 mt-1 max-h-60 overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-hidden sm:text-sm"
        >
          <!-- Loading: pulsing skeleton rows while the search runs -->
          <template v-if="loading">
            <div class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-400">
              <span class="h-2 w-2 animate-pulse rounded-full bg-indigo-500" />
              Searching…
            </div>
            <div
              v-for="n in 3"
              :key="`skeleton-${n}`"
              class="flex items-center gap-3 px-3 py-2"
            >
              <div class="h-5 w-5 animate-pulse rounded-full bg-gray-200" />
              <div class="h-3 w-2/3 animate-pulse rounded bg-gray-200" />
            </div>
          </template>

          <!-- Results grouped by category -->
          <template v-else>
            <div
              v-for="group in groupedOptions"
              :key="group.category"
            >
              <div class="bg-gray-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-500">
                {{ categoryLabel(group.category) }}
              </div>
              <ListboxOption
                v-for="option in group.items"
                :key="option.id"
                v-slot="{ active, selected: isSelected }"
                :value="option"
                as="template"
              >
                <li
                  class="relative cursor-default select-none py-2 pl-8 pr-4"
                  :class="active ? 'bg-indigo-600 text-white' : 'text-gray-900'"
                >
                  <EntityBlock
                    v-if="option.has_image"
                    :entity="option"
                    class="block truncate"
                    :image-size="5"
                    :name-class="isSelected ? 'font-semibold' : 'font-medium'"
                  />
                  <div v-else>
                    {{ option.name }}
                  </div>
                  <span
                    v-show="isSelected"
                    class="absolute inset-y-0 left-0 flex items-center pl-1.5"
                  >
                    <CheckIcon class="h-5 w-5" />
                  </span>
                </li>
              </ListboxOption>
            </div>

            <div
              v-if="! groupedOptions.length"
              class="px-3 py-2 text-sm text-gray-400"
            >
              No results found
            </div>
          </template>
        </ListboxOptions>
      </div>
    </Listbox>
  </div>
</template>

<script setup>
import {CheckIcon, ExclamationTriangleIcon} from '@heroicons/vue/20/solid';
import { Link } from '@inertiajs/vue3';
import {
    Listbox,
    ListboxOptions,
    ListboxOption,
    ListboxLabel, TransitionRoot
} from '@headlessui/vue'
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import {computed, ref, watch, watchEffect} from "vue";
import InputWithValidation from "@/Shared/Layout/Forms/InputWithValidation.vue";
import { getJson } from "@/Functions/http";
import EnableEsiSearchController from "@/actions/Seatplus/Web/Http/Controllers/Shared/EnableEsiSearchController";
import { esiSearch, token } from "@/actions/Seatplus/Web/Http/Controllers/Shared/HelperController";

const enableEsiSearchUrl = EnableEsiSearchController.url();

const open = ref(false);
const term = ref('');
const selected = ref(null);
const suggestions = ref([]);
const loading = ref(false);

const hasToken = ref(null);

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    label: {
        type: String,
        required: false,
        default: 'EsiSuggest'
    },
    placeholder: {
        type: String,
        required: false,
        default: () => {}
    },
    resetAfterSelect: {
        type: Boolean,
        required: false,
        default: false
    },
    // Render the visible label. Set false when a parent already renders its own field label but a
    // (non-empty) label is still needed for the input's id/name — avoids duplicate labels and the
    // empty-id `getElementById("")` warnings that an empty label would trigger.
    showLabel: {
        type: Boolean,
        required: false,
        default: true
    },
})

const emit = defineEmits(['selected', 'selectedObject'])

const close = () => {
    open.value = false
}

const getStuggestions = async () => {

    // In case of a select, the query gets updated, we need to prevent the suggestions from showing again.
    if (term.value === _.get(selected.value, 'name')) {
        return;
    }

    if (term.value.length < 3) {
        open.value = false;
        return;
    }

    // Open the dropdown immediately and show the loading state while the request runs.
    loading.value = true;
    open.value = true;

    await getJson(esiSearch.url({ query: { search: term.value, categories: props.categories } }))
        .then((result) => {
            suggestions.value = result
        }).catch((error) => {
            console.log(error)
        }).finally(() => {
            loading.value = false
        })
}

const checkToken = async () => {

    // If hasToken is null, we don't know yet if the user has a token
    if (_.isNull(hasToken.value)) {
        // check if the user has a token with required scope
        await getJson(token.url())
            .then(response => {
                // if the user has a token, set hasToken to true
                // we don't need to check again
                // we expect the response to be a 1 or 0 and turn it into a boolean
                hasToken.value = !!response;
            }).catch(error => {
                console.log(error)
            })
    }
}

watchEffect(async () => {

    if (term.value === undefined) {
        return;
    }

    if (hasToken.value === false) {
        return;
    }

    await checkToken();

    if (hasToken.value === false) {
        return;
    }

    await getStuggestions();
})

watch(selected, (newValue) => {
    if (! newValue) {
        return;
    }

    emit('selected', _.get(newValue, 'id'))
    emit('selectedObject', newValue)
    open.value = false

    if (props.resetAfterSelect) {
        // The parent renders the choice as a pill, so clear the query for the next search
        // and drop the internal selection to allow re-picking the same entity later.
        suggestions.value = []
        term.value = ''
        selected.value = null

        return;
    }

    // Single-select callers keep the chosen name in the box.
    term.value = _.get(newValue, 'name')
})

const options = computed(() => {
    return _.isArray(suggestions.value) ? suggestions.value : _.get(suggestions.value, 'data', [])
})

const groupedOptions = computed(() => {
    const groups = {};

    for (const option of options.value) {
        const category = option.category ?? 'other';
        (groups[category] ??= []).push(option);
    }

    return Object.entries(groups).map(([category, items]) => ({category, items}));
})

const categoryLabels = {
    character: 'Characters',
    corporation: 'Corporations',
    alliance: 'Alliances',
    type: 'Types',
};

const categoryLabel = (category) => categoryLabels[category] ?? _.upperFirst(category);

const showWarning = computed(() => {

    if (term.value.length < 1) {
        return false;
    }

    return !_.isNull(hasToken.value) && !hasToken.value
})

const handleBackspace = () => {
    if (term.value.length > 2)
        return;

    open.value = false
    suggestions.value = []
    selected.value = null
    emit('selected', null)
    emit('selectedObject', null)
}

</script>
