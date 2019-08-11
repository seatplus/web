<template>
  <b-navbar class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <b-nav-item data-widget="pushmenu" href="#">
        <i class="fas fa-bars"></i>
      </b-nav-item>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <b-spinner variant="primary" label="Spinning" v-if="isLoading"></b-spinner>

      <b-nav-item :href="horizonIndex" v-if="stats.status == 'running'">
        <i class="fas fa-truck-loading"></i>
        <b-badge variant="primary">{{ stats.queue_count }}</b-badge>
      </b-nav-item>

      <b-nav-item :href="horizonError" v-if="stats.status == 'running'">
        <i class="fas fa-bug"></i>
        Route:
        <b-badge variant="danger">{{ stats.error_count }}</b-badge>
      </b-nav-item>

      <b-nav-item :href="horizonIndex" v-if="stats.status == 'paused'">
        <i class="fas fa-pause"></i> Worker is paused
      </b-nav-item>

      <b-nav-item :href="horizonIndex" v-if="stats.status == 'inactive'">
        <i class="fas fa-stop"></i> Worker has stopped
      </b-nav-item>

    </ul>

  </b-navbar>
</template>

<script>
  export default {
    name: "navbar-component",
    props: {
      'horizonIndex': String,
      'horizonError': String
    },

    data() {
      return {
        stats: {}
      }
    },

    mounted() {

      this.refreshStatsPeriodically();

    },

    methods: {

      /**
       * Refresh the stats every period of time.
       */
      refreshStatsPeriodically() {
        Promise.all([

          // Make an ajax request to our server - /queue/status
          axios.get('/queue/status').then(response => this.stats = response.data)

        ]).then(() => {
          this.ready = true;

          this.timeout = setTimeout(() => {
            this.refreshStatsPeriodically(false);
          }, 5000);
        });
      },
    },

    computed : {
      isLoading: function () {
        return $.isEmptyObject(this.stats)
      }
    }
  }
</script>
