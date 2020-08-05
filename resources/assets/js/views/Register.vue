<template>
  <v-card dark class ="login_box">
    <v-card-title class="d-flex align-end ">
        <h2>Register</h2>
    </v-card-title>
    <v-alert type="error" v-if="has_error && !success && error == 'registration_validation_error'">
      Invalid form
    </v-alert>
    <v-alert type="error" v-else-if="has_error && !success">
      Email is already taken
    </v-alert>
    <v-form
    ref="form"
    v-model="valid"
    @submit.prevent="register"
    >
      <v-text-field
        v-model="name"
        :rules="nameRules"
        label="Name"
        required
      ></v-text-field>

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
        hint="At least 8 characters"
        counter
      ></v-text-field>

      <v-text-field
        v-model="password_confirmation"
        :rules="passwordConfirmationRules"
        type="password"
        label="Confirm password"
      ></v-text-field>

      <v-btn
        :disabled="!valid"
        color="primary"
        class="mr-4"
        type="submit"
      >
        Submit
      </v-btn>
    </v-form>
  </v-card>
 
</template>
<script>
  import User from '../models/user';

  export default {
    data() {
      return {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        has_error: false,
        error: '',
        errors: {},
        valid: false,
        success: false,
        showpass: false,
        passwordRules: [
          v => !!v || 'Password is required.',
          v => v.length >= 8 || 'Min 8 characters',
        ],
        passwordConfirmationRules: [
          v => !!v || 'Password confirmation is required.',
          v => v== this.password || 'Passwords don\'t match.',
        ],
        email: '',
        emailRules: [
          v => !!v || 'E-mail is required',
          v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
        ],
        nameRules: [
          v => !!v || 'Name is required',
        ],
        user: new User('', ''),
      }
    },

    methods: {
      register() {
        var app = this
        this.user.name = this.name
        this.user.email = this.email
        this.user.password = this.password
        console.log(this.user)
        this.$store.dispatch('auth/register', this.user).then(
          () => {
            this.$router.push({name: 'login'});
          },
          error => {
            app.has_error = true
          }
        );
      }
        /*
        this.$auth.register({
          data: {
            name: app.name,
            email: app.email,
            password: app.password,
            password_confirmation: app.password_confirmation
          },
          success: function () {
            app.success = true
            this.$router.push({name: 'login', params: {successRegistrationRedirect: true}})
          },
          error: function (res) {
            console.log(res.response.data.errors)
            app.has_error = true
            app.error = res.response.data.error
            app.errors = res.response.data.errors || {}
          }
        })*/
      
    }
  }
</script>

<style scoped>
  .login_box{
      margin: 100px;
      width: 600px;
      padding:25px;
    }
</style>