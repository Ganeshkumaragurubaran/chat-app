<template>
  <div class="chat-container">
    <div class="chat-messages">
      <div v-for="(message, index) in messages" :key="index" class="message" :class="getMessageClass(message)">
        <div class="message-content">

          {{ message.content }}
        </div>
      </div>
    </div>
    <div class="chat-input">
      <textarea v-model="newMessage"  @keydown.enter.prevent="sendMessage" placeholder="Type your message..." required></textarea>
      <button @click="sendMessage"  v-if="newMessage.trim().length > 0">Send</button>
    </div>
  </div>
</template>


<script>
import { ref, onMounted, watch } from 'vue';
import ApiService from '../services/ApiService';
import NotificationService from '../services/NotificationService';

export default {

  props: {
    selectedContact: Object, // Pass the selected contact from the parent component
    selectedGroup: Object, // Pass the selected contact from the parent component
    uid: Number,
  },
  data(){

  },
  methods: {
    getMessageClass(message) {
      return {
        'sent': message.user_id === props.uid,
        'received': message.user_id !== props.uid,
      };
    },
    showTestNotification() {
      const title = 'Test Notification';
      const options = {
        body: 'This is a sample notification message.',
        icon: '/path/to/icon.png', // Add the path to your notification icon
      };

      NotificationService.showDesktopNotification(title, options);
    },

  },
  setup(props) {
    const messages = ref([]);
    const newMessage = ref('');
    const conversationId = ref(null); // Initially set to null



    const fetchMessages = async () => {
      if (props.selectedContact) {
        try {
          const contactId = props.selectedContact.id;
          if (contactId) {
            const response = await ApiService.get(`/api/conversations/${contactId}/${props.uid}/messages`);
            messages.value = response.data.messages;
            conversationId.value = response.data.conversation_id;
          }
        } catch (error) {
          console.error('Failed to fetch messages:', error);
        }
      } else if (props.selectedGroup) {
        try {
          const groupId = props.selectedGroup.id;
          if (groupId) {
            const response = await ApiService.get(`/api/get/messages/${groupId}`);
            messages.value = response.data.messages;
          }
        } catch (error) {
          console.error('Failed to fetch group messages:', error);
        }
      }
    };
    const sendMessage = async () => {
      try {
        if (props.selectedContact) {
          // Send a message to a user
          const response = await ApiService.post(`/api/conversations/${props.selectedContact.id}/${props.uid}/messages`, {
            content: newMessage.value,
          });
          conversationId.value = response.data.conversation_id;
        } else if (props.selectedGroup) {
          // Send a message to a group
          const response = await ApiService.post(`/api/group/messages/${props.selectedGroup.id}`, {
            content: newMessage.value,
          });
        }

        fetchMessages();
        newMessage.value = '';
      } catch (error) {
        console.error('Failed to send message:', error);
      }
    };


    // Watch for changes in the selectedContact and fetch messages accordingly
    watch(() => props.selectedContact, () => {
      messages.value = []; // Clear existing messages
      fetchMessages();
    });
    watch(() => props.selectedGroup, () => {
      messages.value = []; // Clear existing messages
      fetchMessages();
      if (props.selectedGroup) {
        // Listen for group messages
        window.Echo.channel('group.' + props.selectedGroup.id)
          .listen('GroupMessageSent', (event) => {
            const receivedMessage = event.message;

            messages.value.push({
              id: receivedMessage.id,
              content: receivedMessage.content,
              user_id: receivedMessage.user_id,

            });
            // if (receivedMessage.user_id !== props.uid) {
            //   showNotificationForIncomingMessage(receivedMessage.senderName, receivedMessage.content);
            // }

          });
      }
    });
    watch(() => conversationId.value, (newConversationId) => {
      if (conversationId.value !== null) {
        window.Echo.leave(`conversation.${conversationId.value}`);
      }

      if (newConversationId !== null && newConversationId !== undefined) {
        window.Echo.channel(`conversation.${newConversationId}`)
          .listen('MessageSent', (data) => {
            // Update your UI or perform other actions based on the received message
            const receivedMessage = data.message;
            messages.value.push({
              id: receivedMessage.id,
              content: receivedMessage.content,
              user_id: receivedMessage.user_id,
              // Add other properties as needed
            });
            // Show notification for incoming message
            if (receivedMessage.user_id !== props.uid) {
              showNotificationForIncomingMessage(receivedMessage.senderName, receivedMessage.content);
            }

          });
      }
    });
    const showNotificationForIncomingMessage = (senderName, content) => {
      const title = `New Message from ${senderName}`;
      const options = {
        body: content,
        icon: '/user-images/default-user-image.jpg', // Add the correct path to your notification icon
      };

      NotificationService.showDesktopNotification(title, options);
    };

    const getMessageClass = (message) => {
      return {
        'sent': message.user_id === props.uid,
        'received': message.user_id !== props.uid,
      };
    };

    // Fetch messages when the component is mounted
    onMounted(() => {
      fetchMessages();
      
    });

    return {
      messages,
      newMessage,
      sendMessage,
      getMessageClass
    };
  },
};
</script>


<style scoped>
.sidebar-notification {
  padding: 10px;
  border: 1px solid #ddd;
  margin-bottom: 5px;
  background-color: #f8f8f8;
  border-radius: 4px;
  cursor: pointer;
}

.chat-container {
  display: flex;
  flex-direction: column;
  flex: 1;
  background-color: #f4f4f4;
  padding: 10px;
  border-radius: 10px;
  overflow: hidden;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  margin-top: auto;
  /* Push messages to the top */
}

.message {
  display: flex;
  justify-content: flex-start;
  margin-bottom: 10px;
}

.message-content {
  max-width: 70%;
  padding: 10px;
  border-radius: 8px;
  word-wrap: break-word;
  background-color: #fff;
  /* Chat bubble background color */
}

.received .message-content {
  background-color: #5a5e6046;

  border-top-left-radius: 0;
}

.sent .message-content {
  background-color: #bee0f4;
  border-top-right-radius: 0;
}

.received {
  justify-content: flex-start;
}

.sent {

  justify-content: flex-end;
}


.chat-input {
  display: flex;
  padding: 10px;
  background-color: #fff;
  /* Adjust background color as needed */
  border-top: 1px solid #ddd;
  /* Add a border to separate input from messages */
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
</style>