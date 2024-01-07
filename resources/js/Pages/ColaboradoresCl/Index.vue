<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout> s </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import Preloader from "@/Components/Preloader.vue";
import { setSwal } from "../../../Utils/swal";
export default {
    components: {
        AppLayout,
        breadcrumbs,
        SolicitudesColaborador,
        Preloader,
        Checklist,
    },
    data() {
        var self = this;
        return {
            breadcrumbs: [
                {
                    label: "Colaboradores",
                    url: "/redirectpage/colaboradores",
                    icon: "fa fa-users",
                },
            ],
            mensaje: "",
            isLoadingForm: false,
        };
    },
};
</script>
