<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle" />

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          User Settings
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
          Personal details and application.
        </p>
      </div>
      <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200">
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Main Character
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <SelectComponent
                v-model:selected="selected"
                :options="options"
              />
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Characters
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                <li
                  v-for="character in user.data.characters"
                  :key="character.character_id"
                  class="pl-3 pr-4 py-3 flex items-center justify-between text-sm"
                >
                  <EntityBlock :entity="character" />
                </li>
              </ul>
            </dd>
          </div>
          <!--          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Application for
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              Backend Developer
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Email address
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              margotfoster@example.com
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Salary expectation
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              $120,000
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              About
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              Fugiat ipsum ipsum deserunt culpa aute sint do nostrud anim incididunt cillum culpa consequat. Excepteur qui ipsum aliquip consequat sint. Sit id mollit nulla mollit nostrud in ea officia proident. Irure nostrud pariatur mollit ad adipisicing reprehenderit deserunt qui eu.
            </dd>
          </div>
          <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Attachments
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                  <div class="w-0 flex-1 flex items-center">
                    &lt;!&ndash;                    <PaperClipIcon class="shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />&ndash;&gt;
                    <span class="ml-2 flex-1 w-0 truncate">
                      resume_back_end_developer.pdf
                    </span>
                  </div>
                  <div class="ml-4 shrink-0">
                    <a
                      href="#"
                      class="font-medium text-indigo-600 hover:text-indigo-500"
                    >
                      Download
                    </a>
                  </div>
                </li>
                <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                  <div class="w-0 flex-1 flex items-center">
                    &lt;!&ndash;                    <PaperClipIcon class="shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />&ndash;&gt;
                    <span class="ml-2 flex-1 w-0 truncate">
                      coverletter_back_end_developer.pdf
                    </span>
                  </div>
                  <div class="ml-4 shrink-0">
                    <a
                      href="#"
                      class="font-medium text-indigo-600 hover:text-indigo-500"
                    >
                      Download
                    </a>
                  </div>
                </li>
              </ul>
            </dd>
          </div>-->
        </dl>
      </div>
    </div>

    <Link
      :href="route('auth.logout')"
      as="button"
      class="inline-flex mx-auto w-full items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      Logout
    </Link>
  </div>
</template>

<script>
    import PageHeader from "@/Shared/Layout/PageHeader.vue";
    import SelectComponent from "@/Shared/Components/SelectComponent.vue";
    import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
    import {computed, ref, watch} from "vue";
    import { useForm, Link } from "@inertiajs/vue3";
    
    export default {
        name: "UserSettings",
        components: {EntityBlock, SelectComponent, PageHeader, Link},
        props: {
            user: {
                type: Object,
                required: true
            }
        },
        setup(props) {
            const selected = ref({
                character_id: _.get(props.user, 'data.main_character.character_id'),
                name: _.get(props.user, 'data.main_character.name'),
            })

            const form = useForm({
                character_id: _.get(props.user, 'data.main_character.character_id')
            })

            const options = computed(() => {
                let characters = _.get(props.user, 'data.characters', [])

                return _.map(characters, character => {
                    return {
                        character_id: character.character_id,
                        name: character.name
                    }
                })
            })

            watch(selected, () =>  {
                form.transform(() => ({
                    character_id: selected.value.character_id
                }))
                .post(route('change.main_character'))
            })

            return {
                selected,
                pageTitle: 'User Settings',
                form,
                options
            }
        }
    }
</script>

<style scoped>

</style>
