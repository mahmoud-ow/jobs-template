<template>
  <div ref="dashboard">
    <!-- LOG OUT FORM will be submitted by $parent.ref -->
    <form method="POST" action="/auth/logout" style="display:none;">
      <input type="hidden" name="_token" :value="csrf" />
      <button class="btn btn-success" ref="logout"></button>
    </form>

    <NavBarComponent
      :menuOpend="menuOpend"
      @toggleMenu="menuOpend ? (menuOpend = false) : (menuOpend = true)"
      @updatePassword="updatePassword"
      @closeMenu="menuOpend = false"
    />

    <div class="body-wrapper">
      <TabsComponent
        :tabs="tabs"
        :activeTab="activeTab"
        :menuOpend="menuOpend"
        @selectedTab="selectedTab"
        @closeMenu="menuOpend = false"
        @updatePassword="updatePassword"
      />
      <DashboardComponents
        :tabs="tabs"
        :activeTab="activeTab"
        @closeMenu="menuOpend = false"
      />
    </div>
    <!-- /.body-wrapper -->
  </div>
</template>

<script>
/* Secondary Components */
import Statics from "../components/dashboard/components/Statics.vue";
import Users from "../components/dashboard/components/Users.vue";
import NewAdvts from "../components/dashboard/components/NewAdvts.vue";
import CurrentAdvts from "../components/dashboard/components/CurrentAdvts.vue";
import Sections from "../components/dashboard/components/Sections.vue";
import Categories from "../components/dashboard/components/Categories.vue";
import Notifications from "../components/dashboard/components/Notifications.vue";
import Messages from "../components/dashboard/components/Messages.vue";
import MetaTags from "../components/dashboard/components/MetaTags.vue";
import Settings from "../components/dashboard/components/Settings.vue";

const tabs = [
  {
    title: "إحصائيات",
    ref: "statics",
    icon: '<i class="far fa-chart-bar"></i>',
    component: Statics
  },
  {
    title: "الإعلانات",
    ref: "ads",
    icon: '<i class="fab fa-adversal"></i>',
    group: [
      {
        title: "إعلانات جديدة",
        ref: "newAds",
        icon: '<i class="fas fa-check-double"></i>',
        component: NewAdvts
      },
      {
        title: "الإعلانات الحالية",
        ref: "currentAds",
        icon: '<i class="fas fa-ad"></i>',
        component: CurrentAdvts
      }
    ]
  },

  {
    title: "أقسام و تصنيفات",
    ref: "sectionsNCategories",
    icon: '<i class="fas fa-arrows-alt"></i>',
    group: [
      {
        title: "الأقسام الرئيسية",
        ref: "mainSections",
        icon: '<i class="fas fa-sitemap"></i>',
        component: Sections
      },
      {
        title: "تصنيفات الأقسام",
        ref: "sectionsCategories",
        icon: '<i class="fas fa-boxes"></i>',
        component: Categories
      }
    ]
  },
  {
    title: "المستخدمين",
    ref: "users",
    icon: '<i class="fas fa-users"></i>',
    component: Users
  },
  {
    title: "الرسائل الواردة",
    ref: "receivedMessages",
    icon: '<i class="fas fa-inbox"></i>',
    component: Messages
  },
  {
    title: "الإشعارات",
    ref: "notifications",
    icon: '<i class="fas fa-bell"></i>',
    component: Notifications
  },
  {
    title: "وسوم السيو ( META )",
    ref: "metaTags",
    icon: '<i class="fas fa-tachometer-alt"></i>',
    component: MetaTags
  },
  {
    title: "الإعدادات",
    ref: "settings",
    icon: '<i class="fas fa-cogs"></i>',
    component: Settings
  }
];

/* Primary Components */
import TabsComponent from "../components/dashboard/tabs/TabsComponent.vue";
import NavBarComponent from "../components/dashboard/navbar/NavBarComponent.vue";
import DashboardComponents from "../components/dashboard/DashboardComponents.vue";

/* DATATABLES */
import DataTableResponsive from "datatables.net-responsive-bs4";
import DataTableFixed from "datatables.net-fixedheader-bs4";

// depend
import { mapActions } from "vuex";
export default {
  components: {
    DashboardComponents,
    NavBarComponent,
    TabsComponent
  },
  methods: {
    ...mapActions("auth", ["changePassword"]),
    selectedTab(ref) {
      this.activeTab = ref;
      this.menuOpend = false;
    },
    updatePassword() {
      var self = this;
      Swal.fire({
        title: "تغيير كلمة المرور",
        showCancelButton: true,
        confirmButtonText: "حفظ",
        cancelButtonText: "إلغاء",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        showLoaderOnConfirm: true,
        html: `
                <form autocomplete="off">
                    <input autocomplete="off" id="old_password" type="password" placeholder="كلمة المرور القديمة" class="form-control change_password_field text-center">
                    <input autocomplete="off" id="new_password" type="password" placeholder="كلمة المرور الجديدة" class="form-control change_password_field text-center">
                    <input autocomplete="off" id="new_password_confirmation" type="password" placeholder="تأكيد كلمة المرور الجديدة" class="form-control change_password_field text-center">
                </form>`,

        preConfirm: () => {
          var old_password = $("#old_password").val();
          var new_password = $("#new_password").val();
          var new_password_confirmation = $("#new_password_confirmation").val();

          if (!old_password || !new_password || !new_password_confirmation) {
            Swal.showValidationMessage("قم بتعبئة الحقول");
          } else {
            return self
              .changePassword({
                old_password: old_password,
                new_password: new_password,
                new_password_confirmation: new_password_confirmation
              })
              .then(function(response) {
                if (response.data.error == 1) {
                  throw new Error(response.data.message);
                }
                return response.data;
              })
              .catch(error => {
                Swal.showValidationMessage(
                  `${error.toString().replace("Error: ", "")}`
                );
              });
          }
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then(result => {
        if (result.value) {
          window.Toast.fire({
            icon: "success",
            title: result.value.message
          });
        }
      });
    }
  },
  data() {
    return {
      csrf,
      menuOpend: false,
      activeTab: tabs[0].ref,
      tabs
    };
  },
  created() {
    var self = this;
    Vue.nextTick(function() {
      self.activeTab = self.tabs[5].ref; //group[1].ref
    });
  }
};
</script>

<style lang="scss">
@import url("//cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap4.css");
@import url("//cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.css");
@import url("//cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.bootstrap4.css");
[dir='ltr'] .base-dir{
  direction: ltr;
}
[dir='rtl'] .base-dir{
  direction: rtl;
}
[dir='rtl'] .rtl-row-reverse{
  flex-direction: row-reverse;
}
$custom-file-text: (
  en: "Browse",
  es: "Elegir"
);

.body-wrapper {
  min-height: calc(100vh);
  position: relative;
  display: flex;
  padding-top: 60px;
}
body {
  overflow-y: scroll !important;
  padding: 0 !important;
}
.swal2-modal {
  padding: 10px 0 !important;
  .swal2-header {
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
  }
  .change_password_field {
    margin-top: 10px;
    padding: 0 10px !important;
    height: 40px !important;
    display: flex !important;
    align-items: center !important;
  }
  .swal2-validation-message {
    width: 100%;
    margin: 10px 0 0 0 !important;
    font-size: 16px;
    color: red;
  }
  .swal2-actions {
    button {
      padding: 7px 25px;
    }
  }
}
.component-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  direction: ltr;
}

.component-content {
  width: 100%;
  height: 100%;
  padding: 30px 8px 50px;
  position: relative;
  z-index: 1;
}
.loading {
  position: absolute;
  width: 100%;
  height: 100%;
  background-color: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2;
  transition: all 0.2s ease;
}
.hide-loading {
  z-index: 1;
  opacity: 0;
}

.component-title {
  position: relative;
  z-index: 1;
  color: #343a40;
  margin-bottom: 29px;
  background-color: #343a400f;
  padding: 10px;
  direction: rtl;
  border-bottom: thin solid #ddd;
  display: flex;
  align-items: center;
}
[dir="rtl"] .component-title {
  text-align: right;
}
.component-title h5 {
  margin: 0;
}
.component-title button {
  border-radius: 50%;
  border: none;
  padding: 4px 12px;
  background-color: #00bcd4;
  color: #fff;
  text-shadow: 0 0 black;
  box-shadow: 1px 1px 3px grey;
  font-weight: 500;
  text-transform: capitalize;
  display: flex;
  width: 30px;
  height: 30px;
  align-items: center;
  justify-content: center;
  outline: none !important;
  margin: 0 10px 0 0;
}
[dir="rtl"] .component-title button {
  margin: 0 0 0 10px;
}
.component-title button:hover {
  box-shadow: 1px 1px 3px rgb(49, 49, 49);
}
.component-title button:active {
  transform: translate3d(1px, 1px, 0);
}
/* DATATABLES */
.fixedHeader-floating {
  z-index: 1;
  direction: ltr;
}
table.display {
  width: 100% !important;
  border-top: none;
}
table.dataTable tr {
  text-align: justify;
}
[dir="rtl"] table.dataTable {
  direction: rtl;
}
[dir="rtl"]
  table.dataTable.dtr-inline.collapsed
  > tbody
  > tr[role="row"]
  > td.dtr-control:before,
[dir="rtl"]
  table.dataTable.dtr-inline.collapsed
  > tbody
  > tr[role="row"]
  > th.dtr-control:before {
  left: auto;
  right: 5px;
}
[dir="rtl"]
  table.dataTable.dtr-inline.collapsed
  > tbody
  > tr[role="row"]
  > td.dtr-control {
  padding-right: 30px;
  padding-left: 0.75rem;
}
table.dataTable td.ltr {
  direction: ltr;
}
.dt-switch-wrapper {
  transform: translate3d(0px, 2px, 0px);
}
button.dt-btn {
  position: relative;
  margin: -3px 6px 0;
  transform: translate3d(0px, 1px, 0px);
  border: none;
  padding: 3px 14px;
  box-shadow: 1px 1px 3px grey;
  outline: none;
}
button.dt-btn:hover {
  box-shadow: 1px 1px 5px rgb(75, 75, 75);
}
.dt-btn:active {
  transform: translate3d(1px, 2px, 0px) !important;
}
/* TOGGLE SWITCH */
.checkbox {
  display: none;
}
.switch {
  align-items: center;
  background-color: gray;
  border-radius: 500px;
  cursor: pointer;
  display: flex;
  height: 24px;
  justify-content: space-between;
  padding: 0 10px;
  position: relative;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  width: 60px;
  display: flex !important;
  margin: 0;
}
.checkbox:checked ~ .switch {
  background-color: #337ab7;
}
.checkbox:not(:checked) ~ .switch {
  background-color: gray;
}
.switch__left,
.switch__right {
  color: white;
  font-weight: bold;
  text-transform: uppercase;
  font-size: 10px;
}
.checkbox:checked ~ .switch .switch__left {
  visibility: hidden;
}
.checkbox:not(:checked) ~ .switch .switch__right {
  visibility: hidden;
}
.switch__circle {
  height: 16px;
  padding: 0 5px;
  position: absolute;
  transition: all 0.1s linear;
  width: 16px;
}
.checkbox:checked ~ .switch .switch__circle {
  left: 0;
  right: calc(100% - 40px);
}
.checkbox:not(:checked) ~ .switch .switch__circle {
  left: calc(100% - 26px);
  right: 0;
}
.switch__circle-inner {
  background-color: white;
  border-radius: 50%;
  display: block;
  height: 16px;
  width: 16px;
}

/* CSS LOADER */
.lds-dual-ring {
  display: inline-block;
  width: 80px;
  height: 80px;
}

.lds-dual-ring:after {
  content: " ";
  display: block;
  width: 64px;
  height: 64px;
  margin: 8px;
  border-radius: 50%;
  border: 6px solid #00bcd4;
  border-color: #00bcd4 transparent #343a40 transparent;
  animation: lds-dual-ring 1.2s linear infinite;
}
@keyframes lds-dual-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.swal2-html-container .lds-dual-ring {
  width: 40px !important;
  height: 40px !important;
  margin: 0 10px;
}
.swal2-html-container .lds-dual-ring:after {
  width: 40px;
  height: 40px;
  margin: 3px;
}
</style>
