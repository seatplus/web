<template>
  <div class="relative py-16">
    <div class="mx-auto max-w-7xl bg-indigo-600 lg:bg-transparent lg:px-8">
      <div class="lg:grid lg:grid-cols-12">
        <div class="relative z-10 lg:col-span-4 lg:col-start-1 lg:row-start-1 lg:bg-transparent lg:py-16">
          <div
            class="absolute inset-x-0 h-1/2 bg-gray-100 lg:hidden"
            aria-hidden="true"
          />
          <div class="mx-auto max-w-md px-4 sm:max-w-3xl sm:px-6 lg:max-w-none lg:p-0">
            <div class="aspect-w-10 aspect-h-6 sm:aspect-w-2 sm:aspect-h-1 lg:aspect-w-1">
              <img
                class="rounded-3xl object-cover object-center shadow-2xl"
                :src="`https://images.evetech.net/characters/${mainCharacterId}/portrait?size=512`"
                :alt="resolvedObject?.name"
              >
            </div>
          </div>
        </div>

        <div class="relative bg-indigo-600 lg:col-span-10 lg:col-start-3 lg:row-start-1 lg:grid lg:grid-cols-10 lg:items-center lg:rounded-3xl">
          <div
            class="absolute inset-0 hidden overflow-hidden rounded-3xl lg:block"
            aria-hidden="true"
          >
            <svg
              class="absolute bottom-full left-full translate-y-1/3 -translate-x-2/3 transform xl:bottom-auto xl:top-0 xl:translate-y-0"
              width="404"
              height="384"
              fill="none"
              viewBox="0 0 404 384"
              aria-hidden="true"
            >
              <defs>
                <pattern
                  id="64e643ad-2176-4f86-b3d7-f2c5da3b6a6d"
                  x="0"
                  y="0"
                  width="20"
                  height="20"
                  patternUnits="userSpaceOnUse"
                >
                  <rect
                    x="0"
                    y="0"
                    width="4"
                    height="4"
                    class="text-indigo-500"
                    fill="currentColor"
                  />
                </pattern>
              </defs>
              <rect
                width="404"
                height="384"
                fill="url(#64e643ad-2176-4f86-b3d7-f2c5da3b6a6d)"
              />
            </svg>
            <svg
              class="absolute top-full -translate-y-1/3 -translate-x-1/3 transform xl:-translate-y-1/2"
              width="404"
              height="384"
              fill="none"
              viewBox="0 0 404 384"
              aria-hidden="true"
            >
              <defs>
                <pattern
                  id="64e643ad-2176-4f86-b3d7-f2c5da3b6a6d"
                  x="0"
                  y="0"
                  width="20"
                  height="20"
                  patternUnits="userSpaceOnUse"
                >
                  <rect
                    x="0"
                    y="0"
                    width="4"
                    height="4"
                    class="text-indigo-500"
                    fill="currentColor"
                  />
                </pattern>
              </defs>
              <rect
                width="404"
                height="384"
                fill="url(#64e643ad-2176-4f86-b3d7-f2c5da3b6a6d)"
              />
            </svg>
          </div>
          <div class="relative mx-auto max-w-md space-y-6 py-12 px-4 sm:max-w-3xl sm:py-16 sm:px-6 lg:col-span-6 lg:col-start-4 lg:max-w-none lg:p-0">
            <h2
              id="join-heading"
              class="text-3xl font-bold tracking-tight text-white"
            >
              Welcome {{ resolvedObject?.name }}
            </h2>
            <p class="text-lg text-white">
              This is the first step of the onboarding process. If this instance has an open enlistment you can join it and then add additional characters
            </p>
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

import {useResolveId} from "@/Functions/useResolveId.js";


const props = defineProps({
    mainCharacterId: {
        type: Number,
        required: true
    }
})

const resolvedObject = useResolveId(props.mainCharacterId)

</script>

<style scoped>

</style>