<template>
    <div class="modal fade" id="groupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="clearform">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Group Name:</label>
                            <input type="text" class="form-control" v-model="groupName" id="recipient-name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="clearform">Close</button>
                    <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#anotherModal"
                        data-dismiss="modal" data-whatever="@mdo">Create</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="anotherModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ groupName }} Participants</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="clearform">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <div class="contacts">
                                <div v-for="friend in friends" :key="friend.id" class="contact"  @click="selectContact(friend.id)">
                                    <img class="facebook-comment-avatar" :src="getUserImage(friend.user_image)" alt="Image">
                                    <div class="contact-info">
                                        <h6>{{ friend.name }}</h6>
                                    </div>
                                    <input type="checkbox" v-model="selectedUsers" :value="friend.id" class="user-checkbox">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="clearform">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" @click="createGroup">Add</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue';
import ApiService from '../services/ApiService';
import { useStore } from 'vuex';
import NotificationService from '../services/NotificationService';

export default {
    methods: {
        getUserImage(image) {
            // Handle user image URL logic here
            return image ? '/user-images/' + image : '/user-images/default-user-image.jpg';
        },
    },

    setup() {
        const groupName = ref(null);
        const groupId = ref(null);
        const friends = ref([]);
        const selectedUsers = ref([]);
        const store = useStore();


        const clearform = () => {
            groupName.value = null;
            selectedUsers.value = []

        }
        const selectContact = (userId) => {
            // Toggle the checkbox value for the clicked contact
            const index = selectedUsers.value.indexOf(userId)
            if (index === -1) {
                selectedUsers.value.push(userId)
            } else {
                selectedUsers.value.splice(index, 1)
            }
        }

        const createGroup = async () => {
            try {
                const response = await ApiService.post(`/api/groups/create`, {
                    groupName: groupName.value,
                    selectedUsers: selectedUsers.value,
                })
                // Extract the groupId from the response
                const newGroupId = response.data.groupId;

                // Set the groupId value
                groupId.value = response.data.group.id

                clearform();

            } catch (error) {
                console.error(error.response.data.message);
            }
        }
        const showNotificationForIncomingMessage = (senderName, content) => {
            const title = `New Message from ${senderName}`;
            const options = {
                body: content,
                icon: '/user-images/default-user-image.jpg', // Add the correct path to your notification icon
            };

            NotificationService.showDesktopNotification(title, options);
        };
        watch(() => groupId.value, (newGroupId) => {
            if (groupId.value !== null) {
                window.Echo.leave(`group.${groupId.value}`);
            }

            if (newGroupId !== null && newGroupId !== undefined) {
                window.Echo.channel(`group.${newGroupId}`)
                    .listen('GroupCreated', (data) => {
                        console.log('GroupMessageSent event received:', data);
                        const message = data.message;
                        showNotificationForIncomingMessage(message.user.name, message.content);
                    });
            }
        });
        const fetchFriends = async () => {
            try {
                const response = await ApiService.get(`/api/get/friend/list`);
                friends.value = response.data.friends
            } catch (error) {
                // Handle logout error here
                console.error('Logout failed', error);
            }
        };
        onMounted(() => {
            fetchFriends();
        });



        return {
            groupName,
            clearform,
            friends,
            selectedUsers,
            createGroup,
            selectContact

        }
    }

}
</script>

<style scoped>
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

.user-checkbox {
    margin-left: 10px;
    /* Adjust the margin as needed */
}
</style>