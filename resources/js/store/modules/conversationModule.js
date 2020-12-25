import ConversationsService from '../../services/ConversationsService.js'

export const namespaced = true

export const state = {
  activeConversation_id: 0, // current active conversation
  conversation_partner_id: 0, // this is a two people chat system ( <-- partner + current auth )
  conversation_last_partner_reply_id: 0, // this will be set when loading a conversation first time to check for new replies
}

export const mutations = {
  UPDATE_ACTIVE_COVERSATION_ID(state, conversation_id) {
    state.activeConversation_id = conversation_id
  },
  UPDATE_COVERSATION_PARTNER_ID(state, partner_id) {
    state.conversation_partner_id = partner_id
  },
  UPDATE_PARTNER_LAST_REPLY_ID(state, reply_id){
    state.conversation_last_partner_reply_id = reply_id
  }
}

export const actions = {
  updateActiveConversationId(context, conversation_id) {
    context.commit('UPDATE_ACTIVE_COVERSATION_ID', conversation_id)
  },
  updateConversationPartnerId(context, partner_id) {
    context.commit('UPDATE_COVERSATION_PARTNER_ID', partner_id)
  },
  updatePartnerLastReplyId(context, reply_id){
    context.commit('UPDATE_PARTNER_LAST_REPLY_ID', reply_id)
  },

  fetchConversations(context, params) {
    return ConversationsService.fetchConversations(params);
  },
  fetchPreviousConversations(context, params) {
    return ConversationsService.fetchPreviousConversations(params);
  },
  fetchConversation(context, params) {
    return ConversationsService.fetchConversation(params);
  },
  addNewConversationReply(context, params) {
    return ConversationsService.addNewConversationReply(params);
  },
  fetchPreviousConversationReplies(context, params){
    return ConversationsService.fetchPreviousConversationReplies(params);
  }
  
}

export const getters = {

}