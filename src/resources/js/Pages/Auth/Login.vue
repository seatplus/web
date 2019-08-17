<template>
  <div>
    <div class="login-box">
      <flash-messages/>
      <div class="login-logo">
        <a href="../../index2.html"><b>Admin</b>LTE</a>
      </div>
      <!-- /.login-logo -->
      <div class="card">

        <div class="card-body login-card-body">
          {{ $page.login_welcome }}
          <div class="social-auth-links text-center mb-3">
            <a :href="route('auth.eve')">Inertia Link 1</a>
            <inertia-link :href="route('auth.eve')">Inertia Link 2</inertia-link>
            <a :href="route('auth.eve')">
              <img :src="$page.evesso_img_src">
            </a>
          </div>
        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
  </div>
</template>

<script>
  import FlashMessages from '@/Shared/FlashMessages'

  export default {
    components: {
      FlashMessages,
    },
    name      : "Login"


  }
</script>

<style scoped>

</style>