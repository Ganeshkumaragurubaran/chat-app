import ApiService from './services/ApiService.js'

export const fetchUserDetail = async () => {
  try {
    const response = await ApiService.post('/api/me', null);
    return response.data;
  } catch (error) {
    console.error('Failed to fetch user data:', error);
    throw error;
  }
};