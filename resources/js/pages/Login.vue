<template>
  <div class="page-container">
    <div class="login-form">
      <h1>Login</h1>
      <form @submit.prevent="submit">
        <div class="form-group">
          <label for="email" class="smaller-text">Email:</label>
          <input type="text" id="email" name="email" v-model="email" class="input-field " />
          <span class="error smaller-text">{{ errors.email }} {{ errors.credentials }}</span>
        </div>
        <div class="form-group">
          <label for="password" class="smaller-text">Password:</label>
          <input type="password" id="password" name="password" v-model="password" class="input-field" />
          <span class="error smaller-text">{{ errors.password }}</span>
        </div>
        <button type="submit" class="submit-button">Submit</button>
      </form>

      <p>
        Sign up here
        <router-link to="/register" class="active ml-1">Register</router-link>
      </p>

    </div>
  </div>
</template>
  
<script>
import { ref, defineComponent } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
export default defineComponent({

  setup() {
    const email = ref('');
    const password = ref('');
    const errors = ref({
      email: null,
      password: null,
      credentials: null
    });
    const store = useStore();
    const router = useRouter();
    

    const submit = async () => {
      clearErrors();
      if (!email.value) {
        errors.value.email = 'email is required';
      }

      if (!password.value) {
        errors.value.password = 'password is required';
      }
      if (email.value && password.value) {
        try {
          const response = await axios.post(`http://127.0.0.1:8000/api/login`, { email: email.value, password: password.value });
          const token = response.data.token;
          localStorage.setItem('token', token);
          store.commit('setUser', response.data.user);
          store.commit('setToken', token);
          router.push({ name: 'dashboard' });
        } catch (error) {
          if (error.response && error.response.status === 401) {
            errors.value.credentials = 'Invalid credentials';
          } else {
            // Handle other errors
            console.error('Login failed', error);
          }
        }

      }



    }

    const clearErrors = () => {
      errors.value.email = null;
      errors.value.password = null;
      errors.value.credentials = null;
    };

    return {
      email,
      password,
      errors,
      submit
    };
  },
});
</script>
  
<style scoped>
.page-container {
  background-color: #f0f0f0;
  /* Background color for the entire page */
  min-height: 100vh;
  /* Ensure the container covers the full viewport height */
  display: flex;
  justify-content: center;
  align-items: center;
}

.login-form {
  max-width: 600px;
  width: 400px;
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
  background-color: #fff;
  text-align: center;
}


h1 {
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 15px;
  text-align: left;
}

label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}

.input-field {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.error {
  color: red;
}

.submit-button {
  width: 100%;
  padding: 10px;
  background-color: #1877f2;
  /* Facebook's blue color */
  color: #fff;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
}

.submit-button:hover {
  background-color: #166fe5;
  /* Darker shade on hover */
}

.forgot-password {
  text-align: center;
  margin-top: 10px;
  color: #1877f2;
  cursor: pointer;
}
</style>