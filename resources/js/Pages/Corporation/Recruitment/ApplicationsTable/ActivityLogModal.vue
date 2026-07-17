<template>
  <div class="flex justify-end">
    <Button
      :is-inertia-button="false"
      button-size="xs"
      @click="open = true"
    >
      Activity Log
    </Button>
  </div>
  <teleport to="#destination">
    <WithDismissButtonModal
      v-model="open"
      width="2xl"
    >
      <DialogTitle as="h3">
        Activity Log
      </DialogTitle>
      <LogTab
        v-if="isLoaded"
        :application="application"
        :with-header="false"
      />
    </WithDismissButtonModal>
  </teleport>
</template>

<script>
import WithDismissButtonModal from "@/Shared/Modals/WithDismissButtonModal.vue";
import {computed, ref, watchEffect} from "vue";
import LogTab from "@/Pages/Corporation/Recruitment/Tabs/LogTab.vue";
import Button from "@/Shared/Layout/Button.vue";
import {DialogTitle} from "@headlessui/vue";
import { getJson } from "@/Functions/http";
import { getActivityLog } from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/ApplicationsController";


export default {
    name: "ActivityLogModal",
    components: {Button, LogTab, WithDismissButtonModal, DialogTitle},
    props: {
        applicationId: {
            required: true,
            type: String
        }
    },
    setup(props) {

        const open = ref(false)
        const application = ref(false)

        const isLoaded = computed(() => _.isObject(application.value))

        watchEffect(() => {
            if(open.value && !isLoaded.value) {
                // Non-Inertia JSON endpoint: native fetch via http.js (drops axios) with a
                // Wayfinder-built URL (drops Ziggy route()).
                getJson(getActivityLog.url(props.applicationId))
                    .then(result => application.value = result)
            }
        })

        return {
            open,
            application,
            isLoaded
        }
    }
}
</script>

<style scoped>

</style>