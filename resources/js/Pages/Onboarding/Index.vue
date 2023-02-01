<template>
  <div class="absolute min-h-full min-w-full bg-gray-100 px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
    <main class="mx-auto flex w-full max-w-7xl flex-grow flex-col justify-center px-4 sm:px-6 lg:px-8">
      <div class="flex flex-shrink-0 justify-center">
        <a
          href="/"
          class="inline-flex"
        >
          <span class="sr-only">Seat Plus</span>
          <img
            class="h-12 w-auto"
            src="/img/seat_plus_logo.svg"
            alt="Seat Plus"
          >
        </a>
      </div>
      <div>
        <Introduction
          v-if="step === 1"
          :main-character-id="mainCharacterId"
        >
          <Link
            class="block w-full rounded-md border border-transparent bg-white py-3 px-5 text-center text-base font-medium text-indigo-700 shadow-md hover:bg-gray-50 sm:inline-block sm:w-auto"
            :href="nextUrl"
          >
            Next
          </Link>
        </Introduction>
        <OnboardingEnlistment
          v-if="step === 2"
          :main-character-id="mainCharacterId"
          :enlistments="user_enlistments"
        >
          <Link
            as="button"
            :href="nextUrl"
            class="mt-2 inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
          >
            Next
          </Link>
        </OnboardingEnlistment>
        <AddCharacters
          v-if="step === 3"
          :characters="characters"
        >
          <Link
            :key="character_enlistments.length"
            as="button"
            :method="character_enlistments.length > 0 ? 'get' : 'post'"
            :href="nextUrl"
            class="mt-2 inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
          >
            {{ character_enlistments.length > 0 ? 'Next' : 'Finish' }}
          </Link>
        </AddCharacters>
        <OnboardingEnlistment
          v-if="step === 4"
          :main-character-id="mainCharacterId"
          :enlistments="character_enlistments"
        >
          <Link
            as="button"
            :href="nextUrl"
            method="post"
            class="mt-2 inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
          >
            Finish
          </Link>
        </OnboardingEnlistment>
      </div>
    </main>
  </div>
</template>


<script>

export default {
    layout: null,
}
</script>

<script setup>

import { computed } from "vue";
import { Link } from '@inertiajs/vue3';
import OnboardingEnlistment from "@/Pages/Onboarding/OnboardingEnlistment.vue";
import Introduction from "@/Pages/Onboarding/Introduction.vue";
import AddCharacters from "@/Pages/Onboarding/AddCharacters.vue";

const props = defineProps({
    enlistments: {
        required: true,
        type: Array
    },
    characters: {
        required: true,
        type: Array
    },
    mainCharacterId: {
        required: true,
        type: Number
    },
    step: {
        required: true,
        type: Number
    }
})

const user_enlistments = computed(() => {
    return props.enlistments.filter(enlistment => enlistment.type === 'user')
})

const character_enlistments = computed(() => {
    return props.enlistments.filter(enlistment => enlistment.type === 'character')
})

const nextUrl = computed(() => {

    let route_name = route().current()

    if(props.step === 1) {
        return route(route_name, {step: user_enlistments.value.length > 0 ? 2 : 3})
    }

    if(props.step > 1) {
        return route(route_name, {step: props.step + 1})
    }
})

</script>

