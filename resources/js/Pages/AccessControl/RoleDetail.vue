<template>
  <div>
    <!-- RoleDetail page — placeholder, full implementation in Phase 1.5-J-2 -->
  </div>
</template>

<script setup lang="ts">
defineProps<{
  role: {
    id: number
    name: string
    type: string
    affiliations: Array<{ id: number; category: string; type: string }>
    permissions: string[]
  }
  activeSidebarElement: string
}>()
</script>
