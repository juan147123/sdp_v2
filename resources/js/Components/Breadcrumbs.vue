<template>
    <Preloader v-if="isLoading == true" />
    <div class="breadcrumbs">
        <a :href="this.redirectini"><i class="fa fa-list-alt" aria-hidden="true"></i> Inicio</a>

        <a @click="setPreloader" :href="breadcrumb.url" v-for="breadcrumb in this.modules"><span class="separator">/
            </span><i :class="breadcrumb.icon" aria-hidden="true"></i> {{ breadcrumb.label }}</a>
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
            redirectini: "#"
        };
    },
    mounted() {
        this.setInitUrl();
    },
    methods: {
        setPreloader() {
            this.isLoading = true;
        },
        setInitUrl() {
            if (this.$page.props.permisos.includes(
                this.$env.LIDEROBRACL
            )) {
                this.redirectini = "/redirectpage/obra/colaboradores/cl"
            } else if (this.$page.props.permisos.includes(
                this.$env.APROBOBRA
            )) {
                this.redirectini = "/redirectpage/solicitud/aprobar"
            } else if (this.$page.props.permisos.includes(
                this.$env.APROBVISITADOR
            )) {
                this.redirectini = "/redirectpage/solicitud/visitador/aprobar"
            } else if (this.$page.props.permisos.includes(
                this.$env.ADMRRHH
            )) {
                this.redirectini = "/redirectpage/solicitud/rrhh"
            }
        }
    }
};
</script>
<style scoped>
a {
    font-weight: 400;
}
</style>
