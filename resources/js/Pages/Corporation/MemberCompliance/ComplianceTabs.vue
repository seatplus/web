<template>
  <BarWithUnderline
    :tabs="tabs"
    @select="(tab) => $emit('update:modelValue', tab.value)"
  />
</template>

<script>
import BarWithUnderline from "@/Shared/Layout/Tabs/BarWithUnderline.vue";

const tabs = [
    { id: 0, name: 'Default', value: 'default' },
    { id: 1, name: 'Renegades only', value: 'renegades' },
    { id: 2, name: 'Loyalists only', value: 'loyalists' },
]

export default {
    name: "ComplianceTabs",
    components: {BarWithUnderline},
    props: {
        modelValue: {
            type: String,
            required: true
        }
    },
    emits: ['update:modelValue'],
    setup() {
        return {
            tabs,
        }
    },
}
</script>
