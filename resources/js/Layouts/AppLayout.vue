<template>
    <div class="contenedor p-3" id="contenedor-layout">
        <Sidebar />
        <div>
            <!-- <Navbar /> -->
            <main class="ml-5 mr-5">
                <slot></slot>
            </main>
        </div>
    </div>
</template>
<script>
import Sidebar from "./Components/Sidebar.vue";
import Navbar from "./Components/Navbar.vue";
export default {
    components: {
        Sidebar,
        Navbar,
    },
    mounted() {
       /*  $(this.$el).find(".select-filtros").select2({ tags: true }); */
    },
};
</script>
