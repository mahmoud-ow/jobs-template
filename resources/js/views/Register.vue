<template>
    <div>
        <NavBar />

        <div class="page-label container">
            <div class="page-label-img" data-aos="fade-left">
                <img src="../../images/auth/page-title.svg" alt="" />
                <p>مستخدم جديد</p>
            </div>
            <div class="page-label-line"></div>
        </div>
        <!-- /.page-label -->
        <div class="container mt-md-5 mt-4">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                <div class="col col-md-6 col-sm-12 col-12">
                    <form @submit.prevent="registerUser">
                        <input
                            class="form-control"
                            name="name"
                            type="text"
                            placeholder="الإسم / اللقب"
                            v-model="username"
                            :class="{ error: $v.username.$error }"
                            @blur="$v.username.$touch()"
                        />
                        <template v-if="$v.username.$error">
                            <p
                                v-if="!$v.username.required"
                                class="errorMessage"
                            >
                                &#9888; حقل ( الإسم / اللقب ) مطلوب
                            </p>
                        </template>

                        <input
                            class="form-control mt-3"
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
                            <p
                                v-if="
                                    $v.email.required &&
                                        $v.email.email &&
                                        !$v.email.unique_email
                                "
                                class="errorMessage"
                            >
                                &#9888; ( البريد الإلكترونى ) مستخدم سابقا
                            </p>
                        </template>

                        <input
                            class="form-control mt-3"
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
                            <p
                                v-if="!$v.password.minLength"
                                class="errorMessage"
                            >
                                &#9888; ( كلمة المرور ) 6 أحرف على الأقل
                            </p>
                        </template>

                        <input
                            class="form-control mt-3"
                            name="password_confirmation"
                            type="password"
                            autocomplete="off"
                            placeholder="تأكيد كلمة المرور"
                            v-model="password_confirmation"
                            :class="{ error: $v.password_confirmation.$error }"
                            @blur="$v.password_confirmation.$touch()"
                        />
                        <template v-if="$v.password_confirmation.$error">
                            <p
                                v-if="!$v.password_confirmation.required"
                                class="errorMessage"
                            >
                                &#9888; حقل ( تأكيد كلمة المرور ) مطلوب
                            </p>
                            <p
                                v-if="!$v.password.minLength"
                                class="errorMessage"
                            >
                                &#9888; ( تأكيد كلمة المرور ) 6 أحرف على الأقل
                            </p>
                            <p
                                v-if="
                                    $v.password.minLength &&
                                        !$v.password_confirmation.sameAsPassword
                                "
                                class="errorMessage"
                            >
                                &#9888; ( تأكيد كلمة المرور ) غير متطابق
                            </p>
                        </template>

                        <input
                            class="form-control mt-3"
                            name="phone"
                            type="number"
                            placeholder="رقم الهاتف"
                            v-model.number="phone"
                            :class="{ error: $v.phone.$error }"
                            @blur="$v.phone.$touch()"
                        />
                        <template v-if="$v.phone.$error">
                            <p v-if="!$v.phone.required" class="errorMessage">
                                &#9888; حقل ( رقم الهاتف ) مطلوب
                            </p>
                        </template>

                        <button class="btn btn-success mt-3" type="submit">
                            إنشاء حساب
                            <i style="font-size:16px" class="fas">&#xf234;</i>
                        </button>
                    </form>

                    <div class="user-sugg-links mt-4">
                        <router-link :to="{ name: 'login' }">
                            <i style="font-size:16px" class="fas">&#xf502;</i>
                            تسجيل دخول
                        </router-link>

                        |
                        <a href="/users/login">نسيت كلمة المرور ؟</a>
                    </div>
                </div>
                <div
                    class="col col-md-6 col-sm-12 col-12 page-image-wrapper d-none d-md-flex"
                >
                    <img
                        class="page-image"
                        src="../../images/auth/register.svg"
                        alt=""
                    />
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>
</template>

<script>
import { mapActions } from "vuex";
import {
    required,
    email,
    minLength,
    sameAs,
    helpers
} from "vuelidate/lib/validators";

const available = (value, vm) => vm.valid_email;

export default {
    data() {
        return {
            username: "",
            email: "",
            valid_email: true,
            password: "",
            password_confirmation: "",
            phone: "",
            errors: null
        };
    },
    validations: {
        username: { required },
        email: {
            required,
            email,
            available
        },
        password: { required, minLength: minLength(6) },
        password_confirmation: { required, sameAsPassword: sameAs("password") },
        phone: { required }
    },
    methods: {
        ...mapActions("auth", ["register"]),
        registerUser() {
            this.valid_email = true;
            this.$v.$touch();
            let userInfo = {
                username: this.username,
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation,
                phone: this.phone
            };
            if (!this.$v.$invalid) {
                this.register(userInfo)
                    .then(rsp => {
                        Toast.fire({
                            icon: rsp.data.status,
                            title: rsp.data.message
                        });
                        rsp.data.valid_email
                            ? (this.valid_email = false)
                            : null;

                        if (rsp.data.status == "success") {
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
