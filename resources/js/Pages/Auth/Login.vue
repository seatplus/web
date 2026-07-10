<template>
  <div>
    <AppHead app-title="Login" />
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div>
        <img
          class="mx-auto h-12 w-auto"
          src="/img/seat_plus_logo.svg"
          alt="Seat Plus"
        >
        <h2 class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-900">
          SeAT plus
        </h2>
        <p class="mt-2 text-center text-sm leading-5 text-gray-600 max-w">
          {{ $trans('web::auth.login_welcome') }}
        </p>
      </div>

      <div class="mt-8">
        <a
          :href="route('auth.eve')"
          class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-hidden focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
        >
          <span class="absolute left-0 inset-y pl-3">
            <svg
              class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400 transition ease-in-out duration-150"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                clip-rule="evenodd"
              />
            </svg>
          </span>
          Sign in
        </a>
      </div>

      <div class="mt-6 flex justify-center">
        <select
          v-model="selectedLocale"
          class="rounded-md border-gray-300 text-sm text-gray-600 shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
          @change="switchLocale"
        >
          <option
            v-for="code in locales"
            :key="code"
            :value="code"
          >
            {{ localeName(code) }}
          </option>
        </select>
      </div>
    </div>
  </div>
</template>

<script>

import EmptyLayout from "@/Shared/Layout/AuthLayout/EmptyLayout.vue";
import AppHead from "@/Shared/AppHead.vue";
import { router } from "@inertiajs/vue3";
import { localeName } from "@/i18n/localeName";

export default {
    name: "Login",
    components: {AppHead},
    layout: (h, page) => h(EmptyLayout, () => page),
    data() {
        return {
            selectedLocale: this.$page.props.locale,
        }
    },
    computed: {
        locales() {
            return this.$page.props.locales || []
        }
    },
    methods: {
        localeName,
        // `translations` is a reactive Inertia prop now — the redirect back re-renders
        // in the newly-selected language without a full page reload.
        switchLocale() {
            router.post(route('locale.update'), { locale: this.selectedLocale }, {
                preserveScroll: true,
            })
        }
    }
}
</script>

<style scoped>

</style>
