import axios from 'axios'
// import nProgress from 'nprogress'

const apiClient = axios.create({
  baseURL: 'api/user',
  withCredentials: false,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': window.csrf
  }
})



export default {
  changeState(data) {
    return apiClient.post('/change-state', data)
  }
}
