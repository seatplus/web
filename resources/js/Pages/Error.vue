<template>
  <div class="absolute flex min-h-full min-w-full flex-col bg-white pt-16 pb-12">
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
      <div class="py-16">
        <div class="text-center">
          <p class="text-base font-semibold text-indigo-600">
            {{ status }}
          </p>
          <h1 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
            {{ title }}
          </h1>
          <p class="mt-2 text-base text-gray-500">
            {{ description }}
          </p>
          <div class="mt-6">
            <Link
              :href="route('home')"
              class="text-base font-medium text-indigo-600 hover:text-indigo-500"
            >
              Go back home
              <span aria-hidden="true"> &rarr;</span>
            </Link>
          </div>
        </div>
      </div>
    </main>
    <footer class="mx-auto w-full max-w-7xl flex-shrink-0 px-4 sm:px-6 lg:px-8">
      <nav class="flex justify-center space-x-4">
        <Link
          href="https://github.com/seatplus/seatplus/issues/new?assignees=&labels=&template=bug_report.md&title="
          class="text-sm font-medium text-gray-500 hover:text-gray-600"
        >
          Contact Support
        </Link>
        <span
          class="inline-block border-l border-gray-300"
          aria-hidden="true"
        />
        <a
          href="https://discord.gg/sgvp7HMBPX"
          class="text-sm font-medium text-gray-500 hover:text-gray-600"
        >Discord</a>
        <span
          class="inline-block border-l border-gray-300"
          aria-hidden="true"
        />
      </nav>
    </footer>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    name: "Error",
    components: { Link },
    layout: null,
    props: {
        status: {
            type: Number,
            required: true
        }
    },
    computed: {
        title() {
            return {
                503: 'Service Unavailable',
                500: 'Server Error',
                404: 'Page Not Found',
                403: 'Forbidden',
            }[this.status]
        },
        description() {
            return {
                503: 'Sorry, we are doing some maintenance. Please check back soon.',
                500: 'Whoops, something went wrong on our servers.',
                404: 'Sorry, the page you are looking for could not be found.',
                403: 'Sorry, you are forbidden from accessing this page.',
            }[this.status]
        },
    }
}
</script>

<style scoped>

</style>
