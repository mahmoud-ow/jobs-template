// localization
import { EN_TRANSLATIONS } from './lang/en';
import { AR_TRANSLATIONS } from './lang/ar';
let TRANSLATIONS = null;
if (window.lang == 'en') {
  TRANSLATIONS = EN_TRANSLATIONS
} else {
  TRANSLATIONS = AR_TRANSLATIONS
}


// Modules
import * as auth from '../store/modules/authenticationModule.js'
import * as conversation from '../store/modules/conversationModule.js'
// import * as user from '../store/modules/UserModule.js'



export default {
  state: {
    user: {},
    runningRequests: 0,
    visibleChat: false,
    trans: TRANSLATIONS,
    /* data tables reload */
    usersDTReload: false,
    metaDTReload: false,
  },
  mutations: {
    DATATABLE_RELOAD_STATE(state, newState) {
      state[newState.table_ref + 'DTReload'] = newState.reload_state;
    }
  },
  actions: {
    dataTableReloadState(context, newState) {
      context.commit('DATATABLE_RELOAD_STATE', newState)
    }
  },
  getters: {},
  modules: {
    auth,
    conversation
  }
}