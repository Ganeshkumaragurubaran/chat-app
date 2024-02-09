import { createStore } from 'vuex';
import groups from './groups';
const store = createStore({
  modules: {
    groups,
  },
  state: {
    user: null,
    token: null, // Add a token state
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    },
    setToken(state, token) {
      state.token = token; // Define a setToken mutation
    },
  },
  getters: {
    isLoggedIn: (state) => {
      return state.token !== null;
    },
  },
});
export default store;