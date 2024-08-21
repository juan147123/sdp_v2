<template>
    <div class="contenedor p-3" id="contenedor-layout">
        <Toast />
        <Sidebar />
        <div>
            <!-- <Navbar /> -->
            <main class="ml-2 mr-2">
                <slot></slot>
            </main>
        </div>
    </div>
</template>
<script>
import Sidebar from "./Components/Sidebar.vue";
import Navbar from "./Components/Navbar.vue";
import Toast from "primevue/toast";
export default {
    components: {
        Sidebar,
        Navbar,
        Toast
    },
    mounted() {
       /*  $(this.$el).find(".select-filtros").select2({ tags: true }); */
    },
};
</script>
