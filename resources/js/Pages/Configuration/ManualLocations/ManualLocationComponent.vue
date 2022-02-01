<template>
  <div>
    <div :class="{'mt-10 sm:mt-0' : index>0}">
      <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              {{ currentSelected }}
            </h3>
            <p class="mt-1 text-sm text-gray-600">
              This location could not be resolved (yet) via ESI available data. For such unknown structures
              users are free to submit suggestions. Please review the suggestions to the right and
            </p>
          </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
          <div class="shadow sm:rounded-md sm:overflow-hidden">
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
              <RadioListWithDescription
                :key="location.location_id"
                v-model="form.id"
                :options="options"
              />
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
              <button
                :disabled="form.processing"
                type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click="submit"
              >
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--     border -->
    <div
      class="hidden sm:block"
      aria-hidden="true"
    >
      <div class="py-5">
        <div class="border-t border-gray-200" />
      </div>
    </div>
  </div>
</template>

<script>
import RadioListWithDescription from "../../../Shared/Layout/RadioListWithDescription";
import dayjs from "dayjs"
import customParseFormat from "dayjs/plugin/customParseFormat"
import relativeTime from "dayjs/plugin/relativeTime"

dayjs.extend(customParseFormat);
dayjs.extend(relativeTime)
export default {
    name: "ManualLocationComponent",
    components: {RadioListWithDescription},
    props: {
        index: {
            required: true,
            type: Number
        },
        location: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                id: null,
                location_id: this.location.location_id
            })
        }
    },
    computed: {
        options() {
            return _.map(this.location.data, suggestion => {
                let main_character = _.get(suggestion, 'user.main_character.name')
                let characters = _.map(
                    (main_character
                            ? _.filter(_.get(suggestion, 'user.characters', []), character => character.name !== main_character)
                            : _.get(suggestion, 'user.characters', [])
                    ) , character => character.name).join(', ')
                let timeFromNow = dayjs(suggestion.created_at).fromNow()

                return {
                    id: suggestion.id,
                    title: _.get(suggestion, 'system.name', '?') + ' - ' + suggestion.name,
                    description: `Submitted by ${main_character} (${characters}) ${timeFromNow}`
                }
            })
        },
        currentSelected() {
            const current = _.head(_.filter(this.location.data, 'selected'))

            if (_.isUndefined(current))
                return `Unknown (${this.form.location_id})`

            let current_system = _.get(current, 'system.name', '?');

            return `${current_system} - ${current.name} (${current.location_id})`
        }
    },
    methods: {
        submit() {
            let self = this;

            return this.form.post(this.$route('accept.manuel_locations'), {
                onSuccess: () => {
                    self.$emit('onSubmittedSuggestion')
                }
            })


        }
    }
}
</script>

<style scoped>

</style>
