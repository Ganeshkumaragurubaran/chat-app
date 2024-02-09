<template>
    <div class="modal fade " id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">

            <div class="modal-content modalBody">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="user-profile">
                        <img class="user-image"  v-if="group"
                          :src="getGroupImage(image)"  alt="User Image">
                        <img class="user-image" v-else
                          :src="getUserImage(image)"  alt="User Image">
                        <h5>{{ name }}</h5>
                    </div>

                    <div v-if="group" class="mt-2">
                        <h6>Group Members:</h6>
                        <ul class="skype-member-list">
                            <li v-for="member in groupMembers" :key="member.id" class="skype-member-item">
                                <img  :src="getUserImage(member.user_image)"  alt="Member Image" class="member-image" />
                                <div class="member-details">
                                    <span class="member-name">{{ member.name }}</span>
                                    <span class="member-email">{{ member.email }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, watchEffect, watch } from 'vue';
import ApiService from '../services/ApiService';
import { inject } from 'vue';

export default {
    props: {
        user: Object,
        group: Object,
    },

    setup(props) {
        const name = ref(null);
        const image = ref(null);
        const groupId = ref(null);
        const groupMembers = ref([]);
        const globalMethods = inject('globalMethods');



        const fetchGroupMembers = async (newGroupId) => {
            try {
                const response = await ApiService.get(`/api/get/group/${newGroupId}`);
                groupMembers.value = response.data.group
                console.log("groupMembers.value", groupMembers.value)
            } catch (error) {
                console.log("Error fetching", error)
            }
        }

        // Watch for changes in the 'user' prop
        watchEffect(() => {
            if (props.user) {
                name.value = props.user.name;
                image.value = props.user.user_image;
                groupId.value = null; // Reset groupId if user prop is present
            } else if (props.group) {
                groupId.value = props.group.id;
                name.value = props.group.name;
                image.value = props.group.group_image;
                fetchGroupMembers(groupId.value); // Fetch group members immediately
                console.log("group id:", groupId.value)
            }
        });
        watch(groupId, (newGroupId) => {
            if (newGroupId !== null) {
                fetchGroupMembers(newGroupId);
            }
        });

        return {
            name,
            image,
            groupMembers,
            getUserImage: globalMethods.getUserImage,
            getGroupImage: globalMethods.getGroupImage,


        }
    }
}
</script>


<style scoped>
.modal-header {
    background-color: #2c3e50;
}

.facebook-comments {
    list-style: none;
    padding: 0;
}

.user-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.user-image {
    width: 100px;
    /* Adjust the width as needed */
    height: 100px;
    /* Adjust the height as needed */
    border-radius: 50%;
    margin-bottom: 10px;
}

.skype-member-list {
  list-style: none;
  padding: 0;
}

.skype-member-item {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.member-image {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  margin-right: 10px;
}

.member-details {
  flex: 1;
}

.member-name {
  font-weight: bold;
  display: block;
}

.member-email {
  color: #555;
  display: block;
}
.custom-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;

}

</style>