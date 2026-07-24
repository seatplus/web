<template>
  <InfiniteScroll
    data="mailHeaders"
    items-element="#mobile-mail-list"
    preserve-url
  >
    <ul
      id="mobile-mail-list"
      class="divide-y divide-gray-200"
    >
      <Disclosure
        v-for="mail in mailHeaders"
        :key="mail.id"
        v-slot="{open}"
        as="li"
      >
        <DisclosureButton class="flex w-full py-2 hover:bg-violet-200 focus:outline-hidden focus-visible:ring-3 focus-visible:ring-violet-500/75">
          <div class="flex w-full space-x-3">
            <EveImage
              :object="{character_id: mail.from}"
              tailwind_class="h-6 w-6 rounded-full"
            />
            <div class="flex-1 space-y-1">
              <div class="flex items-center justify-between">
                <div class="space-x-1 inline-flex">
                  <ResolveIdToName
                    :id="mail.from"
                    :tailwind-class="isSelected(mail) ? 'text-sm font-medium text-indigo-900' : 'text-sm font-medium'"
                  />
                  <Time
                    :timestamp="mail.timestamp"
                    :class="['text-sm', isSelected(mail) ? 'text-indigo-500' : 'text-gray-500']"
                  />
                </div>
                <ChevronUpIcon
                  :class="open ? 'transform rotate-180' : ''"
                  class="w-5 h-5"
                />
              </div>
              <p class="flex text-sm text-gray-500">
                {{ mail.subject }}
              </p>
            </div>
          </div>
        </DisclosureButton>
        <DisclosurePanel class="space-y-2">
          <MailRepresentation
            v-if="open"
            :key="mail.id"
            :mail-id="mail.id"
          />
        </DisclosurePanel>
      </Disclosure>
    </ul>
  </InfiniteScroll>
</template>

<script setup>
import MailRepresentation from "./MailRepresentation.vue";
import EveImage from "@/Shared/EveImage.vue"
import Time from "@/Shared/Time.vue";
import ResolveIdToName from "../../ResolveIdToName.vue";
import { InfiniteScroll, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { ChevronUpIcon } from "@heroicons/vue/20/solid";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";

const props = defineProps({
    selectedId: {
        type: Number,
        required: false,
        default: null
    }
});

defineEmits(['update:selectedId']);

const page = usePage()

const mailHeaders = computed(() => page.props.mailHeaders?.data ?? [])

const isSelected = (mail) => mail.id === props.selectedId
</script>

<style scoped>

</style>
