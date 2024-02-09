<template>
    <div class="dashboard">
        <!-- Sidebar with contacts -->
        <div class="sidebar">
            <div class="profile">
                {{ currentUser.name }}
            </div>
            <div class="sidebar-buttons mt-3">
                <button type="button" class="btn btn-secondary" @click="openSearchFriend">
                    <i class="fas fa-user-plus"></i>
                </button>
                <button type="button" class="btn btn-secondary float-right sidebar-buttons" data-toggle="modal"
                    data-target="#groupModal" data-whatever="@mdo" @click="openGroupModal">
                    <i class="fa-solid fa-people-group"></i>
                </button>
                <button type="button" class="btn btn-secondary" @click="openProfilePictureUploader">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
            <div class="contacts">
                <!-- List of contacts -->
                <div v-for="contact in contacts" :key="contact.id" class="contact"
                    @click="selectItem({ ...contact, type: 'contact' })">
                    <img class="facebook-comment-avatar" :src="getUserImage(contact.user_image)" alt="Image">
                    <div class="contact-info">
                        <h6>{{ contact.name }}</h6>

                    </div>
                </div>
                <div v-for="group in groups" :key="group.id" class="contact"
                    @click="selectItem({ ...group, type: 'group' })">
                    <img class="facebook-comment-avatar" :src="getGroupImage(group.group_image)" alt="Image">
                    <div class="contact-info">
                        <h6>{{ group.name }}</h6>

                    </div>
                </div>
            </div>
        </div>
        <user-modal v-if="showUserModal" :user="selectedContact" :group="selectedGroup" />
        <group-form v-if="showGroupModal" />

        <!-- Main chat area -->
        <div class="main-chat">
            <div class="chat-header">
                <button @click="logout" class="btn btn-danger float-right">Logout</button>
                <div class="contact-info" @click="openUserModal(selectedContact)" data-toggle="modal"
                    data-target="#exampleModalScrollable">
                    <div v-if="selectedContact" class="user-info">
                        <img class="contact-image" :src="getUserImage(selectedContact.user_image)" />
                        <h4>{{ selectedContact.name }}</h4>
                    </div>

                    <!-- Display selectedGroup information if available -->
                    <div v-else-if="selectedGroup" class="user-info">
                        <img class="contact-image" :src="getGroupImage(selectedGroup.group_image)" />
                        <h4>{{ selectedGroup.name }}</h4>
                    </div>

                </div>
            </div>
            <div class="chat-messages" >
                <SearchFriend v-if="showSearchFriend" />
                <ProfilePictureUploader v-if="showProfilePictureUploader" />
                <Chat v-if="isContactOrGroupSelected" :selectedContact="selectedContact" :uid="currentUserId"
                    :selectedGroup="selectedGroup" />

                <!-- Display chat messages here -->
            </div>
            <!-- <div class="chat-input" >
                <textarea placeholder="Type your message..."></textarea>
                <button >Send</button>

            </div> -->
        </div>
    </div>
</template>
  
<script>
import ApiService from '../services/ApiService';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import Chat from '../components/Chat.vue'
import SearchFriend from '../components/SearchFriend.vue';
import { onMounted, ref, watch, computed, } from 'vue';
import UserModal from '../components/UserModal.vue';
import GroupForm from '../components/GroupForm.vue';
import { inject } from 'vue';
import useFetchData from '../utils/useFetchData.js';
import ProfilePictureUploader from '../components/ProfilePictureUploader.vue';


export default {
    components: {
        Chat,
        SearchFriend,
        UserModal,
        GroupForm,
        ProfilePictureUploader,
    },

    setup() {
        const router = useRouter();
        const store = useStore();

        const currentUserId = ref(null)
        const currentUser = ref({})
        const selectedContact = ref(null)
        const selectedGroup = ref(null)
        const showUserModal = ref(false);
        const userData = ref({});
        const showGroupModal = ref(false);
        const showSearchFriend = ref(false);
        const showProfilePictureUploader = ref(false);

        const { contacts, groups } = useFetchData();

        const globalMethods = inject('globalMethods');
        const logout = async () => {
            try {
                await ApiService.post('/api/logout', {},);

                store.commit('setUser', null);
                store.commit('setToken', null);

                localStorage.removeItem('token');

                router.push({ name: 'welcome' });
            } catch (error) {
                console.error('Logout failed', error);
            }
        };
        const selectContact = (contact) => {
            // Update selectedContact when a contact is clicked
            selectedContact.value = contact;

        };
        const selectGroup = (group) => {
            selectedGroup.value = group;
        };
        const selectItem = (item) => {
            if (item.type === 'contact') {
                selectedContact.value = item;
                selectedGroup.value = null;
                showSearchFriend.value = false;
                showProfilePictureUploader.value = false;

            } else if (item.type === 'group') {
                selectedGroup.value = item;
                selectedContact.value = null;
                showSearchFriend.value = false;
                showProfilePictureUploader.value = false;

            } else {
                selectedContact.value = null;
                selectedGroup.value = null;
            }
        };
        const isContactOrGroupSelected = computed(() => {
            // Check if either selectedContact or selectedGroup is truthy
            return !!selectedContact.value || !!selectedGroup.value;
        });
        const fetchUserDetail = async () => {
            try {
                const response = await ApiService.post('/api/me', null);
                currentUserId.value = response.data.id;
                currentUser.value = response.data;
                return response.data;
            } catch (error) {
                console.error('Failed to fetch user data:', error);
                throw error;
            }
        };
        const openUserModal = (user) => {
            showUserModal.value = true;
            userData.value = user;
        };
        const openGroupModal = () => {
            showGroupModal.value = true;
        };
        const openSearchFriend = () => {
            showSearchFriend.value = true;
            showProfilePictureUploader.value = false;
            selectedContact.value = null;
            selectedGroup.value = null;
        };
        const openProfilePictureUploader = () => {
            showProfilePictureUploader.value = true;
            showSearchFriend.value = false;
            selectedContact.value = null;
            selectedGroup.value = null;
        };

        onMounted(() => {
            // fetchgroups();
            // fetchFriends();
            fetchUserDetail();
        });

        return {
            logout,
            contacts,
            selectContact,
            selectedContact,
            currentUserId,
            currentUser,
            openUserModal,
            showUserModal,
            showGroupModal,
            openGroupModal,
            groups,
            selectGroup,
            selectedGroup,
            selectItem,
            getUserImage: globalMethods.getUserImage,
            getGroupImage: globalMethods.getGroupImage,
            showSearchFriend,
            openSearchFriend,
            openProfilePictureUploader,
            showProfilePictureUploader,
            isContactOrGroupSelected,
        }
    },

};
</script>
 
<style scoped>
/* Dashboard styling */
.dashboard {
    display: flex;
    height: 100vh;
}

/* Sidebar styling */
.sidebar-notification {
    padding: 10px;
    border: 1px solid #603030;
    margin-bottom: 5px;
    background-color: #d81313;
    border-radius: 4px;
    cursor: pointer;
}

.sidebar {
    width: 300px;
    background-color: #2c3e50;
    color: #ecf0f1;
    padding: 20px;
    overflow-y: auto;
}

.sidebar-buttons {
    display: flex;
    /* Add this line to make the buttons follow a row layout */
    flex-direction: row;
    /* Optional: Set row as the flex direction (it's the default value) */
    justify-content: space-between;
    /* Optional: Add space between buttons */
}

.profile {
    text-align: center;
}

.contacts {
    margin-top: 30px;
}

.contact {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    cursor: pointer;
    padding: 10px;
    border-bottom: 1px solid #ddd;
    transition: background-color 0.3s ease;
}

.contact:hover {
    background-color: #34495e;
    /* Darker color on hover */
}

.contact img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.contact-info {
    flex-grow: 1;
    /* Take up remaining space */
}

/* Main chat area styling */
.main-chat {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 20px;
}

.chat-header {
    text-align: center;
    margin-bottom: 20px;
}

.user-info {
    display: flex;
    align-items: center;
    /* Align items vertically in the center */
}

.contact-info {
    display: flex;
    align-items: center;
}

.contact-image {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.chat-messages {
    display: flex;
    flex: 1;
    overflow-y: auto;
}

.chat-input {
    display: flex;
    justify-content: space-between;
    /* Space between input and search */
}
.chat-input textarea {
  flex: 1;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-right: 8px;
}

.chat-input button {
  padding: 8px 16px;
  background-color: #4caf50;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
/* Additional styling based on your requirements */
</style>