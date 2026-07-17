<template>
  <Deferred data="openEnlistments">
    <template #fallback>
      <div class="py-5 border-b border-gray-200 space-y-2">
        <div class="h-5 w-32 rounded bg-gray-200 animate-pulse" />
        <div class="space-y-2">
          <div class="h-3 w-full rounded bg-gray-200 animate-pulse" />
          <div class="h-3 w-5/6 rounded bg-gray-100 animate-pulse" />
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div
            v-for="n in 2"
            :key="n"
            class="flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-xs"
          >
            <div class="h-10 w-10 rounded-full bg-gray-200 animate-pulse" />
            <div class="flex-1 space-y-2">
              <div class="h-3 w-1/2 rounded bg-gray-200 animate-pulse" />
              <div class="h-3 w-1/3 rounded bg-gray-100 animate-pulse" />
            </div>
          </div>
        </div>
      </div>
    </template>

    <div
      v-show="enlistments.length > 0"
      class="py-5 border-b border-gray-200 space-y-2"
    >
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        Job Postings
      </h3>
      <p class="max-w-4xl text-sm leading-5 text-gray-500">
        The following corporations are open for new recruits. These job listings do have two kind of characteristics: either 'character' or 'user' and it's defined by  a senior recruiter of that corporation.
        For an enlistment with character type, this means you are able to apply with single characters of yours and sso-scope requirements are only enforced per applied character.
        If an enlistment is of user type, upon application each and every character that belongs to your user account must fulfill the set sso-scope requirements of the recruiting corporation.
      </p>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <Enlistment
          v-for="enlistment in enlistments"
          :key="enlistment.corporation_id"
          :enlistment="enlistment"
        />
      </div>
    </div>
  </Deferred>
</template>

<script setup>
import {computed} from "vue";
import {Deferred, usePage} from "@inertiajs/vue3";
import Enlistment from "./Enlistment.vue";

const enlistments = computed(() => usePage().props.openEnlistments ?? [])

</script>

<style scoped>

</style>
