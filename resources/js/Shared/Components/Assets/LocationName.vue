<template>
  <span>
    <span v-if="system"> {{ system }} - </span> {{ name }}
  </span>
</template>

<script setup>
import { computed, ref } from "vue";
import { getJson } from "@/Functions/http";
import { getLocation } from "@/actions/Seatplus/Web/Http/Controllers/Shared/ManualLocationController";

const props = defineProps({
    location: {
        required: true,
        type: Object
    }
});

const result = ref({})

const name = computed(() => {
    // An already-resolved name (a location summary carries `name` directly) wins, so a
    // known location renders immediately without an on-demand fetch.
    if (props.location.name) {
        return props.location.name
    }

    return props.location.location != null ? _.get(props.location, 'location.locatable.name') : _.get(result.value, 'name', 'loading ...')
})

const system = computed(() => _.get(result.value, 'system.name',))

// Only resolve on-demand when there is no known name and no eager relation to read from.
if (! props.location.name && props.location.location == null) {
    getJson(getLocation.url(props.location.location_id))
        .then((data) => {
            result.value = data
        })
}
</script>

<style scoped>

</style>
