<template>
    <breadcrumbs :modules="breadcrumbs" />
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <div>
        <i
            class="fas fa-arrow-left mt-3 ml-2 arrow-back"
            @click="changeViewDetail"
        ></i>
    </div>
    <div class="d-flex align-items-center justify-content-between ml-2 mt-3">
        <h5></h5>
        <h5 class="m-2 bg-white p-2">
            Solicitud:
            {{ this.solicitudesColaborador.codigo }}
        </h5>
    </div>
    <div
        class="text-end m-2"
        v-if="
            this.$page.props.rol.id_rol == 79 ||
            this.$page.props.rol.id_rol == 78
        "
    >
        <div class="btn-group">
            <button
                class="btn btn-primary btn-sm dropdown-toggle mb-3"
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
                        ><i class="fas fa-check text-success"></i> Aprobar
                        todos</a
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
    <div class="contenedor-solicitud" v-if="this.checkView != true">
        <div class="box ml-2 mr-2 mt-1">
            <div class="box-body">
                <table
                    class="table text-nowrap table-bordered dt-responsive"
                    id="tableSolicitudesDetalle"
                >
                    <thead class="table-dark">
                        <tr>
                            <th>NP Usuario</th>
                            <th>Nombres y Apellidos</th>
                            <th>Motivo de desvinculación</th>
                            <th>Fecha de desvinculación</th>
                            <th>Correo de redirección</th>
                            <th>Comentarios</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <Modal :archivosList="this.archivosList" />
            </div>
        </div>
    </div>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import Modal from "./Components/Modal.vue";
import Checklist from "./Checklist/Index.vue";
import Preloader from "@/Components/Preloader.vue";
import { setSwal } from "../../../../Utils/swal";
export default {
    props: [
        "solicitudesColaborador",
        "CodigoSolicitud",
        "idSolicitud",
        "archivosList",
    ],
    emits: ["changeViewDetail", "reloadTable"],
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
                    url: "/redirectpage/solicitud",
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
        };
    },
    mounted() {},
    methods: {
        changeViewDetail() {
            this.$emit("changeViewDetail");
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
            this.$emit("changeViewDetail");
            this.$emit("reloadTable");
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
