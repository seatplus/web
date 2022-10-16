<template>
  <div class="bg-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:py-24 sm:px-6 lg:px-8">
      <div class="relative bg-gray-50 shadow-xl">
        <h2 class="sr-only">
          Add Characters
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-3">
          <!-- Contact information -->
          <div class="relative overflow-hidden bg-indigo-700 py-10 px-6 sm:px-10 xl:p-12">
            <div
              class="pointer-events-none absolute inset-0 sm:hidden"
              aria-hidden="true"
            >
              <svg
                class="absolute inset-0 h-full w-full"
                width="343"
                height="388"
                viewBox="0 0 343 388"
                fill="none"
                preserveAspectRatio="xMidYMid slice"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M-99 461.107L608.107-246l707.103 707.107-707.103 707.103L-99 461.107z"
                  fill="url(#linear1)"
                  fill-opacity=".1"
                />
                <defs>
                  <linearGradient
                    id="linear1"
                    x1="254.553"
                    y1="107.554"
                    x2="961.66"
                    y2="814.66"
                    gradientUnits="userSpaceOnUse"
                  >
                    <stop stop-color="#fff" />
                    <stop
                      offset="1"
                      stop-color="#fff"
                      stop-opacity="0"
                    />
                  </linearGradient>
                </defs>
              </svg>
            </div>
            <div
              class="pointer-events-none absolute top-0 right-0 bottom-0 hidden w-1/2 sm:block lg:hidden"
              aria-hidden="true"
            >
              <svg
                class="absolute inset-0 h-full w-full"
                width="359"
                height="339"
                viewBox="0 0 359 339"
                fill="none"
                preserveAspectRatio="xMidYMid slice"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M-161 382.107L546.107-325l707.103 707.107-707.103 707.103L-161 382.107z"
                  fill="url(#linear2)"
                  fill-opacity=".1"
                />
                <defs>
                  <linearGradient
                    id="linear2"
                    x1="192.553"
                    y1="28.553"
                    x2="899.66"
                    y2="735.66"
                    gradientUnits="userSpaceOnUse"
                  >
                    <stop stop-color="#fff" />
                    <stop
                      offset="1"
                      stop-color="#fff"
                      stop-opacity="0"
                    />
                  </linearGradient>
                </defs>
              </svg>
            </div>
            <div
              class="pointer-events-none absolute top-0 right-0 bottom-0 hidden w-1/2 lg:block"
              aria-hidden="true"
            >
              <svg
                class="absolute inset-0 h-full w-full"
                width="160"
                height="678"
                viewBox="0 0 160 678"
                fill="none"
                preserveAspectRatio="xMidYMid slice"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M-161 679.107L546.107-28l707.103 707.107-707.103 707.103L-161 679.107z"
                  fill="url(#linear3)"
                  fill-opacity=".1"
                />
                <defs>
                  <linearGradient
                    id="linear3"
                    x1="192.553"
                    y1="325.553"
                    x2="899.66"
                    y2="1032.66"
                    gradientUnits="userSpaceOnUse"
                  >
                    <stop stop-color="#fff" />
                    <stop
                      offset="1"
                      stop-color="#fff"
                      stop-opacity="0"
                    />
                  </linearGradient>
                </defs>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-white">
              Add Characters
            </h3>
            <p class="mt-6 max-w-3xl text-base text-indigo-50">
              Please add all characters you want to track. You can add more later.
            </p>
          </div>

          <!-- Contact form -->
          <div class="flex flex-col justify-between py-10 px-6 sm:px-10 lg:col-span-2 xl:p-12">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <a
                type="button"
                :href="route('auth.eve')"
                class="flex p-2 lg:col-span-2 border-2 border-dashed border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-lg"
              >
                <div class="mb-4 shrink-0 sm:mb-0 mr-4 self-center">
                  <div>
                    <UserPlusIcon
                      class="h-10 w-10 rounded-full text-gray-900"
                      aria-hidden="true"
                    />
                  </div>
                </div>
                <div class="flex flex-col justify-center">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Add another character
                  </h3>
                </div>
              </a>
              <EntityByIdBlock
                v-for="character in characters"
                :id="character.character_id"
                :key="character.id"
                :entity="character"
              />
            </div>

            <div class="sm:flex sm:justify-end">
              <slot />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import { UserPlusIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
  characters: {
    type: Array,
    required: true,
  },
});
</script>
