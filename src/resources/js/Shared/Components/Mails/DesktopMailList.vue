<template>
  <!--  py-6 px-4 sm:px-6 lg:px-8-->
  <div class="absolute inset-0 overflow-y-auto">
    <InfiniteLoadingHelper
      v-slot="{results}"
      route="get.mail.headers"
      :params="{character_ids: characterIds}"
    >
      <ul class="divide-y divide-gray-200">
        <li
          v-for="mail in mails(results)"
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
    </InfiniteLoadingHelper>
    <!--    <div class="h-full border-2 border-gray-200 border-dashed rounded-lg" />-->
  </div>
</template>

<script>
import {computed, ref} from "vue";
import InfiniteLoadingHelper from "../../InfiniteLoadingHelper";
import EveImage from "../../EveImage";
import Time from "../../Time";
import ResolveIdToName from "../../ResolveIdToName";

export default {
    name: "DesktopMailList",
    components: {ResolveIdToName, Time, EveImage, InfiniteLoadingHelper},
    props: {
        characterIds: {
            type: Array,
            required: true
        },
        selectedId: {
            required: false
        },
    },
    emits: ['update:selectedId'],
    setup(props, {emit}) {

        const emitSelection = (selectedId) => emit('update:selectedId', selectedId)

        const mails = (results) => {
            return _.map(results, result => {
                return {
                    ...result,
                    current: _.isEqual(props.selectedId, result.id)
                }
            })
        }

        return {
            mails,
            emitSelection,
        }
    }
}
</script>

<style scoped>

</style>