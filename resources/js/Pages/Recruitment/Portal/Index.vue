<template>
  <div class="space-y-3">
    <PageHeader page-title="Job Portal" />

    <p class="max-w-4xl text-sm leading-5 text-gray-500">
      The following corporations are open for recruitment. A posting is either <strong>single character</strong>
      &mdash; apply with one character and only that character must meet the SSO scope requirements &mdash;
      or <strong>whole account</strong>, where every character on your account must meet them.
    </p>

    <div
      v-if="myApplications.length > 0"
      class="space-y-2"
    >
      <h3 class="text-lg font-medium text-gray-900">
        Your applications
      </h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <MyApplicationCard
          v-for="application in myApplications"
          :key="application.application_id"
          :application="application"
        />
      </div>
    </div>

    <div
      v-if="postings.length === 0"
      class="text-center py-12"
    >
      <BriefcaseIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-semibold text-gray-900">
        No open job postings
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        No corporation is currently recruiting. Check back later.
      </p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
      <PostingCard
        v-for="posting in postings"
        :key="posting.corporation_id"
        :posting="posting"
        :characters="characters"
        :applied="appliedCorporationIds.includes(posting.corporation_id)"
        :post-application-url="postApplicationUrl"
      />
    </div>
  </div>
</template>

<script setup>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import { BriefcaseIcon } from "@heroicons/vue/24/outline";
import PostingCard from "./PostingCard.vue";
import MyApplicationCard from "./MyApplicationCard.vue";

defineProps({
  postings: {
    required: true,
    type: Array,
  },
  characters: {
    required: true,
    type: Array,
  },
  appliedCorporationIds: {
    required: true,
    type: Array,
  },
  myApplications: {
    required: true,
    type: Array,
  },
  postApplicationUrl: {
    required: true,
    type: String,
  },
});
</script>
