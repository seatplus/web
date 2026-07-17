<template>
  <div class="space-y-3">
    <PageHeader :breadcrumbs="[{name: 'Recruitment', route: 'corporation.recruitment'}]">
      User Application
      <template #primary>
        <HeaderButton
          v-if="canImpersonate"
          :secondary="true"
          @click="impersonate"
        >
          Impersonate
        </HeaderButton>
      </template>
    </PageHeader>

    <ul class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <li class="col-span-2">
        <TabComponent
          :recruit="recruit"
          :watchlist="watchlist"
          :application="application"
          :target-corporation="application.corporation"
        />
      </li>

      <li class="col-span-1">
        <CardWithHeader>
          <template #header>
            <div class="flex items-center gap-4">
              <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100">
                <IdentificationIcon class="h-6 w-6 text-indigo-600" />
              </div>
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                  Application of {{ recruit.main_character.name }}
                </h3>
                <p class="mt-1 text-sm leading-5 text-gray-500">
                  This will decide if one or all of the following characters are allowed to join the corporation: {{ characters }}
                </p>
              </div>
            </div>
          </template>

          <div class="space-y-6 px-4 py-5 sm:p-6">
            <p class="text-sm leading-5 text-gray-500">
              Remember to invite them in game as well.
            </p>

            <div class="space-y-3">
              <UpdateCharacterComponent
                v-for="character in recruit.characters"
                :key="character.character_id"
                :character="character"
              />
            </div>

            <!-- Decision -->
            <form
              v-if="isOpen"
              class="space-y-6 border-t border-gray-200 pt-6"
              @submit.prevent="submit"
            >
              <fieldset>
                <legend class="text-sm font-medium text-gray-900">
                  Decision
                </legend>
                <p class="mt-1 text-sm leading-5 text-gray-500">
                  Decide if the recruit should be accepted to the corporation or not.
                </p>
                <div class="mt-4 space-y-4">
                  <div class="flex items-center">
                    <input
                      id="accept_application"
                      v-model="form.decision"
                      value="accepted"
                      name="decision"
                      type="radio"
                      class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600"
                    >
                    <label
                      for="accept_application"
                      class="ml-3 block text-sm font-medium text-gray-700"
                    >
                      Accept application
                    </label>
                  </div>
                  <div class="flex items-center">
                    <input
                      id="reject_application"
                      v-model="form.decision"
                      value="rejected"
                      name="decision"
                      type="radio"
                      class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600"
                    >
                    <label
                      for="reject_application"
                      class="ml-3 block text-sm font-medium text-gray-700"
                    >
                      Reject application
                    </label>
                  </div>
                </div>
                <p
                  v-if="form.errors.decision"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.decision }}
                </p>
              </fieldset>

              <!-- Explanation (required when rejecting) -->
              <div v-if="form.decision === 'rejected'">
                <InputWithValidation
                  v-model="form.explanation"
                  label="Explanation"
                  placeholder="Why is this application rejected?"
                  :error="form.errors.explanation ?? ''"
                />
                <p
                  v-if="!form.errors.explanation"
                  class="mt-2 text-sm text-gray-500"
                >
                  Write a few sentences about the decision, so recruiters in the future might learn from past decisions.
                </p>
              </div>

              <div class="flex justify-end">
                <Button
                  :is-inertia-button="false"
                  button-size="medium"
                  @click="submit"
                >
                  Submit review
                </Button>
              </div>
            </form>
          </div>
        </CardWithHeader>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import { IdentificationIcon } from '@heroicons/vue/24/outline';
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import HeaderButton from "@/Shared/Layout/HeaderButton.vue";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import Button from "@/Shared/Layout/Button.vue";
import InputWithValidation from "@/Shared/Layout/Forms/InputWithValidation.vue";
import TabComponent from "./TabComponent.vue";
import UpdateCharacterComponent from "./UpdateCharacterComponent.vue";
import { reviewApplication } from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/ApplicationsController";
import ImpersonateRecruit from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/ImpersonateRecruit";

const props = defineProps({
    recruit: {
        required: true,
        type: Object
    },
    application: {
        required: true,
        type: Object
    },
    watchlist: {
        required: true,
        type: Object
    },
    activeSidebarElement: {
        required: true,
        type: String
    }
});

const form = useForm({
    decision: null,
    explanation: ''
});

const isOpen = computed(() => props.application.status === 'open');

const canImpersonate = computed(() => props.recruit.id && isOpen.value);

const characters = computed(() => _.map(props.recruit.characters, (character) => character.name).join(', '));

const impersonate = () => router.visit(ImpersonateRecruit.url(props.application.id));

const submit = () => form.post(reviewApplication.url(props.application.id));
</script>

<style scoped>

</style>
