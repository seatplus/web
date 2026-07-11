<template>
  <!-- scroll-region: lets Inertia preserve this container's scroll position so
       <InfiniteScroll> merges the next page in place instead of jumping to top. -->
  <div
    class="absolute inset-0 overflow-y-auto"
    scroll-region=""
  >
    <InfiniteScroll
      data="mailHeaders"
      items-element="#desktop-mail-list"
      preserve-url
    >
      <ul
        id="desktop-mail-list"
        class="divide-y divide-gray-200"
      >
        <li
          v-for="mail in mails"
          :key="mail.id"
          :class="[mail.current ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'flex space-x-3 cursor-pointer' ,'py-6 px-4 sm:px-6 lg:px-8 rounded-md ml-1 mr-1']"
          @click="emitSelection(mail.id)"
        >
          <EveImage
            :object="{character_id: mail.from}"
            tailwind_class="h-6 w-6 rounded-full"
          />
          <div class="flex-1 space-y-1">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-medium">
                <ResolveIdToName
                  :id="mail.from"
                  :tailwind-class="mail.current ? 'text-gray-900 text-sm font-medium' : 'text-gray-600 text-sm font-medium'"
                />
              </h3>
              <Time
                :timestamp="mail.timestamp"
                :class="['text-sm', mail.current ? 'text-gray-800' : 'text-gray-500']"
              />
            </div>
            <p :class="['text-sm', mail.current ? 'text-gray-800' : 'text-gray-500']">
              {{ mail.subject }}
            </p>
          </div>
        </li>
      </ul>
    </InfiniteScroll>
  </div>
</template>

<script>
import { InfiniteScroll, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import EveImage from "@/Shared/EveImage.vue"
import Time from "@/Shared/Time.vue";
import ResolveIdToName from "../../ResolveIdToName.vue";

export default {
    name: "DesktopMailList",
    components: {ResolveIdToName, Time, EveImage, InfiniteScroll},
    props: {
        selectedId: {
            required: false
        },
    },
    emits: ['update:selectedId'],
    setup(props, {emit}) {

        const page = usePage()

        const emitSelection = (selectedId) => emit('update:selectedId', selectedId)

        // Mail headers arrive as the page scroll prop; flag the selected one.
        const mails = computed(() => _.map(page.props.mailHeaders?.data ?? [], result => ({
            ...result,
            current: _.isEqual(props.selectedId, result.id),
        })))

        return {
            mails,
            emitSelection,
        }
    }
}
</script>

<style scoped>

</style>
