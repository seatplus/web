<template>
  <DarkSidebar :active-sidebar-element="activeSidebarElement">
    <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <slot>
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900">
              Dashboard
            </h1>
          </div>
          <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Replace with your content -->
            <div class="py-4">
              <div class="border-4 border-dashed border-gray-200 rounded-lg h-96" />
            </div>
            <!-- /End replace -->
          </div>
        </slot>
      </div>
    </main>
  </DarkSidebar>
</template>

<script setup>
import DarkSidebar from "@/Shared/SidebarLayout/DarkSidebar.vue";

defineProps({
  activeSidebarElement: {
    type: String,
    required: true,
  },
});

</script>

