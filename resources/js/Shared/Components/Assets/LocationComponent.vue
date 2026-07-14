<template>
  <div ref="rootEl">
    <WideLists>
      <template #header>
        <div
          class="bg-white px-4 border-b border-gray-200 sm:px-6"
          :class="compact ? 'py-2' : 'py-5'"
        >
          <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
            <div class="ml-4 mt-4">
              <h3
                class="leading-6 font-medium text-gray-900"
                :class="compact ? 'text-sm' : 'text-lg'"
              >
                {{ location.name ?? 'Unknown Location' }}
              </h3>
              <p
                class="text-sm text-gray-500"
                :class="compact ? 'mt-0' : 'mt-1'"
              >
                <span v-if="hasLoaded">{{ total }} items</span>
                <span
                  v-else
                  class="inline-block h-3 w-24 rounded bg-gray-200 animate-pulse align-middle"
                />
              </p>
            </div>
            <div class="inline-flex items-baseline space-x-2">
              <div
                v-if="location.is_manual_location && context !== 'recruitment'"
                class="ml-4 mt-4 shrink-0"
              >
                <button
                  type="button"
                  class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-xs text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  @click="openModal = true"
                >
                  Add location information
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>
      <template #elements>
        <!-- First-page skeleton while the location's items are fetched on scroll-into-view. -->
        <div
          v-if="!hasLoaded"
          class="divide-y divide-gray-200"
        >
          <div
            v-for="n in 3"
            :key="n"
            class="flex items-center gap-4 px-4 py-4 sm:px-6"
          >
            <div class="h-10 w-10 rounded-full bg-gray-200 animate-pulse" />
            <div class="flex-1 space-y-2">
              <div class="h-3 w-1/3 rounded bg-gray-200 animate-pulse" />
              <div class="h-3 w-1/4 rounded bg-gray-100 animate-pulse" />
            </div>
          </div>
        </div>

        <ItemList
          v-else
          :items="items"
          :compact="compact"
        />

        <!-- In-location pagination: sentinel loads the next page of top-level items when reached. -->
        <div ref="sentinel" />
        <div
          v-if="hasLoaded && loading"
          class="py-4 text-center text-sm font-medium text-gray-500"
        >
          loading more…
        </div>
      </template>
    </WideLists>
    <teleport to="#destination">
      <AddManualLocationModal
        v-model="openModal"
        :location_id="location.location_id"
      />
    </teleport>
  </div>
</template>

<script setup>
import WideLists from "../../WideLists.vue";
import ItemList from "./ItemList.vue";
import AddManualLocationModal from "./AddManualLocationModal.vue";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { getJson } from "@/Functions/http";
import { location as locationAction } from "@/actions/Seatplus/Web/Http/Controllers/Character/AssetsController";

const props = defineProps({
    location: {
        required: true,
        type: Object,
    },
    context: {
        required: false,
        type: String,
        default: "character",
    },
    compact: {
        required: false,
        default: false,
        type: Boolean,
    },
    // The applied asset filter (search/systems/regions/types/groups/categories + character_ids),
    // forwarded to the per-location endpoint so a location shows only its matching top-level items.
    filter: {
        required: false,
        type: Object,
        default: () => ({}),
    },
});

const openModal = ref(false);
const items = ref([]);
const total = ref(0);
const currentPage = ref(0);
const lastPage = ref(1);
const loading = ref(false);
const isVisible = ref(false);
const rootEl = ref(null);
const sentinel = ref(null);

const hasLoaded = computed(() => currentPage.value > 0);
const canLoadMore = computed(() => currentPage.value > 0 && currentPage.value < lastPage.value);

const fetchPage = async (page) => {
    if (loading.value) {
        return;
    }
    loading.value = true;

    const url = locationAction.url(
        { location_id: props.location.location_id },
        { query: { ...props.filter, items: page } },
    );

    try {
        const response = await getJson(url);
        const data = response?.data ?? [];
        const meta = response?.meta ?? {};

        items.value = page === 1 ? data : [...items.value, ...data];
        currentPage.value = meta.current_page ?? page;
        lastPage.value = meta.last_page ?? page;
        total.value = meta.total ?? items.value.length;
    } finally {
        loading.value = false;
    }
};

const loadFirstPage = () => {
    if (! hasLoaded.value) {
        fetchPage(1);
    }
};

const loadNextPage = () => {
    if (canLoadMore.value) {
        fetchPage(currentPage.value + 1);
    }
};

let observer;
onMounted(() => {
    // 300px rootMargin so a location (and its next page) start loading just before entering view.
    observer = new IntersectionObserver(
        (entries) => entries.forEach((entry) => {
            if (entry.target === rootEl.value) {
                isVisible.value = entry.isIntersecting;
                if (entry.isIntersecting) {
                    loadFirstPage();
                }
            }
            if (entry.target === sentinel.value && entry.isIntersecting) {
                loadNextPage();
            }
        }),
        { rootMargin: "300px" },
    );
    if (rootEl.value) {
        observer.observe(rootEl.value);
    }
    if (sentinel.value) {
        observer.observe(sentinel.value);
    }
});
onBeforeUnmount(() => observer?.disconnect());

// A filter change re-queries the shell list; reset this location and reload it if it's on screen
// (off-screen locations reset and lazy-load again when scrolled to).
watch(() => props.filter, () => {
    currentPage.value = 0;
    lastPage.value = 1;
    total.value = 0;
    items.value = [];
    if (isVisible.value) {
        loadFirstPage();
    }
}, { deep: true });
</script>
