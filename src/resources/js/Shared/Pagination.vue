<template>
    <!--<nav aria-label="pagination">
        <ul class="pagination justify-content-end">
            <li :class="['page-item', collection.meta.current_page <= 1 ? 'disabled' : '']">
                <inertia-link class="page-link" :href="buildHref(1)" tabindex="-2">First</inertia-link>
            </li>
            <li :class="['page-item', collection.meta.current_page <= 1 ? 'disabled' : '']">
                <inertia-link class="page-link" :href="buildHref(collection.meta.current_page - 1)" tabindex="-1">Previous</inertia-link>
            </li>

            <li v-for="page in pages" class="page-item" :class="['page-item', isCurrentPage(page) ? 'active' : '']">
                <inertia-link class="page-link" :href="buildHref(page)">{{ page }}</inertia-link>
            </li>

            <li :class="['page-item', collection.meta.current_page >= collection.meta.last_page ? 'disabled' : '']">
                <inertia-link class="page-link" :href="buildHref(collection.meta.current_page + 1)">Next</inertia-link>
            </li>
            <li :class="['page-item', collection.meta.current_page >= collection.meta.last_page ? 'disabled' : '']">
                <inertia-link class="page-link" :href="buildHref(collection.meta.last_page)">Last</inertia-link>
            </li

        </ul>
    </nav>-->
    <div class="flex items-center justify-between "><!--removed: bg-white px-4 py-3 border-t border-gray-200  -->
        <div class="flex-1 flex justify-between sm:hidden">
            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                Previous
            </a>
            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                Next
            </a>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm leading-5 text-gray-700">
                    Showing
                    <span class="font-medium">{{collection.meta.from}}</span>
                    to
                    <span class="font-medium">{{collection.meta.to}}</span>
                    of
                    <span class="font-medium">{{collection.meta.total}}</span>
                    results
                </p>
            </div>
            <div>
              <span class="relative z-0 inline-flex shadow-sm">
                  <inertia-link :disabled="this.prevDisabled" :href="buildHref(1)"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm leading-5 font-medium focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                                :class="{ 'text-gray-500 hover:text-gray-400': !prevDisabled, 'text-gray-400 pointer-events-none': prevDisabled }">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd"
                            d="M13.707 5.293a1 1 0 010 1.414L10.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd"/>
                      <path fill-rule="evenodd" d="M7 5a1 1 0 011 1v8a1 1 0 11-2 0V6a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                  </inertia-link>

                  <inertia-link :disabled="prevDisabled" :href="buildHref(collection.meta.current_page - 1)"
                          class="-ml-px relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm leading-5 font-medium focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                          :class="{ 'text-gray-500 hover:text-gray-400': !prevDisabled, 'text-gray-400 pointer-events-none': prevDisabled }">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd"/>
                    </svg>
                  </inertia-link>

                  <span v-for="page in pages">
                    <inertia-link :href="buildHref(page)"
                            class="hidden md:inline-flex -ml-px relative items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium focus:z-10 focus:outline-none focus:border-indigo-300 focus:shadow-outline-indigo active:bg-indigo-200 active:text-gray-700 transition ease-in-out duration-150 hover:bg-indigo-50"
                            :class="{ 'bg-indigo-100 text-indigo-700': tinted && page === currentPage, 'bg-white text-gray-700': page !== currentPage }">
                      {{ page }}
                    </inertia-link>
                  </span>

                  <inertia-link  :disabled="nextDisabled" :href="buildHref(collection.meta.current_page + 1)"
                          class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md sm:rounded-none border border-gray-300 bg-white text-sm leading-5 font-medium focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                          :class="{ 'text-gray-500 hover:text-gray-400': !nextDisabled, 'text-gray-400 pointer-events-none': nextDisabled}">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"/>
                    </svg>
                  </inertia-link>

                  <inertia-link   :href="buildHref(collection.meta.last_page)"
                          class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm leading-5 font-medium focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                          :class="{ 'text-gray-500 hover:text-gray-400': !nextDisabled, 'text-gray-400 pointer-events-none': nextDisabled }">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd"
                            d="M6.293 5.293a1 1 0 000 1.414L9.586 10l-3.293 3.293a1 1 0 001.414 1.414l4-4a1 1 0 000-1.414l-4-4a1 1 0 00-1.414 0z"
                            clip-rule="evenodd"/>
                      <path fill-rule="evenodd" d="M13 5a1 1 0 00-1 1v8a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                  </inertia-link>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Pagination",
        props: {
            collection: {
                type    : Object,
                required: true
            },
            offset: {
                type: Number,
                default: 5
            }
        },
        data() {
            return {
                tinted: true
            }
        },
        methods: {
            isCurrentPage(page) {
                return this.collection.meta.current_page === page;
            },
            buildHref(page) {

                let uri = window.location.search.substring(1);
                let searchParams = new URLSearchParams(uri);

                searchParams.set("page", page);

                return this.collection.meta.path + '?' + searchParams.toString();
            },
        },
        computed: {
            pages() {
                let pages = [];
                let from = this.collection.meta.current_page - Math.floor(this.offset / 2);
                if (from < 1) {
                    from = 1;
                }
                let to = from + this.offset - 1;
                if (to > this.collection.meta.last_page) {
                    to = this.collection.meta.last_page;
                }
                while (from <= to) {
                    pages.push(from);
                    from++;
                }
                return pages;
            },
            currentPage() {
                return this.collection.meta.current_page
            },
            prevDisabled() {
                console.log(this.collection.meta.current_page <= 1)
                return this.collection.meta.current_page <= 1
            },
            nextDisabled() {
                return this.collection.meta.current_page >= this.collection.meta.last_page
            }
        },
    }
</script>

<style scoped>

</style>
