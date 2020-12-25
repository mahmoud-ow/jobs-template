import UserService from '../../services/UsersService.js'

export const namespaced = true

export const state = {

}

export const mutations = {

}

export const actions = {
  changeState(context, data) {
    UserService.changeState(data).then((response) => {
      window.Toast.fire({
        icon: response.data.status,
        title: response.data.message,
      });
      context.dispatch("dataTableReloadState", {
        table_ref: data.tabRef,
        reload_state: true
      }, { root: true });
    })
  }
}

export const getters = {

}