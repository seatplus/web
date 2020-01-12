<template>
  <div>
    <div class="wrapper">
      <navbar />
      <sidebar />
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                  PAGE HEADER
                  <small>PAGE DESCRIPTION</small>
                </h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Starter Page</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">

          <FlashMessages />
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

  export default {
    name: "Layout",
    components: {
      FlashMessages,
      Sidebar,
      Footer,
      Navbar
    }
  }
</script>

<style scoped>

</style>
