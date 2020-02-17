<template>
    <div>
        <div class="wrapper">
            <navbar />
            <sidebar :activeEntryUrl="getActiveSidebarElement()" />
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0 text-dark">
                                    {{ this.pageHeader}}
                                    <small>{{ this.pageDescription}}</small>
                                </h1>
                            </div><!-- /.col -->
                            <!--TODO: Figure out if breadcrumps are needed-->
                            <!--<div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Starter Page</li>
                                </ol>
                            </div>--><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <b-card no-body v-if="$page.user.data.impersonating">
                            <inertia-link  :href="route('impersonate.stop')" class="btn-block btn btn-warning">Stop Impersonate</inertia-link>
                        </b-card>
                        <FlashMessages />
                        <RequiredScopesWarning :v-if="hasRequiredScopes()" :scopes="this.requiredScopes" />
                    </div>
                    <slot />

                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            <Footer />

        </div>
    </div>

</template>

<script>
    import Navbar from "@/Shared/Navbar"
    import Sidebar from "@/Shared/Sidebar"
    import FlashMessages from "@/Shared/FlashMessages"
    import Footer from "@/Shared/Footer"
    import RequiredScopesWarning from "./RequiredScopesWarning"

    export default {
        name: "Layout",
        components: {
            FlashMessages,
            Sidebar,
            Footer,
            Navbar,
            RequiredScopesWarning
        },
        props   : {
            pageHeader: {
                type: String,
                default: 'PAGE HEADER',
                required: true
            },
            pageDescription: {
                type: String,
                default: 'Page Description',
                required: true
            },
            activeSidebarElement: {
                type: String,
                default: null,
                required: false
            },
            requiredScopes: {
                type: Array,
                default: function () {
                    return []
                },
                required: false
            }
        },
        methods: {
            getActiveSidebarElement() {
                return this.activeSidebarElement ?? window.location.href
            },
            hasRequiredScopes() {
                return ! _.isEmpty(this.requiredScopes)
            }
        }
    }
</script>

<style scoped>

</style>
