import { createRouter, createWebHistory } from "vue-router";
import store from "./store/store.js";
import Welcome from "./pages/Welcome.vue"


const routes = [

  {
    path: '/',
    component: () => import('./pages/Welcome.vue'),
    name: 'welcome',
  },
  {
    path: '/login',
    component: () => import('./pages/Login.vue'),
    name: 'login',
  },
  {
    path: '/register',
    component: () => import('./pages/Register.vue'),
    name:'register',
  },
  {
    path: '/dashboard',
    component: () => import('./pages/Dashboard.vue'),
    name: 'dashboard',
    meta: { requiresAuth: true },
  },
  {
    path: '/profile',
    component: () => import('./components/ProfilePictureUploader.vue'),
    name: 'profile',
    meta: { requiresAuth: true },
  },
  {
    path: '/add/friends',
    component: () => import('./components/SearchFriend.vue'),
    name: 'search-friends',
    meta: { requiresAuth: true },
  },

]


const router = createRouter({
  history: createWebHistory(),
  routes,

});
// Add a navigation guard to protect the dashboard route
router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
  const isAuthenticated = !!store.state.token; // Check if the user is authenticated (token exists)

  if (requiresAuth && !isAuthenticated) {
    // If the route requires authentication and the user is not authenticated, redirect to the login page
    next({ name: 'login' });
  } else {
    // If the route does not require authentication or the user is authenticated, allow the navigation
    next();
  }
});

export default router;
