<template>
  <nav class="sticky-top navbar navbar-expand-lg navbar-light bg-light">
    <router-link :to="{ name: 'home' }" class="navbar-brand">
      Navbar
      <span class="sr-only">(current)</span>
    </router-link>
    <button
      class="navbar-toggler"
      type="button"
      data-toggle="collapse"
      data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <!-- <li class="nav-item active">
                    <router-link
                        :to="{ name: 'home' }"
                        class="nav-link d-flex align-items-center"
                    >
                        <i style="font-size:24px" class="fas">&#xf015;</i>
                        &nbsp; {{ lang.home }}
                        <span class="sr-only">(current)</span>
                    </router-link>
                </li> -->
        <li class="nav-item active">
          <router-link
            :to="{ name: 'add-ad' }"
            class="nav-link d-flex align-items-center"
          >
            <i class="fas fa-plus" style="font-size:24px"></i>
            &nbsp; إضافة إعلان
            <span class="sr-only">(current)</span>
          </router-link>
        </li>
        <li class="nav-item dropdown" v-if="!user.logged_in">
          <a
            class="nav-link d-flex align-items-center dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i style="font-size:24px" class="fas fa-user-circle"></i>
            &nbsp; {{ lang.user }}
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <router-link :to="{ name: 'login' }" class="dropdown-item">
              <i style="font-size:24px" class="fas">&#xf502;</i>
              &nbsp; {{ lang.login }}
              <span class="sr-only">(current)</span>
            </router-link>
            <router-link :to="{ name: 'register' }" class="dropdown-item">
              <i style="font-size:24px" class="fas">&#xf234;</i>
              &nbsp; {{ lang.register }}
              <span class="sr-only">(current)</span>
            </router-link>
          </div>
        </li>

        <li class="nav-item dropdown" v-if="user.logged_in">
          <a
            class="nav-link d-flex align-items-center dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i style="font-size:24px" class="fas fa-user-circle"></i>
            &nbsp; {{ user.username }}
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <router-link
              v-if="user.user_type != 'admin'"
              :to="{
                name: 'user',
                params: { id: user.id, username: user.username }
              }"
              class="dropdown-item"
            >
              <i style="font-size:24px" class="fas">&#xf234;</i>
              &nbsp; حسابى
              <span class="sr-only">(current)</span>
            </router-link>
            <router-link
              v-if="user.user_type == 'admin'"
              :to="{ name: 'dashboard' }"
              class="dropdown-item"
            >
              <i style="font-size:24px" class="fas">&#xf234;</i>
              &nbsp; لوحة التحكم
              <span class="sr-only">(current)</span>
            </router-link>
            <a
              style="cursor:pointer;"
              @click="$refs.logout.click()"
              class="dropdown-item"
            >
              <i style="font-size:24px" class="fas">&#xf502;</i>
              &nbsp; تسجيل خروج
              <span class="sr-only">(current)</span>
              <form method="POST" action="/auth/logout" style="display:none;">
                <input type="hidden" name="_token" :value="csrf" />
                <button class="btn btn-success" ref="logout">Logout</button>
              </form>
            </a>
          </div>
        </li>
        <li class="nav-item active">
          <div class="nav-link d-flex align-items-center">
            <i class="fas fa-phone-square" style="font-size:24px ;"></i>
            &nbsp; تواصل معنا
          </div>
        </li>

        <li class="nav-item dropdown">
          <a
            class="nav-link d-flex align-items-center dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i style="font-size:24px" class="fa">&#xf1ab;</i>
            &nbsp; {{ lang.language }}
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a href="/lang/en" class="dropdown-item">
              <img src="../../images/england_48px.png" alt="" />
              &nbsp;
              {{ lang.english }}
              <span class="sr-only">(current)</span>
            </a>
            <a href="/lang/ar" class="dropdown-item">
              <img src="../../images/egypt_48px.png" alt="" />
              &nbsp;
              {{ lang.arabic }}
              <span class="sr-only">(current)</span>
            </a>
          </div>
        </li>
      </ul>
      <form class="form-inline my-2 my-lg-0">
        <input
          class="form-control mr-sm-2"
          type="search"
          :placeholder="lang.searchJob"
          aria-label="Search"
        />
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">
          {{ lang.search }}
        </button>
      </form>
    </div>
  </nav>
</template>

<script>
import { mapState } from "vuex";
export default {
  data() {
    return {
      lang: this.$store.state.trans.navbar,
      csrf
    };
  },
  computed: {
    ...mapState(["user"])
  }
};
</script>

<style scoped>
nav {
  background-color: #fff !important;
  box-shadow: 1px 1px 5px #0000003d;
}
.form-control {
  font-size: 14px !important;
}
</style>
