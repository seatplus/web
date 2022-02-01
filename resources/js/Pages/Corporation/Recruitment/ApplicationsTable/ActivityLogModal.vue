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
import WithDismissButtonModal from "../../../../Shared/Modals/WithDismissButtonModal";
import {computed, ref, watchEffect} from "vue";
import LogTab from "../Tabs/LogTab";
import Button from "../../../../Shared/Layout/Button";
import route from 'ziggy'
import {DialogTitle} from "@headlessui/vue";


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
                axios.get(route('get.activity.log', props.applicationId))
                    .then(result => application.value = result.data)
                    .catch(error => console.log(error))
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