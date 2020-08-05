<template>
  <v-card dark class ="login_box">
  <v-card-title class="d-flex align-end ">
      <h2>Login</h2>
    </v-card-title>
    <v-alert type="error" v-if="has_error">
      Error, please try again
    </v-alert>
    <v-form
    ref="form"
    v-model="valid"
    @submit.prevent="login"
    >

      <v-text-field
        v-model="email"
        :rules="emailRules"
        label="E-mail"
        required
      ></v-text-field>

      <v-text-field
        v-model="password"
        :append-icon="showpass ? 'mdi-eye' : 'mdi-eye-off'"
        :rules="passwordRules"
        :type="showpass ? 'text' : 'password'"
        name="input-10-1"
        label="Password"
        @click:append="showpass = !showpass"
      ></v-text-field>

      <v-btn
        :disabled="!valid"
        color="primary"
        class="mr-4"
        type="submit"
      >
        Login
      </v-btn>
    </v-form>
  </v-card>
</template>

<script>
  import User from '../models/user';

  export default {
    data() {
      return {
        
        password: null,
        has_error: false,
        valid: false,
        showpass: false,
        passwordRules: [
          v => !!v || 'Password is required.',
        ],
        email: '',
        emailRules: [
          v => !!v || 'E-mail is required',
        ],
        user: new User('', ''),
      }
    },

    mounted() {
      //
    },

    methods: {
      validate () {
        this.$refs.form.validate()
      },
      login(){
        var app = this
        
        this.user.email = this.email
        this.user.password = this.password
        console.log(this.user)
        this.$store.dispatch('auth/login', this.user).then(
          () => {
            this.$router.push({name: 'Home'});
          },
          error => {
            app.has_error = true
          }
        );
      }
      /*
      login() {
        // get the redirect object
        var redirect = this.$auth.redirect()
        var app = this
        this.$auth.login({
          params: {
            email: app.email,
            password: app.password
          },
          success: function() {
            // handle redirection
            //const redirectTo = redirect ? redirect.from.name : this.$auth.user().role === 2 ? 'admin.dashboard' : 'dashboard'
            console.log(this.$auth)
            this.$router.push({name: 'Home'})
          },
          error: function() {
            app.has_error = true
          },
          rememberMe: true,
          fetchUser: true
        })
      }*/
    },

    created() {//
      if (this.$store.state.auth.status.loggedIn) {//
        this.$router.push({name: 'Home'});//
      }//
    },//
  }
</script>

<style scoped>
  .login_box{
      margin: 100px;
      width: 600px;
      padding:25px;
    }
</style>