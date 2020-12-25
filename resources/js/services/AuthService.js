import axios from 'axios'
// import nProgress from 'nprogress'

const apiClient = axios.create({
  baseURL: '/auth',
  withCredentials: false,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': window.csrf
  },
})


export default {
  register(credentials) {
    return apiClient
      .post('/register', credentials)
  },
  login(credentials) {
    return apiClient
      .post('/login', credentials)
  },
  updatePassword(credentials) {
    return apiClient
      .post('/update-password', credentials)
  }
}
