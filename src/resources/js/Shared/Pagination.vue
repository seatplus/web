<template>
    <nav aria-label="pagination">
        <ul class="pagination justify-content-end">
            <li :class="['page-item', collection.meta.current_page <= 1 ? 'disabled' : '']">
                <inertia-link class="page-link" :href="collection.links.first" tabindex="-2">First</inertia-link>
            </li>
            <li :class="['page-item', collection.meta.current_page <= 1 ? 'disabled' : '']">
                <inertia-link class="page-link" :href="collection.links.prev" tabindex="-1">Previous</inertia-link>
            </li>

            <li v-for="page in pages" class="page-item" :class="['page-item', isCurrentPage(page) ? 'active' : '']">
                <inertia-link class="page-link" :href="buildHref(page)">{{ page }}</inertia-link>
            </li>

            <li :class="['page-item', collection.meta.current_page >= collection.meta.last_page ? 'disabled' : '']">
                <inertia-link class="page-link" :href="collection.links.next">Next</inertia-link>
            </li>
            <li :class="['page-item', collection.meta.current_page >= collection.meta.last_page ? 'disabled' : '']">
                <inertia-link class="page-link" :href="collection.links.last">Last</inertia-link>
            </li>

        </ul>
    </nav>
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
    methods: {
      isCurrentPage(page) {
        return this.collection.meta.current_page === page;
      },
      buildHref(page) {

        return this.collection.meta.path + '/?page=' + page
      }
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
      }
    }
  }
</script>

<style scoped>

</style>
