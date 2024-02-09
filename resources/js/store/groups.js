export default {
    namespaced: true,
    state: {
      groupId: null,
    },
    mutations: {
      setGroupId(state, groupId) {
        state.groupId = groupId;
        state.selectedGroup = groupId;
      },
    },
  };