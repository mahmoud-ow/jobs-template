<template>
    <div>
        <NavBar />

        <div class="page-label container">
            <div class="page-label-img" data-aos="fade-left">
                <img src="../../images/auth/page-title.svg" alt="" />
                <p>تسجيل دخول</p>
            </div>
            <div class="page-label-line"></div>
        </div>
        <!-- /.page-label -->
        <div class="container mt-md-5 mt-4">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                <div class="col col-md-6 col-sm-12 col-12">
                    <template v-if="errors.length && !$v.$invalid">
                        <div
                            class="alert alert-danger alert-dismissible fade show"
                            role="alert"
                            style="direction: ltr;"
                        >
                            <span>{{ errors[0] }}</span>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </template>
                    <template v-if="this.$route.query.source === 'logout'">
                        <div
                            class="alert alert-success alert-dismissible fade show"
                            role="alert"
                            style="direction: ltr;"
                        >
                            <span>تم تسجيل الخروج بنجاح</span>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </template>
                    <template v-if="this.$route.query.access === 'required'">
                        <div
                            class="alert alert-danger alert-dismissible fade show"
                            role="alert"
                            style="direction: ltr;"
                        >
                            <span>قم بتسجيل الدخول للمتابعة</span>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="alert"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </template>

                    <form @submit.prevent="userLogin">
                        <input
                            class="form-control mt-4"
                            name="email"
                            type="email"
                            placeholder="البريد الإلكترونى"
                            v-model="email"
                            :class="{ error: $v.email.$error }"
                            @blur="$v.email.$touch()"
                        />
                        <template v-if="$v.email.$error">
                            <p v-if="!$v.email.required" class="errorMessage">
                                &#9888; حقل ( البريد الإلكترونى ) مطلوب
                            </p>
                            <p v-if="!$v.email.email" class="errorMessage">
                                &#9888; حقل ( البريد الإلكترونى ) غير صحيح
                            </p>
                        </template>

                        <input
                            class="form-control mt-4"
                            name="password"
                            type="password"
                            autocomplete="off"
                            placeholder="كلمة المرور"
                            v-model="password"
                            :class="{ error: $v.password.$error }"
                            @blur="$v.password.$touch()"
                        />
                        <template v-if="$v.password.$error">
                            <p
                                v-if="!$v.password.required"
                                class="errorMessage"
                            >
                                &#9888; حقل ( كلمة المرور ) مطلوب
                            </p>
                        </template>

                        <button class="btn btn-success mt-4" type="submit">
                            تسجيل دخول
                            <i
                                style="font-size:16px"
                                class="fas fa-lock-open"
                            ></i>
                        </button>
                    </form>

                    <div class="user-sugg-links mt-5" style="font-size:14px">
                        <a href="/users/login"
                            ><i style="font-size:16px" class="fas fa-key"></i
                            >&nbsp; نسيت كلمة المرور ؟</a
                        >
                        &nbsp; | &nbsp;
                        <router-link :to="{ name: 'register' }">
                            <i class="fas fa-user-plus"></i>&nbsp; إنشاء حساب
                        </router-link>
                    </div>
                </div>
                <div
                    class="col col-md-6 col-sm-12 col-12 page-image-wrapper d-none d-md-flex"
                >
                    <img
                        class="page-image"
                        src="../../images/auth/login.svg"
                        alt=""
                    />
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import { required, email } from "vuelidate/lib/validators";
import NProgress from "nprogress";

export default {
    data() {
        return {
            email: "",
            password: "",
            errors: []
        };
    },
    validations: {
        email: {
            required,
            email
        },
        password: { required }
    },
    computed:{
        ...mapState(['user'])
    },
    methods: {
        ...mapActions("auth", ["login"]),
        userLogin() {
            var self = this;
            this.$v.$touch();
            let userInfo = {
                email: this.email,
                password: this.password
            };
            this.errors = [];
            if (!this.$v.$invalid) {
                NProgress.start();
                this.login(userInfo)
                    .then(rsp => {
                        NProgress.done();

                        if (rsp.data.status == "success") {
                            this.$store.state.user = rsp.data.user;
                            if(this.user.user_type=='admin'){
                                this.$router.push({ name: "dashboard" });
                            } else {
                                this.$router.push({ name: "home" });
                            }
                        }
                        if (rsp.data.login == "fail") {
                            Toast.fire({
                                icon: rsp.data.status,
                                title: rsp.data.message
                            });
                            this.errors.push(rsp.data.message);
                        }
                    })
                    .catch(err => {});
            }
        }
    }
};
</script>

<style scoped>
.page-label {
    margin: 50px auto;
    position: relative;
    overflow: hidden;
}
.page-label-img {
    width: 25%;
    min-width: 260px;
    display: inline-flex;
    position: relative;
    right: 5%;
    justify-content: center;
    z-index: 1;
}
.page-label-img > img {
    position: absolute;
    width: 100%;
    height: 100%;
}
.page-label-img > p {
    position: relative;
    height: 100%;
    top: 2px;
    margin: 0;
    padding: 20px 0;
    font-size: 20px;
    text-shadow: 1px 1px 2px #4caf5063;
    color: #28a745;
}
.page-label-line {
    border-bottom: thin solid #4fc839;
    position: absolute;
    width: calc(100% - 30px);
    right: 0;
    left: 0;
    bottom: 44%;
    margin: 0 auto;
}

.page-image-wrapper {
    display: flex;
    justify-content: center;
}
.page-image {
    width: 85%;
    height: 85%;
    -o-object-fit: contain;
    object-fit: contain;
}

input.error {
    border-color: red;
}
.errorMessage {
    font-size: 12px;
    padding-top: 3px;
    color: red;
}
</style>
