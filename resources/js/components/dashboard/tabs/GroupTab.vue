<template>
    <div
        class="group-tabs-wrapper"
        :class="{
            'active-group-tab': activeTabChild > 0,
            'expanded-group-tab': tab.ref === activeGroupTab
        }"
    >
        <div class="tab group-tab" @click="toggleGroupTab(tab)">
            <div class="tab-icon" v-html="tab.icon"></div>
            <div class="tab-text">{{ tab.title }}</div>
            <div class="tab-chev">
                <i
                    v-show="activeGroupTab !== tab.ref"
                    class="fas fa-chevron-down"
                ></i>
                <i
                    v-show="activeGroupTab === tab.ref"
                    class="fas fa-chevron-up"
                ></i>
            </div>
        </div>
        <template v-for="childTab in tab.group">
            <SingleTab
                v-show="activeGroupTab === tab.ref"
                class="childTab"
                :activeTab="activeTab"
                :key="childTab.ref"
                :tab="childTab"
                @selectedTab="selectedTab(childTab)"
            />
        </template>
    </div>
</template>

<script>
import SingleTab from "./SingleTab.vue";
export default {
    props: ["tab", "activeTab", "activeGroupTab"],
    components: {
        SingleTab
    },
    methods: {
        selectedTab(selectedTab) {
            this.$emit("selectedTab", selectedTab);
        },
        toggleGroupTab(tab) {
            this.$emit("toggleGroupTab", tab);
        }
    },

    computed: {
        activeTabChild() {
            return this.tab.group.filter(tab => tab.ref == this.activeTab)
                .length;
        }
    }
};
</script>

<style></style>
