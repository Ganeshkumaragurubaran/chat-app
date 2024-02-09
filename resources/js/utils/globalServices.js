import ApiService from '../services/ApiService.js'

export const fetchFriends = async () => {
    try {
        const response = await ApiService.get(`/api/get/friend/list`);
        return response.data.friends;
    } catch (error) {
        console.error('Fetch friends failed', error);
        throw error;
    }
};

export const fetchGroups = async () => {
    try {
        const response = await ApiService.get(`/api/get/user/group`);
        return response.data.user_groups;
    } catch (error) {
        console.error('Fetch groups failed', error);
        throw error;
    }
};