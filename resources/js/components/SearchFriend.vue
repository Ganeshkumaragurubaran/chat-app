
<template>
    <div>
        <input v-model="searchQuery" @input="searchUsers" placeholder="Search for friends">
        {{ error }}
        <ul>
            <li v-for="user in searchResults" :key="user.id">
                {{ user.name }}
                <button @click="sendFriendRequest(user.id)">Send Request</button>
            </li>
        </ul>
        <div v-if="friendRequestReceived" class="notification">
            <p>{{ friendRequestData.sender.name }} sent you a friend request!</p>
            <button @click="acceptFriendRequest(friendRequestData.sender.id)">Accept</button>
        </div>
        <div v-if="friendRequestSend" class="notification">
            <p>You sent you a friend request!</p>
        </div>
        <h2>Friend Requests</h2>
        <ul>
            <li v-for="request in friendRequests" :key="request.id">
                {{ request.sender_name }} sent you a friend request!
                <button @click="acceptFriendRequest(request.reciever_id)">Accept</button>
            </li>
        </ul>
    </div>
</template>

<script>
import { ref } from 'vue';
import ApiService from '../services/ApiService';
export default {
    data() {
        return {
            error: null,
            searchQuery: '',
            searchResults: [],
            friendRequestReceived: false,
            friendRequestSend: false,
            friendRequestData: {},
            currentUserId: null,

            friendRequests: [],
        };
    },
    methods: {
        async searchUsers() {
            try {
                const response = await ApiService.get(`/api/search?query=${this.searchQuery}`);
                this.searchResults = response.data.users;
            } catch (error) {
                this.error = error.response.data.message
                console.error('Error searching users:', error);
            }
        },

        async sendFriendRequest(receiverId) {
            try {
                const response = await ApiService.post('/api/send-friend-request', { receiver_id: receiverId });
                console.log("Success", response)
            } catch (error) {
                console.error('Error sending friend request:', error);
            }
        },
        async fetchUserDetail() {
            try {
                const response = await ApiService.post('/api/me', null);
                this.currentUserId = response.data.id;
                console.log("Name", response.data.name)
                return response.data;
            } catch (error) {
                console.error('Failed to fetch user data:', error);
                throw error;
            }
        },
        async fetchFriendRequests() {
            try {
                const response = await ApiService.get('/api/friend-requests');
                this.friendRequests = response.data.friendRequests;
            } catch (error) {
                console.error('Failed to fetch friend requests:', error);
            }
        },
        async acceptFriendRequest(requestId) {
            try {
                // Make an API request to accept the friend request
                const response = await ApiService.post(`/api/friend-requests/${requestId}/accept`);

                // Handle the response as needed
                console.log(response);

                // Update the UI to reflect the accepted friend request
                this.friendRequests = this.friendRequests.filter(request => request.reciever_id !== requestId);
            } catch (error) {
                console.error('Failed to accept friend request:', error);
            }
        },
    },
    mounted() {
        this.fetchFriendRequests();
        this.fetchUserDetail();
        window.Echo.channel('friend-requests')
            .listen('pusher:subscription_succeeded', (data) => {
                // You can handle the subscription success event here if needed
                console.log('Subscription succeeded on friend-requests channel', data);
            })
            .listen('FriendRequestSent', (data) => {
                // Handle the actual event when a friend request is sent
                console.log('FriendRequestSent event received', data);

                const isCurrentUser = data.sender.id === this.currentUserId;
                // Display different messages based on whether the sender is the current user or not
                if (isCurrentUser) {
                    this.friendRequestSend = true;
                    this.friendRequestData = 'You sent a friend request!';
                    console.log('You sent a friend request!');
                    // Display a different message or take any other action for the sender
                } else {
                    this.friendRequestReceived = true;
                    this.friendRequestData = data;
                    console.log(`${data.sender.name} sent you a friend request!`);
                    // Display a message for the user who received the request
                }
            });
    },

};
</script>

<style scoped>
.notification {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
}
</style>