import axios from 'axios';
import store from '../store/store.js';
import router from '../routes';

const apiService = axios.create({
  baseURL: 'http://127.0.0.1:8000',
  // You can set other Axios configurations here, such as headers or timeouts.
});

// Add an interceptor to attach the JWT token to each request.
apiService.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token'); // Retrieve the token from storage.
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) =>  {
    console.log("APISERVICE ERROR",error)

    // If the request returns a 401 Unauthorized status
    if (error.response.status === 401) {
      // Clear the authentication token and redirect to the login page
      store.commit('clearToken');
      router.push({ name: 'login' });
    } else {
      // For other errors, you might want to handle them differently
      // For example, show an error message to the user
      console.error('Request failed:', error.message);
    }

    // Always return a Promise rejection for the error to propagate
    return Promise.reject(error);
  }
);

export default apiService;