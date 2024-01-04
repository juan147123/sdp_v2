<template>
    <div class="breadcrumbs">
       <!--  <a href="#">Inicio</a>

        <a :href="breadcrumb.url" v-for="breadcrumb in this.modules"
            ><span class="separator">/</span>{{ breadcrumb.label }}</a
        > -->
    </div>
</template>
<script>
export default {
    props: ["modules"],
};
</script>
<style scoped></style>
