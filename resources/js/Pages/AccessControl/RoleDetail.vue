<template>
    <div>
        <h1>{{ role.name }}</h1>
    </div>
</template>

<script setup>
defineProps({
    role: Object,
    can_edit: Boolean,
    activeSidebarElement: String,
})
</script>
