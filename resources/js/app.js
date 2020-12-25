import 'babel-polyfill'
require('./bootstrap');
window.Vue = require('vue');


/******* NProgress *******/
import 'nprogress/nprogress.css'

/******* Sweet Alert *******/
window.Swal = require('sweetalert2')
window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})
window.ToastLoading = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})
window.loading = () => {
    ToastLoading.fire({
        icon: "info",
        html: '<div class="lds-dual-ring"></div>'
    });
}




/******* VUEX *******/
import Vuex from 'vuex'
import Store from "./store/store"
const store = new Vuex.Store(Store)

Store.state.user = userInfo

/******* VUELIDATE *******/
import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

/******* AOS *******/
import AOS from 'aos';
import 'aos/dist/aos.css';
AOS.init();

/******* Vue Router *******/
import NProgress, { settings } from 'nprogress'
NProgress.configure({ showSpinner: false });
const { default: VueRouter } = require('vue-router');
Vue.use(VueRouter);
import routes from './router/router';
import Axios from 'axios';
const router = new VueRouter({
    routes,
    mode: "history"
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title
    // Start the route progress bar.
    NProgress.start()
    // check for auth routes ( window.user in index.blade )
    if (to.matched.some(record => record.meta.requiresAuth) && !Store.state.user.logged_in) {
        next('/login?access=required')
    }
    if (to.matched.some(record => record.meta.requiresAdmin) && Store.state.user.logged_in && Store.state.user.user_type != 'admin') {

        next('/login?access=denied')
    }
    if (to.matched.some(record => record.meta.guestOnly) && Store.state.user.logged_in && Store.state.user.user_type == 'admin') {
        next('/dashboard')
        NProgress.done()
    }
    next()
})


router.afterEach(() => {
    // Complete the animation of the route progress bar.
    NProgress.done()
})



/******* GLOBAL COMPONENTS *******/
Vue.component('chat-component', require('./components/ChatComponent.vue').default);
Vue.component('NavBar', require('./components/NavBarComponent.vue').default);
Vue.component('DataTable', require('./components/NavBarComponent.vue').default);

/******* Vue INSTANCE *******/
import { mapState } from 'vuex';
import { state } from './store/modules/conversationModule';
window.App = new Vue({
    router,
    store,
    computed: { ...mapState({
        runningRequests: state => state.runningRequests,
        activeConversation_id: state => state.conversation.activeConversation_id,
        conversation_partner_id: state => state.conversation.conversation_partner_id,
        conversation_last_partner_reply_id: state => state.conversation.conversation_last_partner_reply_id,
    }) },
    data: {
        frequentRequestTimer: 2000,
    },
    methods: {
        /* this method creates frequent Axios requests for services like chat, notifications .. etc */
        frequentRequest: function () {
            var self = this;
            if (self.runningRequests == 0) {
                self.$store.state.runningRequests += 1;
                Axios.get('/api/frequent-request', {
                    params: {
                        auth_id: Store.state.user.id,
                        activeConversation_id: self.activeConversation_id,
                        conversation_partner_id: self.conversation_partner_id,
                        conversation_last_partner_reply_id: self.conversation_last_partner_reply_id
                    }
                }).then((response) => {
                    console.log( JSON.stringify(response.data) )
                    (self.runningRequests > 0) ? self.$store.state.runningRequests -= 1 : null;
                }).catch((err) => {
                    (self.runningRequests > 0) ? self.$store.state.runningRequests -= 1 : null;
                }).then(() => {
                    setTimeout(self.frequentRequest, self.frequentRequestTimer);
                })

                //console.log('YES');
            } else {
                //console.log('no');
                setTimeout(self.frequentRequest, self.frequentRequestTimer)
            }

        }
    },
    mounted() {
        // console.log(JSON.stringify(Store.state.user));
        this.frequentRequest();
    }
}).$mount('#app');

