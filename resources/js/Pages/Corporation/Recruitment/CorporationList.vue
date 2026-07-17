<template>
  <div>
    <!-- Debounced server-side corp-name search. Reloads only the `corporations` scroll prop with a
         namespaced `corp_search` param (never `search`, so it can't collide with a page's own search),
         resetting the prop so <InfiniteScroll> replaces the list with the filtered first page. -->
    <div class="mb-4">
      <label
        for="corp_search"
        class="sr-only"
      >
        Search corporations
      </label>
      <input
        id="corp_search"
        v-model="corpSearch"
        type="text"
        placeholder="Search for a corporation…"
        class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-xs focus:outline-hidden focus:ring-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
      >
    </div>

    <!-- Native Inertia v3 infinite scroll over the page-level `corporations` scroll prop
         (affiliated, not-yet-enlisted corporations paginated with pageName 'corporations').
         Replaces the axios/Ziggy useInfinityScrolling loader. -->
    <InfiniteScroll
      data="corporations"
      items-element="#recruitment-corporation-list"
      :buffer="500"
      preserve-url
    >
      <ul
        id="recruitment-corporation-list"
        class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3"
      >
        <li
          v-for="corporation in corporations"
          :key="corporation.corporation_id"
        >
          <CardWithHeader>
            <template #header>
              <EntityBlock :entity="corporation" />
            </template>

            <div class="grid grid-cols-2 divide-x divide-gray-200">
              <Button
                :href="openRecruitment.url()"
                method="post"
                :data="{ corporation_id: corporation.corporation_id, type: 'character' }"
                class="w-full justify-center gap-x-2 rounded-none border-0 py-4 shadow-none"
              >
                <UserIcon
                  class="h-5 w-5 text-gray-400"
                  aria-hidden="true"
                />
                Recruits only
              </Button>
              <Button
                :href="openRecruitment.url()"
                method="post"
                :data="{ corporation_id: corporation.corporation_id, type: 'user' }"
                class="w-full justify-center gap-x-2 rounded-none border-0 py-4 shadow-none"
              >
                <UsersIcon
                  class="h-5 w-5 text-gray-400"
                  aria-hidden="true"
                />
                All characters
              </Button>
            </div>
          </CardWithHeader>
        </li>
      </ul>

      <template #loading>
        <div class="relative block w-full py-6 text-center">
          <svg
            class="animate-spin mx-auto h-8 w-8 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
          </svg>
          <span class="mt-2 block text-sm font-medium text-gray-500">
            loading more corporations…
          </span>
        </div>
      </template>
    </InfiniteScroll>

    <p
      v-if="corporations.length === 0"
      class="px-4 py-8 text-center text-sm text-gray-500"
    >
      No corporations available to open for recruitment.
    </p>
  </div>
</template>

<script>
import { InfiniteScroll, router } from "@inertiajs/vue3";
import { UserIcon, UsersIcon } from "@heroicons/vue/24/outline";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import Button from "@/Shared/Layout/Button.vue";
import { create as openRecruitment } from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/EnlistmentsController";

export default {
    name: "CorporationList",
    components: {
        InfiniteScroll,
        CardWithHeader,
        EntityBlock,
        Button,
        UserIcon,
        UsersIcon,
    },
    setup() {
        return { openRecruitment };
    },
    data() {
        return {
            corpSearch: '',
        };
    },
    computed: {
        corporations() {
            return this.$page.props.corporations?.data ?? [];
        },
    },
    watch: {
        corpSearch() {
            this.debouncedReload();
        },
    },
    created() {
        // Debounce so the server only reloads once the manager pauses typing.
        this.debouncedReload = _.debounce(this.reload, 300);
    },
    methods: {
        // Reload only the `corporations` scroll prop with the namespaced search term; reset it so
        // <InfiniteScroll> replaces the list with the filtered first page instead of merging.
        reload() {
            router.reload({
                only: ['corporations'],
                reset: ['corporations'],
                data: { corp_search: this.corpSearch === '' ? null : this.corpSearch },
                preserveScroll: true,
                preserveUrl: true,
            });
        },
    },
}
</script>
