<template>
  <div class="content-wrapper" @click="closeMenu">
    <!-- <h1>{{runningRequests}}</h1> -->
    <template v-for="tab in tabs">
      <!-- group tab components -->
      <template v-if="tab.group">
        <component
          v-for="tab in tab.group"
          :key="tab.ref"
          :tab="tab"
          :tabs="tabs"
          v-show="tab.ref === activeTab"
          :activeTab="activeTab"
          v-bind:is="tab.component"
        ></component>
      </template>
      <!-- single tab component -->
      <template v-else>
        <component
          :key="tab.ref"
          :tab="tab"
          :tabs="tabs"
          v-show="tab.ref === activeTab"
          :activeTab="activeTab"
          v-bind:is="tab.component"
        ></component>
      </template>
    </template>
  </div>
  <!-- /.content-wrapper -->
</template>

<script>
import {mapState} from 'vuex'
export default {
  props: ["tabs", "activeTab"],
  methods: {
    closeMenu() {
      this.$emit("closeMenu");
    }
  },
  computed:{
    ...mapState(['runningRequests'])
  },
  beforeCreate() {
    $(document).ready(()=>{
        $("#toggleChat").addClass('hideToggleChat')
    })
  },
  beforeDestroy() {
    $("#toggleChat").removeClass('hideToggleChat')
  }
};
</script>

<style scoped>
.content-wrapper {
  width: 100%;
  min-height: 100%;
  position: relative;
  z-index: 1;
}
</style>
