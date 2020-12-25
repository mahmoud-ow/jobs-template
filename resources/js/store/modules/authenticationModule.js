import UserService from '../../services/AuthService.js'

export const namespaced = true

export const state = {

}

export const mutations = {

}

export const actions = {

  register(context, credentials) {
    return UserService.register(credentials)
  },
  login(context, credentials) {
    return UserService.login(credentials)
  },
  changePassword(context, credentials) {
    return UserService.updatePassword(credentials)
  }
}

export const getters = {

}