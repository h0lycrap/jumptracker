<template>
  <v-app class="app">
    <v-app-bar app dark class="topbar">
      <div class="d-flex align-center">
        <router-link :to="{name: 'Home'}">
          <v-img
            alt="Vuetify Logo"
            class="shrink mr-2"
            contain
            :src="require('./assets/urtlogo.png')"
            transition="scale-transition"
            width="40"
          />
        </router-link>
        <h1>URT Jump Tracker</h1>
      </div>
      <v-spacer />
      <h3 v-if="loggedIn">{{'Welcome ' + $store.state.auth.user.name}}</h3>
      <v-btn  color="primary" class="loginbtn" v-if="!loggedIn" :to="{name: 'register'}">Register</v-btn>
      <v-btn  class="loginbtn" v-if="!loggedIn" :to="{name: 'login'}">Login</v-btn>
      <v-btn  color="error" class="loginbtn" v-if="loggedIn" @click="$store.dispatch('auth/logout'); $router.push({name: 'login'})">Logout</v-btn>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
  
  export default {
    data() {
      return {
        //
      }
    },
    components: {
      
    },

    computed: {
      loggedIn(){
        //return this.$auth.check()
        return this.$store.state.auth.status.loggedIn
      },
    },
  }
</script>

<style scoped>
.topbar {
  background-image: linear-gradient(to right, #111b36, #6e8ad4);
}

.app {
  background-image: linear-gradient(to bottom, #505050 10%, 30%, #000000 60%);
  background-attachment: fixed;
}

.loginbtn{
  margin-left:16px;
}
</style>