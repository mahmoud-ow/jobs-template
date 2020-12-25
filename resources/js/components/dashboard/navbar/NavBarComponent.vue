<template>
  <div class="navbar-wrapper" id="navbar-wrapper" @click="closeMenu">
    <div class="menu-toggle" @click.stop="toggleMenu">
      <div class="menu-toggle">
        <i class="fas fa-bars" v-show="!menuOpend"></i>
        <i class="fas fa-times" v-show="menuOpend"></i>
      </div>
    </div>
    <div class="text-light px-2 d-none d-md-flex"><h5 class="m-0">لوحة التحكم</h5></div>

    <div class="home-btn">
      <router-link :to="{ name: 'home' }"
        ><i class="fas fa-home"></i
      ></router-link>
    </div>
    <div class="chat-btn" @click.stop="$store.state.visibleChat = true">
      <i class="fab fa-facebook-messenger"></i>
    </div>
    <div class="user-actions-wrapper">
      <div class="user-action auth-info">
        <div class="auth-icon">
          <i class="fas fa-user-circle"></i>
        </div>
        <div class="auth-name">{{ user.username }}</div>
        <div class="auth-avatar">
          <img src="https://i.pravatar.cc/150?img=2" alt="" />
        </div>
      </div>
      <div
        class="user-action user-action-hidden"
        @click="$emit('updatePassword')"
      >
        <div><i class="fas fa-user-lock"></i></div>
        <div>تغير كلمة المرور</div>
      </div>
      <div
        class="user-action user-action-hidden"
        @click="$parent.$refs.logout.click()"
      >
        <div><i class="fas fa-power-off"></i></div>
        <div>تسجيل خروج</div>
      </div>
    </div>
    <!-- /.navUser -->
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  props: ["menuOpend"],
  data() {
    return {
      csrf
    };
  },
  methods: {
    toggleMenu() {
      this.$emit("toggleMenu");
    },
    closeMenu() {
      this.$emit("closeMenu");
    }
  },
  computed: {
    ...mapState(["user"])
  },
  mounted() {
    let lastScrollTop = 0;
    let element = document.getElementById("navbar-wrapper");
    document.addEventListener(
      "scroll",
      function() {
        let st = window.pageYOffset || document.documentElement.scrollTop;
        if (st > lastScrollTop && st > $("#navbar-wrapper").height()) {
          element.style = "transform: translate3d(0, -60px , 0)";
        } else {
          element.style = "transform: translate3d(0, 0 , 0)";
        }
        lastScrollTop = st <= 0 ? 0 : st;
      },
      false
    );
  }
};
</script>

<style lang="scss" scoped>
$main-color: #343a40;
$blue: #00bcd4;

.navbar-wrapper {
  height: 60px;
  width: 100%;
  padding: 0 10px;
  background-color: $main-color;
  box-shadow: 1px 1px 3px #00000040;
  display: flex;
  align-items: center;
  flex-direction: row;
  position: fixed;
  transition: all 0.2s ease;
  z-index: 2;
}

.home-btn {
  margin: 0 10px 0 auto;
}
[dir="rtl"] .home-btn {
  margin: 0 auto 0 10px;
}

.chat-btn {
  margin: 0 10px 0 0 ;
}
[dir="rtl"] .chat-btn {
  margin: 0 0 0 10px;
}

.menu-toggle,
.home-btn,
.chat-btn {
  cursor: pointer;
  width: 44px;
  height: 44px;
  border: thin solid #ffffff21;
  background-color: #52575c;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.menu-toggle:hover,
.home-btn:hover,
.chat-btn:hover {
  box-shadow: 0 0 5px $blue;
  background-color: rgb(43, 43, 43);
}
.home-btn a {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
}
.menu-toggle i,
.home-btn i,
.chat-btn i {
  transition: all 0.2s ease;
  font-size: 24px;
  color: #fff;
}

.user-actions-wrapper {
  height: 44px;
  cursor: pointer;
  border-radius: 44px;
  background-color: #52575c;
  color: #fff;
  transition: all 0.2s ease;
  direction: ltr;
}
.user-action {
  box-sizing: initial;
  height: 44px;
  width: 170px;
  display: flex;
  align-items: center;
  padding: 0 4px;
}

.user-action-hidden {
  margin-top: 20px;
  background-color: #fff;
  color: #52575c;
  transition: all 200ms ease;
  visibility: hidden;
  opacity: 0;
  > div {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  > div:nth-of-type(1) {
    width: 30px;
  }
  > div:nth-of-type(2) {
    flex: 1;
  }
}
.user-action-hidden:last-of-type {
  border-radius: 0 0 5px 5px;
}
.user-action-hidden:hover {
  background-color: #eee !important;
}

/* hovers */
.user-actions-wrapper:hover {
  border-radius: 5px 5px 0 0;
}
.user-actions-wrapper:hover .user-action-hidden {
  margin-top: 0px;
  box-shadow: 0px 0px 5px #13131357;
  visibility: visible;
  opacity: 1;
}

/* auth */
.auth-avatar {
  overflow: hidden;
  width: 36px;
  height: 36px;
  border-radius: 50%;
}
.auth-avatar img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}
.auth-name {
  width: calc(100% - 72px);
  padding: 0 10px;
  text-align: left;
  text-transform: capitalize;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}
.auth-icon {
  width: 36px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 32px;
  color: $blue;
}

@media only screen and (max-width: 600px) {
  .user-action {
    width: 150px;
    font-size: 15px;
  }
}
</style>
