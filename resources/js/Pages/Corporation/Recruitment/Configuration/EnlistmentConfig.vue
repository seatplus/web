<template>
  <TwoColumnCardWithSubmitAction :with-bottom-border="withBottomBorder">
    <template #title>
      <span v-if="!enlistment">Create</span> Enlistment
    </template>
    <template #description>
      Select <span v-if="!enlistment">corporation and</span> type of enlistment. Additionally you can opt to support multiple review process steps.
    </template>
    <div class="space-y-6 sm:space-y-5">
      <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
        <label
          class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"
        >
          Corporation
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <Autosuggest
            v-if="!enlistment"
            route-name="get.affiliated.corporations"
            :route-parameters="{ permission: 'can open or close corporations for recruitment' }"
            placeholder="Search for corporation"
            @selectedObject="selection => form.corporation = selection"
          />
          <EntityByIdBlock
            v-else
            :id="enlistment.corporation_id"
            :image-size="8"
            name-font-size="md"
            :with-sub-text="false"
          />
          <div
            v-if="form.errors.corporation"
            id="solar_system-error"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.corporation.find(Boolean) }}
          </div>
        </div>
      </div>

      <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
        <label
          class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"
        >
          Typ
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2 space-y-2">
          <div class="flex flex-shrink-0 items-center space-x-2">
            <SimpleToggle v-model="isUserType" />
            <span class="text-sm font-medium text-gray-900">User Type</span>
          </div>
          <div class="flex flex-shrink-0 items-center space-x-2">
            <SimpleToggle v-model="isCharacterType" />
            <span class="text-sm font-medium text-gray-900">Character Type</span>
          </div>
        </div>
      </div>

      <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
        <label
          for="steps"
          class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"
        >
          Review Process Steps
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <input
            id="steps"
            v-model="form.steps"
            name="steps"
            type="text"
            class="block max-w-lg w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md"
          >
          <span class="text-sm text-gray-500">F.e. first and second review. Separate Steps by semi-column. Leave blank if you do not plan to use a multi step review process</span>
        </div>
      </div>
    </div>
    <template #button>
      <button
        :disabled="form.processing"
        type="submit"
        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        @click="submit()"
      >
        Save
      </button>
    </template>
  </TwoColumnCardWithSubmitAction>
</template>

<script>
import {useForm} from "@inertiajs/inertia-vue3";
import {computed} from "vue";
import SimpleToggle from "@/Shared/SimpleToggle.vue";
import Autosuggest from "@/Shared/Components/Autosuggest.vue";
import TwoColumnCardWithSubmitAction from "@/Shared/Layout/Forms/TwoColumnCardWithSubmitAction.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";

export default {
    name: "EnlistmentConfig",
    components: {EntityByIdBlock, TwoColumnCardWithSubmitAction, Autosuggest, SimpleToggle},
    props: {
        enlistment: {
            required: false,
            type: Object,
            default: null
        },
        withBottomBorder: {
            required: false,
            type: Boolean,
            default: false
        }
    },
    emits: ['onSuccess'],
    setup(props, { emit }) {
        const form = useForm({
            corporation: props.enlistment,
            type: _.get(props, 'enlistment.type', 'user'),
            steps: _.get(props, 'enlistment.steps', [], '').join('; ')
        })

        const submit = () => {
            form
                .transform((data) => ({ ...data, corporation_id: data.corporation.corporation_id}))
                .post(route('create.corporation.recruitment'), {
                onSuccess: () => {
                    emit('onSuccess')
                }
            })
        }

        const isUserType = computed({
            get() {
                return form.type === 'user'
            },
            set(newValue) {
                form.type = newValue ? 'user' : 'character'
            }
        })

        const isCharacterType = computed({
            get() {
                return form.type === 'character'
            },
            set(newValue) {
                form.type = newValue ? 'character' : 'user'
            }
        })

        return {
            open,
            form,
            isUserType,
            isCharacterType,
            submit
        }
    }
}
</script>

<style scoped>

</style>