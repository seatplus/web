<template>
  <Card>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
      <Multiselect
        v-model="filter"
        route-name="wallet.journalTypes"
        label="Wallet Journal entries"
        placeholder="Please search for ref_type to filter for"
      />
    </div>
  </Card>
</template>

<script>
import Card from "../../Layout/Cards/Card.vue";
import Multiselect from "../Multiselect.vue";
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