<template>
  <div class="page-container">
    <div class="login-form">
      <h1>Register</h1>
      <form @submit.prevent="registerUser">
        <div class="form-group">
          <label for="name" class="smaller-text">Name:</label>
          <input type="text" id="name" name="name" v-model="name" class="input-field smaller-text" />
          <span class="error smaller-text">{{ errors.name }}</span>
        </div>
        <div class="form-group">
          <label for="email" class="smaller-text">Email:</label>
          <input type="text" id="email" name="email" v-model="email" class="input-field smaller-text" />
          <span class="error smaller-text">{{ errors.email }}</span>
        </div>
        <div class="form-group">
          <label for="password" class="smaller-text">Password:</label>
          <input type="password" id="password" name="password" v-model="password" class="input-field smaller-text" />
          <span class="error smaller-text">{{ errors.password }}</span>
        </div>
        <button type="submit" class="submit-button smaller-text">Submit</button>
      </form>

      <p>
        Login here
        <router-link to="/login" class="active ml-1">Login</router-link>
      </p>

    </div>
  </div>
</template>
    
<script>
import { ref, defineComponent } from 'vue';
import {  useRouter } from 'vue-router'; 

export default defineComponent({

  setup() {
    const name = ref();
    const email = ref();
    const password = ref();
    const errors = ref({
      name: null,
      email: null,
      password: null,
    });
    const router = useRouter(); 

    const registerUser = async () => {
      clearErrors();

      if (!name.value) {
        errors.value.name = 'name is required';
      }
      if (!email.value) {
        errors.value.email = 'email is required';
      }

      if (!password.value) {
        errors.value.password = 'password is required';
      }

      if (email.value && password.value && name.value) {
        try {
          await axios.post('/api/register',  { name: name.value, email: email.value, password: password.value });

          router.push({ name: 'login' });
        } catch (error) {
          console.error('Login failed', error);
        }
      }

    };

    const clearErrors = () => {
      errors.value.name = null;
      errors.value.email = null;
      errors.value.password = null;
    };

    return {
      name,
      email,
      password,
      errors,
      registerUser,
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