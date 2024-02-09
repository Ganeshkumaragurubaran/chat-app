import { ref, onMounted } from 'vue';
import { fetchFriends, fetchGroups } from './globalServices.js';
import ApiService from '../services/ApiService.js';

export default function () {
  const contacts = ref([]);
  const groups = ref([]);

  const fetchFriendsData = async () => {
    try {
      const friends = await fetchFriends();
      contacts.value = friends;
    } catch (error) {
      // Handle error
      console.error('Failed to fetch friends:', error);
    }
  };

  const fetchGroupsData = async () => {
    try {
      const userGroups = await fetchGroups();
      groups.value = userGroups;
    } catch (error) {
      // Handle error
      console.error('Failed to fetch groups:', error);
    }
  };

  onMounted(() => {
    // Fetch friends and groups data on component mount
    fetchFriendsData();
    fetchGroupsData();

  });

  // You can add more functions or variables here

  return {
    contacts,
    groups,
    // Other variables/functions you want to expose
  };
}