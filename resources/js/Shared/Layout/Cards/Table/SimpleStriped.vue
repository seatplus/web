<template>
  <div class="flex flex-col">
    <div class="overflow-x-auto">
      <div class="align-middle inline-block min-w-full">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
          <table class="min-w-full relative divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <slot name="header" />
            </thead>

            <tbody>
              <slot />
            </tbody>

            <slot name="outside" />
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
    name: "SimpleStriped"
}
</script>

<style scoped>

</style>
