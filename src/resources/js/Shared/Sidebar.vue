<template>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    {{--<span class="logo-mini"><b>S</b>T</span>--}}
    <span class="brand-text font-weight-light">S<b>e</b>AT plus</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block">Alexander Pierce</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li v-for="item in $page.sidebar" :class="['nav-item', {'has-treeview' : hasTreeview(item)} ]">
          <a :href="!hasTreeview(item) ? route( item.route ) : '#'" class="nav-link">
            <i :class="['nav-icon', item.icon ]"></i>
            <p>
              {{ item.name }}
              <i v-if="hasTreeview(item)" class="right fa fa-angle-left"></i>
            </p>
          </a>
          <ul v-if="hasTreeview(item)" class="nav nav-treeview">
            <li v-for="entry in item.entries" class="nav-item">
              <a :href="route(entry.route)" class="nav-link">
                <i :class="['nav-icon', entry.icon ]"></i>
                <p> {{ entry.name }}</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-item has-treeview menu-open">
          <a href="#" class="nav-link active">
            <i class="nav-icon fab fa-algolia"></i>
            <p>
              Vue Header
              <i class="right fa fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="#" class="nav-link active">
                <i class="fa fa-circle-o nav-icon"></i>
                <p>Active Page</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="fa fa-circle-o nav-icon"></i>
                <p>Inactive Page</p>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
  </aside>
</template>

<script>
  export default {
    name: "Sidebar",

    methods: {
      hasTreeview(item) {
        return item.hasOwnProperty('entries')
      }
    }
  }
</script>

<style scoped>

</style>