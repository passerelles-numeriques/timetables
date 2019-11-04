<template>
  <div class="container">
    <div class="row">
      <h2>Login</h2><br/>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <form @submit="login">
          <div class="form-group">
            <label for="usernameInput">Username</label>
            <input type="text" nmae="username" class="form-control" id="usernameInput" placeholder="Enter username" v-model="name">
          </div>
          <div class="form-group">
            <label for="passwordInput">Password</label>
            <input type="password" class="form-control" name="password" id="passwordInput" placeholder="Password" v-model="password">
          </div>
          <div class="form-check">
            <button type="submit" class="btn btn-primary" @click="login">Submit</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>
<script>
import AdminService from '../services/AdminService';

export default {
  name: 'Login',
  data() {
    return {
      name: '',
      password: '',
      admin: new AdminService(this)
    };
  },
  mounted() {},
  methods: {
    login(e) {
      e.preventDefault();
      this.$emit('loader', true);
      fetch(`${this.baseURL}api/v1/admin?user=${this.name}&password=${this.password}`)
        .then(response => response.json())
        .then(isAdmin => {
          if (isAdmin) {
            sessionStorage.setItem('username', this.name);
            sessionStorage.setItem('password', this.password);
            //redirect to the view that the user tried to access 
            // (before being redirected to this Login view)
            this.$router.push(this.$route.query.redirect);
          } else {
            this.$emit('showErrorMessage', 'Wrong username or password');
          }
          this.$emit('loader', false);
        });
    }
  },
  watch: {
    name: function(newValue, oldValue) {
      this.$emit('showErrorMessage', '');
    },
    password: function(newValue, oldValue) {
      this.$emit('showErrorMessage', '');
    }
  }
};
</script>

<style scoped>
</style>
