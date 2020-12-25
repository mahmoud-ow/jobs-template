import axios from 'axios'
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
