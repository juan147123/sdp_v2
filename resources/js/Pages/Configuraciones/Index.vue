<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <breadcrumbs :modules="breadcrumbs" />
        <div class="contenedor-configuracion mt-3">
            <div class="d-flex justify-content-center">
                <Areas
                    :areas="areas"
                    :checklist="checklist"
                    :formArea="formArea"
                    @showChecklist="showChecklist"
                    @onSubmitArea="onSubmitArea"
                    @showDataArea="showDataArea"
                    @cleanDataArea="cleanDataArea"
                    @ondeleteArea="ondeleteArea"
                />
                <Checklist
                    :checklist="checklist"
                    :users="users"
                    :selected="selected"
                    :formCheckList="formCheckList"
                    :formUserArea="formUserArea"
                    @reloadChecklist="reloadChecklist"
                    @showDataChecklist="showDataChecklist"
                    @showDataUser="showDataUser"
                    @onSubmitCheckList="onSubmitCheckList"
                    @onSubmitUserArea="onSubmitUserArea"
                    @ondeleteCK="ondeleteCK"
                    @ondeleteUser="ondeleteUser"
                    @cleanDataChecklist="cleanDataChecklist"
                />
            </div>
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import Preloader from "@/Components/Preloader.vue";
import Areas from "./Areas/Index.vue";
import Checklist from "./Checklist/Index.vue";
import { setSwal } from "../../../Utils/swal.js";
import * as mensajes from "../../../Utils/message";
export default {
    components: {
        AppLayout,
        breadcrumbs,
        Areas,
        Checklist,
        Preloader,
    },
    data() {
        return {
            breadcrumbs: [
                {
                    label: "Configuraciones",
                    url: "/configuraciones/areas",
                },
                {
                    label: "Áreas",
                    url: "/configuraciones/areas",
                },
            ],
            mensaje: "",
            isLoadingForm: false,
            selected: true,
            idSelected: 0,
            areas: [],
            checklist: [],
            users: [],
            formArea: this.$inertia.form({
                id: 0,
                descripcion: "",
                categoria: "area",
                pais: "cl",
            }),
            formCheckList: this.$inertia.form({
                id: 0,
                descripcion: "",
                parent_id: 0,
                categoria: "checklist",
                pais: "cl",
                input: false,
            }),
            formUserArea: this.$inertia.form({
                id: 0,
                email: "",
                name: "",
                id_area: 0,
                rol: "81",
                pais: "cl",
            }),
        };
    },
    mounted() {
        this.areas = this.$page.props.data;
    },
    methods: {
        showChecklist(data) {
            $(".selected").removeClass("selected");
            $("#area" + data.id).addClass("selected");
            this.checklist = data.checklist;
            this.users = data.usuarios;
            this.selected = false;
            this.idSelected = data.id;
        },
        reloadChecklist() {
            this.checklist = [];
            this.users = [];
            this.selected = true;
            $(".selected").removeClass("selected");
            this.idSelected = 0; /* 
            $("#btn-checklist").removeClass("btn-active"); */
        },
        /* TODO AREA */
        onSubmitArea() {
            setSwal({
                value: "updateForm",
                callback:
                    this.formArea.id == 0 ? this.createArea : this.updateArea,
            });
        },
        createArea() {
            this.mensaje = "registrando....";
            this.isLoadingForm = true;
            this.formArea.post(this.route("configuraciones.post"), {
                onFinish: () => {
                    this.onFinishArea();
                },
            });
        },
        updateArea() {
            this.mensaje = "actualizando....";
            this.isLoadingForm = true;
            this.formArea.put(this.route("configuraciones.update"), {
                onFinish: () => {
                    this.onFinishArea();
                },
            });
        },
        ondeleteArea(data) {
            setSwal({
                value: "deleteForm",
                callback: () => {
                    this.deleteArea(data);
                },
            });
        },
        deleteArea(data) {
            this.mensaje = "eliminando....";
            this.isLoadingForm = true;
            this.$inertia.put(
                this.route("configuraciones.delete"),
                {
                    id: data.id,
                },
                {
                    onFinish: () => {
                        this.onFinishArea();
                    },
                }
            );
        },
        onFinishArea() {
            this.areas = this.$page.props.data;
            this.formArea.reset();
            $("#btnCloseModalArea").click();
            this.isLoadingForm = false;
            this.mensaje = "";
            setAlertify({ value: "success" });
        },
        showDataArea(data) {
            this.formArea = this.$inertia.form(data);
        },
        cleanDataArea() {
            this.formArea = this.$inertia.form({
                id: 0,
                descripcion: "",
                categoria: "area",
                pais: "cl",
            });
        },

        /* TODO USER */
        onSubmitCheckList() {
            setSwal({
                value: "createForm",
                callback:
                    this.formCheckList.id == 0
                        ? this.createChecklist
                        : this.updateChecklist,
            });
        },

        onSubmitUserArea() {
            setSwal({
                value: "multioption",
                callback:
                    this.formUserArea.id == 0
                        ? this.createUser
                        : this.updateUser,
            });
        },
        createUser() {
            this.isLoadingForm = true;
            this.mensaje = "registrando...";
            this.formUserArea.id_area = this.idSelected;
            this.formUserArea.post(this.route("usuarios.create"), {
                onFinish: () => {
                    this.onFinishUser();
                },
            });
        },
        updateUser() {
            this.isLoadingForm = true;
            this.mensaje = "actualizando...";
            this.formUserArea.put(this.route("usuarios.update"), {
                onFinish: () => {
                    this.onFinishUser();
                },
            });
        },
        deleteUser(data) {
            this.isLoadingForm = true;
            this.mensaje = "eliminando...";
            this.$inertia.put(
                this.route("usuarios.delete"),
                {
                    id: data.id,
                },
                {
                    onFinish: () => {
                        this.onFinishUser();
                    },
                }
            );
        },
        ondeleteUser(data) {
            setSwal({
                value: "deleteForm",
                callback: () => {
                    this.deleteUser(data);
                },
            });
        },
        /* TODO CHECKLIST */
        createChecklist() {
            this.isLoadingForm = true;
            this.mensaje = "registrando...";
            this.formCheckList.parent_id = this.idSelected;
            this.formCheckList.post(this.route("configuraciones.post"), {
                onFinish: () => {
                    this.onFinishCk();
                },
            });
        },
        updateChecklist() {
            this.isLoadingForm = true;
            this.mensaje = "actualizando...";
            this.formCheckList.parent_id = this.idSelected;
            this.formCheckList.put(this.route("configuraciones.update"), {
                onFinish: () => {
                    this.onFinishCk();
                },
            });
        },

        showDataChecklist(data) {
            if (data.input == 0) {
                data.input = false;
            } else {
                data.input = true;
            }
            this.formCheckList = this.$inertia.form(data);
        },
        showDataUser(data) {
            data.id_area = this.idSelected;
            this.formUserArea = this.$inertia.form(data);
        },

        ondeleteCK(data) {
            setSwal({
                value: "deleteForm",
                callback: () => {
                    this.deleteCheckList(data);
                },
            });
        },
        deleteCheckList(data) {
            this.isLoadingForm = true;
            this.mensaje = "eliminando...";
            this.$inertia.put(
                this.route("configuraciones.delete"),
                {
                    id: data.id,
                },
                {
                    onFinish: () => {
                        this.onFinishCk();
                    },
                }
            );
        },
        cleanDataChecklist() {
            this.formCheckList = this.$inertia.form({
                id: 0,
                descripcion: "",
                parent_id: 0,
                categoria: "checklist",
                pais: "cl",
            });
        },
        onFinishCk() {
            this.isLoadingForm = true;
            this.areas = this.$page.props.data;
            const area_checklist = this.areas.find(
                (area) => area.id === this.idSelected
            );
            this.checklist = area_checklist.checklist;
            $("#btnCloseModalChecklist").click();
            this.isLoadingForm = false;
            this.mensaje = "";
            this.$toast.add({
                severity: "success",
                position: "top-right",
                summary: "Notificación",
                detail: mensajes.MENSAJE_EXITO,
                life: 3000,
            });
        },
        onFinishUser() {
            this.isLoadingForm = true;
            this.users = this.$page.props.data;
            const area_user = this.users.find(
                (user) => user.id === this.idSelected
            );
            this.users = area_user.usuarios;
            this.formUserArea.reset();
            $("#btnCloseModalUserArea").click();
            this.isLoadingForm = false;
            this.mensaje = "";
            this.$toast.add({
                severity: "success",
                position: "top-right",
                summary: "Notificación",
                detail: mensajes.MENSAJE_EXITO,
                life: 3000,
            });
        },
    },
};
</script>
