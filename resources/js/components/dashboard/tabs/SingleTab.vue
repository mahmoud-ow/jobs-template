<template>
    <div
        @click="selectTab(tab)"
        class="tab"
        :class="{ 'active-tab': tab.ref === activeTab }"
    >
        <div class="tab-icon" v-html="tab.icon"></div>
        <div class="tab-text">{{ tab.title }}</div>
        <div class="checked-tab">&#x21CB;</div>
    </div>
</template>

<script>
export default {
    props: ["tab", "activeTab"],
    methods: {
        selectTab(tab) {
            this.$emit("selectedTab", tab);
        }
    }
};
</script>

<style lang="scss" scoped>
.checked-tab {
    width: 40px;
    align-items: center;
    justify-content: center;
}
</style>
