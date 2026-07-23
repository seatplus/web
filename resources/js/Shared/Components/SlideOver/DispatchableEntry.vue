<template>
  <li
    :class="['px-6 py-5 relative', {'cursor-pointer': status === 'ready'}]"
    @click="dispatchJob"
  >
    <div class="group flex justify-between items-center space-x-2">
      <div class="-m-1 p-1 block">
        <span class="absolute inset-0 group-hover:bg-gray-50" />
        <div class="flex-1 flex items-center min-w-0 relative">
          <EveImage
            :object="entry"
            :size="256"
            tailwind_class="h-10 w-10 rounded-full"
          />
          <div class="ml-4 truncate">
            <div class="text-sm leading-5 font-medium text-gray-900 truncate">
              {{ entry.name }}
            </div>
            <div class="text-sm leading-5 text-gray-500 truncate">
              <span v-if="status === 'ready'">job can be dispatched</span>
              <span v-if="['pending', 'finished', 'failures'].includes(status)">
                <Time
                  v-if="time"
                  :timestamp="time"
                />
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="relative inline-block text-left">
        <PlayIcon
          v-if="status === 'ready'"
          class="h-8 w-8 text-gray-400"
        />
        <ArrowPathIcon
          v-if="status === 'pending'"
          class="h-8 w-8 text-amber-400 animate-spin"
        />
        <CheckCircleIcon
          v-if="status === 'finished'"
          class="h-8 w-8 text-emerald-400"
        />
        <XCircleIcon
          v-if="status === 'failures'"
          class="h-8 w-8 text-red-400"
        />
      </div>
    </div>
  </li>
</template>

<script setup>
import EveImage from "@/Shared/EveImage.vue"
import Time from "@/Shared/Time.vue";
import { PlayIcon, ArrowPathIcon, CheckCircleIcon, XCircleIcon} from "@heroicons/vue/24/outline"
import { computed, onBeforeMount, onUnmounted, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import { getJson, post } from "@/Functions/http";
import { getBatchStatus, dispatch } from "@/actions/Seatplus/Web/Http/Controllers/Queue/DispatchJobController";

const props = defineProps({
    entry: {
        type: Object,
        required: true
    }
});

const status = ref(_.get(props.entry, 'batch.state'))
const batch_id = ref(_.get(props.entry, 'batch.batch_id'))
const updateStatus = ref()
const dispatch_transfer_object = computed(() => usePage().props.dispatchTransferObject)
const time = computed(() => _.get(props.entry, 'batch.time'))

async function getStatus() {
    const result = await getJson(getBatchStatus.url(batch_id.value))
    status.value = result.state
}

onBeforeMount(() => {
    if(batch_id.value)
        getStatus()
})

watch(status, (newValue, oldValue) => {
    if(newValue === 'pending')
        updateStatus.value = setInterval(getStatus,1000);
    if(oldValue === 'pending')
        clearInterval(updateStatus.value)
})

onUnmounted(() => {
    if (updateStatus.value)
        clearInterval(updateStatus.value)
})

const dispatchJob = async () => {
    try {
        const response = await post(dispatch.url(), {
            character_id: props.entry.character_id,
            corporation_id: props.entry.corporation_id,
            dispatch_transfer_object: dispatch_transfer_object.value,
        })

        batch_id.value = (await response.text()).trim()
        status.value = 'pending'
        getStatus()
    } catch (error) {
        console.error(error)
    }
}
</script>

<style scoped>

</style>