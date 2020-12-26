import axios from 'axios'
import Store from "./../store/store"
// import nProgress from 'nprogress'

const apiClient = axios.create({
  baseURL: "api/conversations",
  withCredentials: false,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': window.csrf
  },
})

apiClient.interceptors.request.use(
  function (config) {
    Store.state.runningRequests += 1;
    return config;
  },
  function (error) {
    Store.state.runningRequests > 0
      ? (Store.state.runningRequests -= 1)
      : null;
    return Promise.reject(error);
  }
);
apiClient.interceptors.response.use(
  function (response) {
    Store.state.runningRequests > 0
      ? (Store.state.runningRequests -= 1)
      : null;
    return response;
  },
  function (error) {
    Store.state.runningRequests > 0
      ? (Store.state.runningRequests -= 1)
      : null;
    return Promise.reject(error.message);
  }
);

export default {
  fetchConversations(params) {
    return apiClient
      .get("/", {
        params
      })
  },
  fetchPreviousConversations(params) {
    return apiClient
      .get("/", {
        params
      })
  },
  fetchConversation(params) {
    return apiClient
      .get("/" + params.conversation_id, {
        params
      })
  },
  addNewConversationReply(params) {
    return apiClient.post("/" + params.conversation_id, params
    )
  },
  fetchPreviousConversationReplies(params) {
    return apiClient.get('/' + params.conversation_id + '/previous_replies', {
      params
    })
  }

}
