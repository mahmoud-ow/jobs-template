<template>
  <div class="menu-wrapper" :class="{ 'visible-menu': menuOpend }">
    <div class="avatar-area">
      <div class="main-avatar-img">
        <img
          src="https://img.icons8.com/bubbles/150/000000/admin-settings-male.png"
        />
      </div>
      <div class="menu-vertical-btns">
        <div @click="closeMenu">
          <div class="menu-side-close">
            <i class="fas fa-times"></i>
          </div>
        </div>
        <div class="action-btn" @click="$emit('updatePassword')">
          <i class="fas fa-user-lock"></i>
        </div>
        <div class="action-btn" @click="$parent.$refs.logout.click()">
          <i class="fas fa-power-off"></i>
        </div>
      </div>
    </div>
    <div class="tabs-wrapper">
      <div class="tabs-content" id="tabs-content">
        <template v-for="tab in tabs">
          <GroupTab
            v-if="tab.group"
            :activeTab="activeTab"
            :activeGroupTab="activeGroupTab"
            :tab="tab"
            @selectedTab="selectedTab"
            @toggleGroupTab="toggleGroupTab"
          />

          <SingleTab
            v-else
            :activeTab="activeTab"
            :tab="tab"
            @selectedTab="selectedTab"
          />
        </template>
      </div>
    </div>
  </div>
  <!-- /.menu-wrapper -->
</template>

<script>
/******* Perfect Scrollbar *******/
import PerfectScrollbar from "perfect-scrollbar";

import SingleTab from "./SingleTab.vue";
import GroupTab from "./GroupTab.vue";
export default {
  props: ["menuOpend", "tabs", "activeTab"],
  data() {
    return {
      activeGroupTab: null
    };
  },
  components: {
    SingleTab,
    GroupTab
  },
  methods: {
    selectedTab(selectedTab) {
      let checkIfSingleTab = this.tabs.filter(
        tab => tab.ref === selectedTab.ref
      );
      checkIfSingleTab.length > 0 ? (this.activeGroupTab = null) : "";
      this.$emit("selectedTab", selectedTab.ref);
    },
    toggleGroupTab(tab) {
      this.activeGroupTab != tab.ref
        ? (this.activeGroupTab = tab.ref)
        : (this.activeGroupTab = null);
    },
    closeMenu() {
      this.$emit("closeMenu");
    }
  },
  mounted() {
    const ps = new PerfectScrollbar(".tabs-content", {
      wheelPropagation: false
    });
  }
};
</script>

<style lang="scss">
@import "~perfect-scrollbar/css/perfect-scrollbar.css";
$active-color: #00bcd4;
$main-color: #343a40;
.tabs-wrapper {
  border: 2px solid #fafafa33;
  border-bottom: 0;
  overflow: hidden;
  padding: 5px;
  margin: 5px;
  border-radius: 5px;
}
.tabs-content {
  position: relative;
  background-color: #ffffff;
  height: calc(100vh - 175px);
  padding: 2px 7px 15px;
  border-bottom: navajowhite;
  box-shadow: 0 0 13px #0000005e inset;
  border-bottom: none;
  border-radius: 3px;

  .ps__thumb-y {
    right: auto !important;
    left: 0px !important;
    background-color: #00bcd4;
    border-radius: 0;
  }
  .ps__rail-y:hover > .ps__thumb-y,
  .ps__rail-y:focus > .ps__thumb-y,
  .ps__rail-y.ps--clicking .ps__thumb-y {
    background-color: #00bcd4;
    width: 100%;
  }
}

.avatar-area {
  height: 175px;
  padding: 0 15px;
  display: flex;
  align-items: center;
  direction: ltr;
  color: #fff;
}

.main-avatar-img {
  justify-content: center;
  border-radius: 50%;
  height: 150px;
  flex: 1;
  width: 140px;
  display: flex;
  padding-right: 22px;
  img {
    object-fit: cover;
    height: 150px;
    width: 150px;
    border-radius: 50%;
    box-shadow: 0px 0px 13px #3a3a3a5e;
    border: 4px solid #fafafa33;
  }
}
.menu-vertical-btns {
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-right: auto;
  margin-top: 66px;
  > div {
    color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid #ddd;
    height: 35px;
    width: 50px;
    margin: 5px 0;
    border: 3px solid #ffffff2e;
    border-radius: 10px;
  }
  > div:nth-of-type(1) {
    position: absolute;
    top: 6px;
    right: 10px;
    border: 1px solid #ffffff21;
    color: #fff;
    width: 50px;
    height: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    cursor: pointer;
    border-radius: 50%;
    font-size: 22px;
    background-color: rgba(255, 255, 255, 0.15);
  }
  > div:nth-of-type(1):hover {
    box-shadow: 0 0 5px #00bcd4;
    background-color: rgb(43, 43, 43);
  }
  .action-btn {
    margin-top: 10px;
  }
  .action-btn:hover {
    cursor: pointer;
    box-shadow: 0 0 5px #00bcd4;
    background-color: #2b2b2b;
  }
}

.menu-wrapper {
  position: fixed;
  top: 0;
  z-index: 2;
  visibility: hidden;
  opacity: 0;
  height: 100%;
  width: 100%;
  max-width: 280px;
  color: $main-color;
  background-color: $main-color;
  transform: translate3d(30%, 0, 0);
  padding: 0 2px;
  box-shadow: 1px 0px 30px #2f3033;
  transition: visibility 0.2s ease, opacity 0.2s ease, transform 0.2s ease;
  background: linear-gradient(
    90deg,
    #5a5a5ab0 0%,
    rgba(24, 24, 24, 0.88) 48%,
    $main-color 99%
  );
}
[dir="ltr"] .menu-wrapper {
  transform: translate3d(-30%, 0, 0);
  background: linear-gradient(
    -90deg,
    #5a5a5ab0 0%,
    rgba(24, 24, 24, 0.88) 48%,
    $main-color 99%
  );
  box-shadow: -1px 0px 30px #2f3033;
}
.visible-menu {
  display: block;
  visibility: visible;
  opacity: 1;
  transform: translate3d(0, 0, 0) !important;
}
#opendMenu {
  transform: translate3d(0, 0, 0);
  visibility: visible;
  opacity: 1;
  transition: visibility 0.2s ease, opacity 0.2s ease, transform 0.2s ease;
}

.tab {
  display: flex;
  padding: 5px 0;
  cursor: pointer;
  text-transform: capitalize;
  text-shadow: 0 0 1px #6c757d;
  background-color: #ffffff;
  color: #343a40;
  box-shadow: 0 0 5px grey;
  border-radius: 3px;
  margin: 5px 0;
}

.tab > div {
  display: flex;
  overflow: hidden;
  white-space: nowrap;
  align-items: center;
  padding: 2px 0;
  height: 28px;
  font-size: 15px;
}
.tab-icon {
  width: 40px;
  justify-content: center;
  border-left: thin solid #c7c7c7;
}
.tab-text {
  flex: 1;
  padding: 2px 10px !important;
}
.tab-chev {
  width: 40px;
  justify-content: center;
}

.childTab {
  background-color: #d4d4d496;
  font-size: 14px;
  margin: 0 !important;
  border-radius: 0;
  box-shadow: none;
  > div {
    font-size: 14px;
  }
}

.tab:hover .checked-tab {
  display: flex !important;
}
.tab.childTab.active-tab,
.childTab:hover {
  background-color: #f1f1f1;
}
.active-tab:not(.childTab),
.tab:hover:not(.childTab),
.active-group-tab .tab:first-of-type {
  background-color: rgb(58, 58, 58);
  color: #fff;
  background: linear-gradient(
    90deg,
    #5a5a5ab0 0%,
    rgba(24, 24, 24, 0.88) 48%,
    #343a40 99%
  );
  box-shadow: 0 0 5px grey;
  border-radius: 3px;
}
.expanded-group-tab {
  box-shadow: 0 0 5px grey;
  border-radius: 3px;
  overflow: hidden;

  .tab:first-of-type {
    margin: 0;
    border-radius: 0;
  }
  .tab {
    border-radius: 0 !important;
    box-shadow: none !important;
    margin: 0 !important;
    border-bottom: thin solid #d6d6d6;
  }
  .tab:last-of-type {
    border-bottom: none;
  }
}
.expanded-group-tab:hover .tab:first-of-type {
  color: #fff;
  background: linear-gradient(
    90deg,
    #5a5a5ab0 0%,
    rgba(24, 24, 24, 0.88) 48%,
    #343a40 99%
  );
}

div.checked-tab {
  display: none;
}
.active-tab .checked-tab {
  display: flex !important;
}
.expanded-group-tab .childTab .checked-tab {
  color: $active-color !important;
}
</style>
