<template>
    <Preloader v-if="isLoading == true" />
    <div class="breadcrumbs">
        <a href="#"><i class="fa fa-list-alt" aria-hidden="true"></i> Inicio</a>

        <a @click="setPreloader" :href="breadcrumb.url" v-for="breadcrumb in this.modules"
            ><span class="separator">/ </span><i :class="breadcrumb.icon" aria-hidden="true"></i> {{ breadcrumb.label }}</a
        >
    </div>
</template>
<script>
import Preloader from "@/Components/Preloader.vue";
export default {
    props: ["modules"],
    components: {
        Preloader,
    },
    data() {
        return {
            isLoading: false,
        };
    },
    methods:{
        setPreloader() {
            this.isLoading = true;
        },
    }
};
</script>
<style scoped>
a{
    font-weight: bold;
}
</style>
