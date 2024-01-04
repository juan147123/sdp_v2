<template>
    <breadcrumbs :modules="breadcrumbs" />
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <i
        class="fas fa-arrow-left mt-4"
        id="arrow-back"
        @click="ChangeView"
    ></i>
    <div
        class="d-flex align-items-center justify-content-between ml-5 mt-3"
    >
        <h5>Usuarios a desvincular</h5>
        <h5 class="m-3">
            Solicitud código:
            {{ this.solicitudesColaborador.codigo }}
        </h5>
    </div>
    <div
        class="text-end m-3"
        v-if="
            this.$page.props.rol.id_rol == 79 ||
            this.$page.props.rol.id_rol == 78
        "
    >
        <div class="btn-group">
            <button
                class="btn btn-outline-primary btn-sm dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                :disabled="this.solicitudesColaborador.status == 3"
            >
                Acciones
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a
                        @click="updateAllStatus(3)"
                        class="dropdown-item"
                        style="cursor: pointer; font-size: 11.5px"
                        ><i class="fas fa-check text-success"></i>
                        Aprobar todos</a
                    >
                </li>
                <li>
                    <a
                        @click="updateAllStatus(2)"
                        class="dropdown-item"
                        style="cursor: pointer; font-size: 11.5px"
                        ><i class="fas fa-times text-danger"></i>
                        Desaprobar todos
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!--    
    <div class="contenedor-solicitud" v-if="this.checkView != true">
        <div class="main-content">
            <div>


                <tableVue
                    :data="this.solicitudesColaborador.solicitud_colaborador"
                    :headers="this.headers"
                    :dataTableOptions="this.dataTableOptions"
                />
                <Modal :archivosList="this.archivosList" />
            </div>
        </div>
    </div>
    <div class="contenedor-checklist" v-else>
        <breadcrumbs :modules="breadcrumbsChecklist" />
        <i
            class="fas fa-arrow-left"
            id="arrow-back"
            @click="getChecklist(false)"
        ></i>
       
        <Checklist :checksUsuario="checksUsuario" />
      
    </div> -->
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import Modal from "./Components/Modal.vue";
import Checklist from "./Checklist/Index.vue";
import Preloader from "@/Components/Preloader.vue";
import { setSwal } from "../../../../Utils/swal";
export default {
    props: ["solicitudesColaborador", "CodigoSolicitud", "idSolicitud"],
    emits: ["ChangeView", "getSolicitudes"],
    components: {
        AppLayout,
        breadcrumbs,
        Modal,
        Checklist,
        Preloader,
    },
    data() {
        var self = this;
        return {
            archivosList: [],
            checkView: false,
            checksUsuario: [],
            mensaje: "",
            isLoadingForm: false,
            ids: [],
            form: this.$inertia.form({
                id: 0,
                status: 0,
                id_solicitud: 0,
                comentario: "",
            }),
            breadcrumbs: [
                {
                    label: "Solicitudes",
                    url: "/redirectPage/solicitud",
                    icon: "fa fa-book",
                },
                {
                    label: "Colaboradores",
                    url: "",
                    icon: "fa fa-users",
                },
            ],
            breadcrumbsChecklist: [
                {
                    label: "Solicitudes",
                    url: "/solicitudes",
                },
                {
                    label: "Colaboradores",
                    url: "",
                },
                {
                    label: "Checklist",
                    url: "",
                },
            ],
            dataTableOptions: {
                responsive: true,
                language: {
                    url: window.location.origin + "/Plugins/Es-es.json",
                },

                columns: [
                    { data: "user_id", width: 90, className: "text-center" },
                    { data: "nombre_completo", className: "text-center" },
                    {
                        data: "sap_maestro_causales_terminos.name",
                        className: "text-center",
                    },
                    { data: "fecha_desvinculacion", className: "text-center" },
                    { data: "redireccion", className: "text-center" },
                    { data: "comentario", className: "text-center" },
                    {
                        data: null,
                        className: "text-center",
                        width: 100,
                        render: function (data, type, row) {
                            const rol = self.$page.props.rol.id_rol;
                            const isObra = rol === 82;
                            var totalChecksRegistrados =
                                row.check_area_colaboradores.filter(
                                    (obj) => obj
                                ).length;
                            var obra = self.solicitudesColaborador.obra;
                            var contadorPendiente =
                                self.$page.props.data.checklist.length -
                                totalChecksRegistrados;
                            var desc = "";
                            var color = "";
                            if ((isObra && row.status == 1) || obra == 1) {
                                desc = "pendiente";
                                color = "info";
                            } else if (
                                (isObra && row.status == 2) ||
                                obra == 1
                            ) {
                                desc = "cancelado";
                                color = "danger";
                            } else if (row.status == 2) {
                                desc = "cancelado";
                                color = "danger";
                            } else if (row.status == 1) {
                                desc =
                                    "checklist pendientes de revisión: " +
                                    contadorPendiente;
                                color = "info";
                            } else {
                                desc = "completo";
                                color = "success";
                            }
                            return `<span class="badge bg-${color} text-white">${desc}</span>`;
                        },
                    },
                    {
                        data: null,
                        className: "text-center",
                        render: function (data, type, row) {
                            const rol = self.$page.props.rol.id_rol;
                            var obra = self.solicitudesColaborador.obra;
                            const isAdmin = rol === 79 || rol === 78;
                            const isObra = rol === 82;
                            const statusCompleto =
                                data.status !== 3 || data.status !== 2;
                            return `<div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-cogs"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones1"  data-bs-toggle="modal" data-bs-target="#modalArchivos"><i class="fas fa-file-alt text-info"></i> Archivos</a></li>
                                            ${
                                                isObra || obra == 1
                                                    ? ""
                                                    : '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones2" ><i class="fas fa-tasks text-primary"></i> Checklist</a></li>'
                                            }
                                            ${
                                                statusCompleto &&
                                                isAdmin &&
                                                row.status != 3
                                                    ? '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones3" ><i class="fas fa-check text-success"></i> Aprobar</a></li>'
                                                    : ""
                                            }
                                            ${
                                                statusCompleto &&
                                                isAdmin &&
                                                row.status != 3
                                                    ? '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones4" ><i class="fas fa-times text-danger"></i> Desaprobar</a></li>'
                                                    : ""
                                            }
                                        </ul>
                                    </div>`;
                        },
                    },
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row)
                        .find("#acciones1")
                        .on("click", function () {
                            self.archivosList = data.archivos;
                        });
                    $(row)
                        .find("#acciones2")
                        .on("click", function () {
                            self.checksUsuario = data.check_area_colaboradores;
                            self.getChecklist(true);
                        });
                    $(row)
                        .find("#acciones3")
                        .on("click", function () {
                            self.updateStatus(data.id, 3, data.id_solicitud);
                        });
                    $(row)
                        .find("#acciones4")
                        .on("click", function () {
                            self.updateStatus(data.id, 2, data.id_solicitud);
                        });
                },
            },

            headers: [
                "NP Usuario",
                "Nombres y Apellidos",
                "Motivo de desvinculación",
                "Fecha de desvinculación",
                "Correo de redirección",
                "Comentarios",
                "Estado",
                "Acciones",
            ],
        };
    },
    mounted() {},
    methods: {
        ChangeView() {
            this.$emit("ChangeView");
        },
        getChecklist(value) {
            this.checkView = value;
        },
        async updateStatus(id, status, id_solicitud) {
            this.form.id = id;
            this.form.status = status;
            this.form.id_solicitud = id_solicitud;

            await new Promise((resolve) => {
                setSwal({
                    value: "updateStatusInput",
                    callback: async (comentario) => {
                        resolve();
                        this.update(comentario);
                    },
                });
            });
        },

        update(comentario) {
            this.mensaje = "espere mientras se efectuan los cambios....";
            this.isLoadingForm = true;
            this.form.comentario = comentario;
            this.form.put(this.route("solicitud.colaborador.update.status"), {
                onFinish: () => {
                    this.onFinish();
                },
            });
        },

        onFinish() {
            this.$emit("ChangeView");
            this.$emit("getSolicitudes");
            this.mensaje = "";
            this.isLoadingForm = false;
        },
        async updateAllStatus(status) {
            await new Promise((resolve) => {
                setSwal({
                    value: "updateStatus",
                    callback: async () => {
                        resolve();
                        this.updateAll(status);
                    },
                });
            });
        },
        updateAll(status) {
            this.mensaje = "espere mientras se efectuan los cambios....";
            this.isLoadingForm = true;

            this.solicitudesColaborador.solicitud_colaborador.forEach(
                (solicitudColacorador) => {
                    if (solicitudColacorador.status == 1) {
                        this.ids.push(solicitudColacorador.id);
                    }
                }
            );

            this.$inertia.put(
                this.route("solicitud.colaborador.update.masive"),
                {
                    ids: this.ids,
                    status: status,
                    id_solicitud: this.solicitudesColaborador.id,
                },
                {
                    onFinish: () => {
                        this.onFinish();
                    },
                }
            );
        },
    },
};
</script>
<style scoped>
#arrow-back {
    font-size: 20px;
    margin-top: 15px;
    margin-left: 25px;
    cursor: pointer;
}
</style>
