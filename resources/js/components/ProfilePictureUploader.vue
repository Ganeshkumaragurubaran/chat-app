<template>
    <div class="profile-page">
      <div class="profile-section">
        <template v-if="loading">
          <Spinner />
        </template>
        <template v-else>
          <div class="profile-details">
            <h2>Welcome to the Dashboard, {{ user.name }}</h2>
            <p>Email: {{ user.email }}</p>
          </div>
  
          <div class="profile-image-container">
            <div class="profile-image">
              <img :src="'/user-images/' + user?.user_image" alt="Profile Image" />
            </div>
            <div class="profile-image-upload">
              <input type="file" ref="fileInput" @change="handleFileChange" style="display: none" />
              <button class="upload-button" @click="openFileInput">Change Profile Picture</button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </template>
  
<script>
import { ref, onMounted, watchEffect } from 'vue';
import ApiService from '../services/ApiService';
import Swal from 'sweetalert2'
import Spinner from './Spinner.vue'
import { fetchUserDetail } from '../user';

export default {
    components: {
        Spinner,
    },
    
    setup() {
        const fileInput = ref(null);
        const selectedImage = ref(null);
        const user = ref({});
        const loading = ref(false);

        const openFileInput = () => {
            fileInput.value.click();
        };


        const handleFileChange = (e) => {
            selectedImage.value = e.target.files[0];
            uploadImage();
        }

        const uploadImage = async () => {
            const formData = new FormData();
            formData.append('user_image', selectedImage.value);


            try {
                const response = await ApiService.post('api/user/update-image', formData);
                console.log(response)
                Swal.fire({
                    title: "Good job!",
                    text: "You Profile Updated Successfully!",
                    icon: "success"
                });
                selectedImage.value = null;
                UserDetail();
            } catch (error) {
                console.error('Failed to create a blog post:', error);
            }
        };
        const UserDetail = async () => {
            try {
                loading.value = true;
                user.value = await fetchUserDetail();
            } finally {
                loading.value = false;
            }
        }

        watchEffect(() => {
            UserDetail();
        });

        return {
            fileInput,
            selectedImage,
            openFileInput,
            handleFileChange,
            uploadImage,
            user,
            loading

        };
    },
};
</script>

<style scoped>
.profile-page {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.profile-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 800px;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-top: 20px;
}

.profile-details {
  text-align: center;
}

.profile-image-container {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 20px;
}

.profile-image {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  margin-right: 20px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.upload-button {
  background-color: #1877f2;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.upload-button:hover {
  background-color: #166fe5;
}
</style>