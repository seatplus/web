<template>
  <section
    aria-labelledby="activity-title"
    class="mt-8 xl:mt-10"
  >
    <div>
      <div class="divide-y divide-gray-200">
        <div
          v-if="withHeader"
          class="pb-4"
        >
          <h2
            id="activity-title"
            class="text-lg font-medium text-gray-900"
          >
            Activity Log
          </h2>
        </div>
        <div class="pt-6">
          <!-- Activity feed-->
          <div class="flow-root">
            <ul
              role="list"
              class="-mb-8"
            >
              <li
                v-for="(item, itemIdx) in activity"
                :key="item.id"
              >
                <div class="relative pb-8">
                  <span
                    v-if="(itemIdx !== activity.length - 1)"
                    class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                    aria-hidden="true"
                  />
                  <div class="relative flex items-start space-x-3">
                    <template v-if="item.type === 'comment'">
                      <div class="relative">
                        <EveImage
                          :object="item.character"
                          :size="64"
                          tailwind_class="h-10 w-10 rounded-full bg-gray-100 rounded-full ring-8 ring-white flex items-center justify-center"
                        />

                        <span class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px">
                          <ChatAltIcon
                            class="h-5 w-5 text-gray-400"
                            aria-hidden="true"
                          />
                        </span>
                      </div>
                      <div class="min-w-0 flex-1">
                        <div>
                          <div class="text-sm font-medium text-gray-900">
                            {{ item.character.name }}
                          </div>
                          <p class="mt-0.5 text-sm text-gray-500">
                            Commented <Time :timestamp="item.created_at" />
                          </p>
                        </div>
                        <div class="mt-2 text-sm text-gray-700">
                          <p>
                            {{ item.comment }}
                          </p>
                        </div>
                      </div>
                    </template>
                    <template v-else-if="item.type === 'decision'">
                      <div class="relative px-1">
                        <EveImage
                          :object="item.character"
                          :size="8"
                          tailwind_class="h-8 w-8 bg-gray-100 rounded-full ring-8 ring-white flex items-center justify-center"
                        />
                      </div>
                      <div class="min-w-0 flex flex-wrap items-baseline gap-x-1.5 py-1.5">
                        <p class="font-medium text-gray-900">
                          {{ item.character.name }}
                        </p>
                        <p class="text-sm text-gray-500">
                          {{ item.comment }}
                        </p>
                        <Time
                          class="whitespace-nowrap text-sm text-gray-900"
                          :timestamp="item.created_at"
                        />
                      </div>
                    </template>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div class="mt-6">
            <form @submit.prevent="form.put($route('comment.application', application.id),{ onSuccess: () => form.reset('comment') })">
              <div class="flex space-x-3">
                <div class="flex-shrink-0">
                  <div class="relative">
                    <EveImage
                      :object="user.main_character"
                      :size="64"
                      tailwind_class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white"
                    />

                    <span class="absolute -bottom-0.5 -right-1 bg-white rounded-tl px-0.5 py-px">
                      <ChatAltIcon
                        class="h-5 w-5 text-gray-400"
                        aria-hidden="true"
                      />
                    </span>
                  </div>
                </div>
                <div class="min-w-0 flex-1">
                  <div>
                    <label
                      for="comment"
                      class="sr-only"
                    >Comment</label>
                    <textarea
                      id="comment"
                      v-model="form.comment"
                      name="comment"
                      rows="3"
                      class="shadow-sm block w-full focus:ring-gray-900 focus:border-gray-900 sm:text-sm border border-gray-300 rounded-md"
                      placeholder="Leave a comment"
                    />
                    <div v-if="form.errors.comment">
                      {{ form.errors.comment }}
                    </div>
                  </div>
                  <div class="mt-6 flex items-center justify-end space-x-4">
                    <button
                      type="submit"
                      :disabled="form.processing"
                      class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                    >
                      Comment
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { ChatAltIcon } from '@heroicons/vue/solid'
import { computed } from "vue";
import EveImage from "@/Shared/EveImage";
import Time from "@/Shared/Time";
import {useForm, usePage} from "@inertiajs/inertia-vue3";

export default {
    name: "LogTab",
    components: {Time, EveImage, ChatAltIcon },
    props: {
        application: {
            required: true,
            type: Object
        },
        withHeader: {
            required: false,
            type: Boolean,
            default: true
        }
    },
    setup(props) {

        const form = useForm({
            comment: null
        })

        const isFinalDecision = (index) => {
            return  _.filter(props.application.log_entries, {type: 'decision'}).length === (index+1)
        }

        const activity = computed(() => {

            let activity = [
                {
                    type: 'decision',
                    character: _.has(props.application, 'applicationable.main_character') ? _.get(props.application, 'applicationable.main_character') : _.get(props.application, 'applicationable'),
                    comment: 'has applied',
                    created_at: props.application.created_at
                }
            ]

            _.each(props.application.log_entries, (entry, entryIdx) => {
                activity.push({
                    type: 'comment',
                    character: entry.causer.main_character,
                    comment: entry.comment,
                    created_at: entry.created_at
                })

                if(entry.type === 'decision') {
                    activity.push({
                        type: 'decision',
                        character: entry.causer.main_character,
                        comment: isFinalDecision(entryIdx) ? `${props.application.status} the application` :  'approved step',
                        created_at: entry.created_at
                    })
                }

            })

            return activity
        })
        const user = usePage().props.value.user.data

        return {
            activity,
            user,
            form
        }
    }
}
</script>

<style scoped>

</style>