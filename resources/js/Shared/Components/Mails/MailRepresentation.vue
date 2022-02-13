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
            <div class="grid grid-cols-4 gap-1">
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
      </dl>
    </div>
  </div>
</template>

<script>
import {onBeforeMount, ref} from "vue";
import route from 'ziggy'
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import Time from "@/Shared/Time";

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