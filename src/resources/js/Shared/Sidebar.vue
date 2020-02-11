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
                    <EveImage v-if="$page.user.data.main_character"
                        :object="$page.user.data.main_character"
                        :size=256
                        class="img-circle elevation-2"
                        :alt="$page.user.data.main_character.name"
                    />
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{$page.user.data.main_character.name}}</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    <div v-for="(header, index) in $page.sidebar">
                        <li class="nav-header">{{index}}</li>
                        <li v-for="item in header" :class="['nav-item', {'has-treeview' : hasTreeview(item), 'menu-open': hasActiveEntries(item) } ]">
                            <inertia-link v-if="!hasTreeview(item)" :href="!hasTreeview(item) ? route( item.route ) : '#'" :class="['nav-link', {'active' : isActive(item) || hasActiveEntries(item)} ]">
                                <i :class="['nav-icon', item.icon ]"></i>
                                <p>
                                    {{ item.name }}
                                    <i v-if="hasTreeview(item)" class="right fa fa-angle-left"></i>
                                </p>
                            </inertia-link>
                            <a v-else-if="hasTreeview(item)" :href="!hasTreeview(item) ? route( item.route ) : '#'" :class="['nav-link', {'active' : isActive(item) || hasActiveEntries(item)} ]">
                                <i :class="['nav-icon', item.icon ]"></i>
                                <p>
                                    {{ item.name }}
                                    <i v-if="hasTreeview(item)" class="right fa fa-angle-left"></i>
                                </p>
                            </a>
                            <ul v-if="hasTreeview(item)" class="nav nav-treeview">
                                <li v-for="entry in item.entries" class="nav-item">
                                    <inertia-link :href="route(entry.route)" :class="['nav-link', {active: isActive(entry)}]">
                                        <i :class="['nav-icon', entry.icon ]"></i>
                                        <p> {{ entry.name }} </p>
                                    </inertia-link>
                                </li>
                            </ul>
                        </li>
                    </div>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script>
  import EveImage from "./EveImage"
  export default {
      components: {EveImage},
      name: "Sidebar",
    props: {
        main_character: Object
    },
      methods: {
            hasTreeview(item) {

                return item.hasOwnProperty('entries')
            },
            isActive(entry) {

                return window.location.href == route(entry.route).url()
            },
            hasActiveEntries(item) {

                let returnValue = false

                if (item.hasOwnProperty('entries')) {
                    Object.keys(item.entries).forEach(key => {
                        if(this.isActive(item.entries[key])){
                            returnValue = true
                            return false;
                        }
                    })
                }

                return returnValue;
            }
        }
    }
</script>

<style scoped>

</style>
