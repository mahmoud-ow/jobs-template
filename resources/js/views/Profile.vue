<template>
  <div>
     <NavBar />
    <h1>{{this.username}} Profile Page</h1>
  </div>
</template>

<script>
export default {
  props: ['username'],
  mounted () {
    document.title = (this.$route.params.username+" Profile page").replace(/\b\w/g, l => l.toUpperCase())
  }
}
</script>

<style>

</style>