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

<script >
import {computed, ref} from "vue";
import WithDismissButtonModal from "@/Shared/Modals/WithDismissButtonModal";
import {useForm} from "@inertiajs/inertia-vue3";
import Autosuggest from "@/Shared/Components/Autosuggest";
import SimpleToggle from "@/Shared/SimpleToggle";
import TwoColumnCardWithSubmitAction from "@/Shared/Layout/Forms/TwoColumnCardWithSubmitAction";
import EnlistmentConfig from "@/Pages/Corporation/Recruitment/Configuration/EnlistmentConfig";

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
