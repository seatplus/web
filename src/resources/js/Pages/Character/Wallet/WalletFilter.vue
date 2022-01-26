<template>
  <Card>
    <div class="grid grid-cols-3 gap-5">
      <Multiselect
        v-model="filter"
        route="wallet.journalTypes"
        label="Wallet Journal entries"
        placeholder="Please search for ref_type to filter for"
      />
    </div>
  </Card>
</template>

<script>
import Card from "@/Shared/Layout/Cards/Card";
import Multiselect from "@/Shared/Components/Multiselect";
export default {
    name: "WalletFilter",
    components: {Multiselect, Card},
    props: {
        modelValue: {
            required: true,
            type: Array
        }
    },
    emits: ['update:modelValue'],
    computed: {
        filter: {
            get() {
                return this.modelValue
            },
            set(newValue) {
                this.$emit('update:modelValue', newValue)
            }
        }
    }
}
</script>

<style scoped>

</style>