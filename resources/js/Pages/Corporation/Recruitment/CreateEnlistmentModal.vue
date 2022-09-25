<template>
  <WithDismissButtonModal
    v-model="open"
    width="4xl"
  >
    <EnlistmentConfig
      class="pt-6 sm:pt-10"
      @onSuccess="open = false"
    />
  </WithDismissButtonModal>
</template>

<script>
import { computed } from "vue";
import WithDismissButtonModal from "@/Shared/Modals/WithDismissButtonModal.vue";
import EnlistmentConfig from "./Configuration/EnlistmentConfig.vue";

export default {
    name: "CreateEnlistmentModal",
    components: {EnlistmentConfig, WithDismissButtonModal},
    props: {
        modelValue: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update:modelValue'],
    setup(props, {emit}) {

        const open = computed({
            //getter
            get() {
                return props.modelValue
            },
            // setter
            set(newValue) {
                emit('update:modelValue', newValue)
            }
        })

        return {
            open
        }


    }
}
</script>
