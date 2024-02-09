<template>
  <div>
    <router-view></router-view>
  </div>
</template>

<script>
import Welcome from './pages/Welcome.vue';
import { provide } from 'vue';
import { getUserImage, getGroupImage } from './utils/globalMethods';
export default {
components:{
  Welcome
},
setup() {
    const globalMethods = {
      getUserImage,
      getGroupImage,
    };

    provide('globalMethods', globalMethods);

    // Your other setup logic goes here

    return {};
  },

}
</script>

<style>

</style>