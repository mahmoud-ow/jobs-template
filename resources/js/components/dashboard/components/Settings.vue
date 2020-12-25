<template>
  <div class="component-wrapper" style="background-color: #EEE;">
    <div class="loading" :class="{ 'hide-loading': loaded }">
      <div class="lds-dual-ring"></div>
    </div>
    <div class="component-title">
      <h5>{{ tab.title }}</h5>
    </div>
    <div class="component-content px-md-4">
      <div class="row rtl-row-reverse">
        <div class="px-4 mb-5 base-dir text-justify col-md-6 col-xs-12">
          <h4 class="settings-header mb-4 pb-3">سوشيال ميديا</h4>
          <div class="form-group">
            <label for="facebook">فيسبوك</label>
            <input
              v-model="social.facebook"
              type="text"
              class="form-control"
              id="facebook"
            />
          </div>
          <div class="form-group">
            <label for="twitter">تويتر</label>
            <input
              v-model="social.twitter"
              type="text"
              class="form-control"
              id="twitter"
            />
          </div>
          <div class="form-group">
            <label for="youtube">يوتيوب</label>
            <input
              v-model="social.youtube"
              type="text"
              class="form-control"
              id="youtube"
            />
          </div>
          <div class="form-group">
            <label for="instagram">إنستجرام</label>
            <input
              v-model="social.instagram"
              type="text"
              class="form-control"
              id="instagram"
            />
          </div>
        </div>

        <div class="px-4 mb-5 base-dir text-justify col-md-6 col-xs-12">
          <h4 class="settings-header mb-4 pb-3">بيانات التواصل</h4>
          <div class="form-group">
            <label for="whatsapp">الهاتف / واتس آب</label>
            <input
              v-model="social.phone"
              type="text"
              class="form-control"
              id="whatsapp"
            />
          </div>
          <div class="form-group">
            <label for="email">البريد الإلكترونى</label>
            <input
              v-model="social.email"
              type="email"
              class="form-control"
              id="email"
            />
          </div>
        </div>
        <div
          class="px-4 base-dir text-justify col-xs-12 col-md-12 pt-4  border-top"
        >
          <button class="btn btn-success" @click="updateSettings">
            حفظ البيانات
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState} from 'vuex'
export default {
  props: ["tab", "activeTab"],
  data() {
    return {
      loaded: false,
      apiClient: axios.create({
        baseURL: "api/settings",
        withCredentials: false,
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": window.csrf
        }
      }),
      social: {}
    };
  },
  computed: {
    selected() {
      return this.activeTab == this.tab.ref;
    },
    ...mapState(['user'])
  },
  methods: {
    updateSettings() {
      loading();
      this.apiClient.post("/", this.social).then(function(response) {
        window.Toast.fire({
          icon: response.data.status,
          title: response.data.message
        });
      });
    }
  },
  watch: {
    selected: function() {
      if (this.selected && !this.loaded) {
        var self = this;
        this.apiClient.get("/").then(function(response) {
          self.social = response.data["social"];
          self.loaded = true;
        });
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.settings-header {
  color: #03a9f4;
  border-bottom: thin solid #ddd;
}
</style>
