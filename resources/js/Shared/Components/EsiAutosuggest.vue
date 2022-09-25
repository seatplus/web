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
          <ListboxLabel
            v-if="label"
            class="block text-sm font-medium text-gray-700"
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
                  <div class="flex-shrink-0">
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
                        :href="route('enable_esi_search')"
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
          @click="toggle"
        />
        <ListboxOptions
          static
          class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        >
          <ListboxOption
            v-for="option in options"
            :key="option"
            v-slot="{ selected }"
            :value="option"
            class="text-gray-900 hover:text-white hover:bg-indigo-600 cursor-default select-none relative py-2 pl-8 pr-4"
          >
            <EntityBlock
              v-if="option.has_image"
              :entity="option"
              class="block truncate"
              :image-size="5"
              :name-class="selected ? 'font-semibold' : 'font-medium' + ' ' + 'text-sm leading 6 text-gray-900'"
            />
            <div v-else>
              {{ option.name }}
            </div>
            <span
              v-show="selected"
              class="absolute inset-y-0 left-0 flex items-center pl-1.5"
            >
              <CheckIcon class="h-5 w-5" />
            </span>
            <ListboxOption />
          </listboxoption>
        </ListboxOptions>
      </div>
    </listbox>
  </div>
</template>

<script setup>
import {CheckIcon, ExclamationTriangleIcon} from '@heroicons/vue/20/solid';
import { Link } from '@inertiajs/inertia-vue3'
import {
    Listbox,
    ListboxOptions,
    ListboxOption,
    ListboxLabel, TransitionRoot
} from '@headlessui/vue'
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import {computed, ref, watch, watchEffect} from "vue";
import InputWithValidation from "@/Shared/Layout/Forms/InputWithValidation.vue";

const open = ref(false);
const term = ref('');
const selected = ref(null);
const suggestions = ref([]);

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
})

const emit = defineEmits(['selected', 'selectedObject'])

const toggle = () => {
    if (options.value.length > 0)
        open.value = !open.value
}

const getStuggestions = async () => {

    // In case of a select, the query gets updated, we need to prevent the suggestions from showing again.
    if (term.value === _.get(selected.value, 'name')) {
        return;
    }

    if (term.value.length < 3) {
        return;
    }

    await axios.get(route('autosuggestion.search', {search: term.value, categories: props.categories}))
        .then((result) => {
            suggestions.value = result.data

            // if previously the suggestions were not shown toggle them
            if (!open.value)
                toggle()
        }).catch((error) => {
            console.log(error)
        })
}

const checkToken = async () => {

    // If hasToken is null, we don't know yet if the user has a token
    if (_.isNull(hasToken.value)) {
        // check if the user has a token with required scope
        await axios.get(route('autosuggestion.token'))
            .then(response => {
                // if the user has a token, set hasToken to true
                // we don't need to check again
                // we expect the response to be a 1 or 0 and turn it into a boolean
                hasToken.value = !!response.data;
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
    term.value = props.resetAfterSelect ? '' : _.get(newValue, 'name')
    open.value = false
    emit('selected', _.get(newValue, 'id'))
    emit('selectedObject', newValue)

})

const options = computed(() => {
    return _.isArray(suggestions.value) ? suggestions.value : _.get(suggestions.value, 'data', [])
})

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


