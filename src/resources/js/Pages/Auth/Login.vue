<template>
  <div class="login-box">
    FLASHMESSAGE
    <div class="login-logo">
      <a href="../../index2.html"><b>Admin</b>LTE</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">

      <div class="card-body login-card-body">
        <div class="social-auth-links text-center mb-3">
          <a :href="route('auth.eve')">Inertia Link 1</a>
          <inertia-link :href="route('auth.eve')">Inertia Link 2</inertia-link>
          <!--<a href="route('auth.eve')">
            <img src="./img/evesso.png">
          </a>-->
        </div>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
</template>

<script>
  export default {
    name: "Login"


  }
</script>

<style scoped>

</style>