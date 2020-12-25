<template>
    <div>
        <h1>Change Language to {{ lang }}</h1>
    </div>
</template>

<script>
export default {
    props: ["lang"],
    mounted() {
        this.$store.dispatch("changeLanguage", this.lang);
    }
};
</script>

<style></style>
