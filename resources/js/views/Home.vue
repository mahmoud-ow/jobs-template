<template>
    <div>
        <NavBar />
       
        <SliderComponent />
        
    </div>
</template>

<script>
import SliderComponent from "../components/SliderComponent.vue";
export default {
    components: { SliderComponent },
    data() {
        return {
            csrf
        }
    },
};
</script>

<style></style>
