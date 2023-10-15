<script setup>

import { defineProps, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    appTitle: {
        type: String,
    },
})

const page = usePage()

const icon = computed(() => {
    return page.props.images.icon
})

</script>

<template>
  <Head :title="appTitle ? `${appTitle} - Seatplus` : 'Seatplus'">
    <link rel="icon" type="image/svg+xml" :href="icon">
    <slot />
  </Head>
</template>

