<template>
  <div
    v-for="message in messages"
    :key="message.timestamp"
    class="bg-white shadow overflow-hidden sm:rounded-lg"
  >
    <div class="px-4 py-5 sm:px-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        {{ message.subject }}
      </h3>
      <div
        v-if="message.labels"
        class="mt-1 space-x-1"
      >
        <span
          v-for="label in message.labels"
          :key="label"
          :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', {
            'bg-indigo-100 text-indigo-800': label.color === '#0000fe',
            'bg-emerald-100 text-emerald-800': label.color === '#006634',
            'bg-blue-100 text-blue-800': label.color === '#0099ff',
            'bg-lime-100 text-lime-800': label.color === '#00ff33',
            'bg-sky-100 text-sky-800': label.color === '#01ffff',
            'bg-green-100 text-green-800': label.color === '#349800',
            'bg-fuchsia-100 text-fuchsia-800': label.color === '#660066',
            'bg-gray-100 text-gray-800': label.color === '#666666',
            'bg-warmGray-100 text-warmGray-800': label.color === '#999999',
            'bg-cyan-100 text-cyan-800': label.color === '#99ffff',
            'bg-rose-100 text-rose-800': label.color === '#9a0000',
            'bg-teal-100 text-teal-800': label.color === '#ccff9a',
            'bg-coolGray-100 text-coolGray-800': label.color === '#e6e6e6',
            'bg-red-100 text-red-800': label.color === '#fe0000',
            'bg-orange-100 text-orange-800': label.color === '#ff6600',
            'bg-yellow-100 text-yellow-800': label.color === '#ffff01',
            'bg-trueGray-100 text-trueGray-800': label.color === '#ffffcd',
            'bg-blueGray-100 text-blueGray-800': label.color === '#ffffff',
          }]"
        >
          {{ label.name }}
        </span>
      </div>
    </div>
    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
      <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-500">
            From
          </dt>
          <dd class="mt-1 text-sm text-gray-900">
            <EntityByIdBlock
              :id="message.from.id"
              name-font-size="md"
              :image-size="8"
            />
          </dd>
        </div>
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-500">
            Received
          </dt>
          <dd class="mt-1 text-sm text-gray-900">
            {{ new Date(message.timestamp).toLocaleString('en-GB', { timeZone: 'UTC' }) }} - <Time :timestamp="message.timestamp" />
          </dd>
        </div>
        <div class="sm:col-span-2">
          <dt class="text-sm font-medium text-gray-500">
            Recipients
          </dt>
          <dd class="mt-1 text-sm text-gray-900">
            <div class="flex space-x-1">
              <EntityByIdBlock
                v-for="recipient in message.recipients"
                :id="recipient.id"
                :key="recipient.id"
                name-font-size="md"
                :image-size="8"
              />
            </div>
          </dd>
        </div>
        <div class="sm:col-span-2">
          <dt class="text-sm font-medium text-gray-500">
            Message
          </dt>
          <dd
            class="mt-1 text-sm text-gray-900"
            v-html="message.body"
          />
        </div>
        <!--        <div class="sm:col-span-2">
          <dt class="text-sm font-medium text-gray-500">
            Attachments
          </dt>
          <dd class="mt-1 text-sm text-gray-900">
            <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
              <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                <div class="w-0 flex-1 flex items-center">
                  <PaperClipIcon class="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />
                  <span class="ml-2 flex-1 w-0 truncate">
                    resume_back_end_developer.pdf
                  </span>
                </div>
                <div class="ml-4 flex-shrink-0">
                  <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Download
                  </a>
                </div>
              </li>
              <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                <div class="w-0 flex-1 flex items-center">
                  <PaperClipIcon class="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />
                  <span class="ml-2 flex-1 w-0 truncate">
                    coverletter_back_end_developer.pdf
                  </span>
                </div>
                <div class="ml-4 flex-shrink-0">
                  <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
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
</template>

<script>
import {onBeforeMount, ref} from "vue";
import route from 'ziggy'
import EntityByIdBlock from "../../Layout/Eve/EntityByIdBlock";
import Time from "../../Time";

export default {
    name: "MailRepresentation",
    components: {Time, EntityByIdBlock},
    props: {
        mailId: {
            type: Number,
            required: true
        }
    },
    setup(props) {
        const messages = ref([])

        const fetchMails = async () => {

            await axios.get(route('get.mail', props.mailId))
                .then(response => {
                    messages.value.push(...response.data);
                })
                .catch(error => console.log(error))
        }

        onBeforeMount(() => fetchMails())

        return {
            messages
        }
    }
}
</script>

<style scoped>

</style>