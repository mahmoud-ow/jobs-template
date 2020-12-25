import Store from "../store/store"
import Home from '../views/Home.vue';
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import Profile from '../views/Profile.vue';
import DashBoard from '../views/DashBoard.vue';

const lang = Store.state.trans.router



export default [
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: { title: lang.home }
  },
  {
    path: '/home',
    name: 'home-redirect',
    redirect: { name: "home" }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { title: lang.login, guestOnly: true }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { title: lang.register }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashBoard,
    meta: { title: lang.dashboard, requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/user/:id/:username',
    name: 'user',
    component: Profile,
    props: true,
  },

];


