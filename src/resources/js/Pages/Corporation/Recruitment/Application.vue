<template>
  <div class="space-y-3">
    <PageHeader :breadcrumbs="[{name: 'Recruitment', route: 'corporation.recruitment'}]">
      User Application
      <template #primary>
        <HeaderButton
          v-if="recruit.id"
          :secondary="true"
          @click="impersonate"
        >
          Impersonate
        </HeaderButton>
      </template>
    </PageHeader>


    <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <li class="col-span-2">
        <TabComponent
          :recruit="recruit"
          :watchlist="watchlist"
          :target-corporation="target_corporation"
        />
      </li>

      <li class="col-span-1">
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
              <!-- Heroicon name: check -->
              <svg
                class="h-6 w-6 text-indigo-600"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"
                />
              </svg>
            </div>
            <div class="mt-3 sm:mt-5">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Application of {{ recruit.main_character.name }}
              </h3>
              <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                This will decide if one or all of the following characters are allowed to join the corporation: {{ characters }}
              </p>
              <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                Remember to invite them in game as well.
              </p>
              <UpdateCharacterComponent
                v-for="character in recruit.characters"
                :key="character.character_id"
                :character="character"
              />
              <!--Decision-->
              <div class="mt-6 sm:mt-5 sm:border-t sm:border-gray-200 sm:pt-5">
                <div
                  role="group"
                  aria-labelledby="label-notifications"
                >
                  <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
                    <div>
                      <div
                        id="label-notifications"
                        class="text-base leading-6 font-medium text-gray-900 sm:text-sm sm:leading-5 sm:text-gray-700"
                      >
                        Decision
                      </div>
                    </div>
                    <div class="sm:col-span-2">
                      <div class="max-w-lg">
                        <p class="text-sm leading-5 text-gray-500">
                          Decide if the recruit should be accepted to corporation or not.
                        </p>
                        <div class="mt-4">
                          <div class="flex items-center">
                            <input
                              id="accept_application"
                              v-model="form.decision"
                              value="accepted"
                              name="accept_application"
                              type="radio"
                              class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                            >
                            <label
                              for="accept_application"
                              class="ml-3"
                            >
                              <span class="block text-sm leading-5 font-medium text-gray-700">Accept application</span>
                            </label>
                          </div>
                          <div class="mt-4 flex items-center">
                            <input
                              id="reject_application"
                              v-model="form.decision"
                              value="rejected"
                              name="reject_application"
                              type="radio"
                              class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                            >
                            <label
                              for="reject_application"
                              class="ml-3"
                            >
                              <span class="block text-sm leading-5 font-medium text-gray-700">Reject application</span>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--Explanation-->
              <div
                v-if="form.decision === 'rejected' "
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
              >
                <label
                  for="explanation"
                  class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                >
                  Explanation
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <div class="max-w-lg flex rounded-md shadow-sm">
                    <textarea
                      id="explanation"
                      v-model="form.explanation"
                      rows="3"
                      class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                      required
                    />
                  </div>
                  <p
                    v-if="$page.props.errors.explanation"
                    class="mt-2 text-sm text-red-600"
                  >
                    {{ $page.errors.explanation[0] }}
                  </p>
                  <p class="mt-2 text-sm text-gray-500">
                    Write a few sentences about the decision, in that recruiters in the future might learn from past decisions.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-6">
            <span class="flex w-full rounded-md shadow-sm">
              <button
                type="button"
                class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-indigo-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                @click="submit"
              >
                Submit review
              </button>
            </span>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import HeaderButton from "@/Shared/Layout/HeaderButton";
import TabComponent from "./TabComponent";
import LeftAlignedData from "../../../Shared/Layout/DataDisplay/LeftAlignedData";
import Time from "../../../Shared/Time";
import Button from "../../../Shared/Layout/Button";
import UpdateCharacterComponent from "./UpdateCharacterComponent";

export default {
    name: "Application",
    components: {
        UpdateCharacterComponent,
        Button,
        Time,
        LeftAlignedData,
        TabComponent,
        HeaderButton,
        PageHeader
    },
    props: {
        recruit: {
            required: true,
            type: Object
        },
        target_corporation: {
            required: true,
            type: Object
        },
        watchlist: {
            required: true,
            type: Object
        },
        activeSidebarElement: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            pageTitle: 'Application',
            form: {
                decision: null,
                explanation: null
            }
        }
    },
    computed: {
        characters() {
            return _.map(this.recruit.characters, (character) => character.name ).join(', ')
        },

    },
    methods: {
        impersonate() {
            return this.$inertia.visit(this.$route('impersonate.recruit', this.recruit.application_id))
        },
        submit() {

            if(this.recruit.id)
                return this.$inertia.post(this.$route('review.application', this.recruit.application_id), this.form);

            this.$inertia.post(this.$route('review.character.application', this.recruit.main_character.character_id), this.form);
        }
    }
}
</script>

<style scoped>

</style>
