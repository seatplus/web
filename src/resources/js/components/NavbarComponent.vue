<template>
  <b-navbar class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <b-nav-item data-widget="pushmenu" href="#">
        <i class="fas fa-bars"></i>
      </b-nav-item>

      i'll test smth
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <div id="horizon-worker" >
        <b-nav-item :href="route">
          <i class="fas fa-truck-loading"></i>
          <b-badge variant="primary">3</b-badge>
        </b-nav-item>
      </div>
      <b-tooltip target="horizon-worker" placement="bottomleft" title="online"></b-tooltip>
    </ul>

  </b-navbar>
</template>

<script>
  export default {
    name: "navbar-component",
    props: ['route']
  }
</script>
