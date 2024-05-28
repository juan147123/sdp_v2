<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>

        <div id="parte-solicitudes-vista">
            <breadcrumbs :modules="breadcrumbs" />
            <div class="col-md-12 mt-2">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-color-custom-creado elevation-1"><i
                                    class="fas fa-list-ol"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text color-custom-creado">CREADOS</span>
                                <span class="info-box-number">{{ conteoSolicitudes.creados }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-color-custom-pendiente elevation-1"><i
                                    class="fas fa-bookmark"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text color-custom-pendiente">PENDIENTES</span>
                                <span class="info-box-number">{{ conteoSolicitudes.pendientes }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-color-custom-aprobado elevation-1"><i
                                    class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text color-custom-aprobado">APROBADOS</span>
                                <span class="info-box-number">{{ conteoSolicitudes.aprobados }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-color-custom-rechazado elevation-1"><i
                                    class="fas fa-times-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text color-custom-rechazado">RECHAZADOS</span>
                                <span class="info-box-number">{{ conteoSolicitudes.rechazados }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box m-1 mt-3">

                <div class="container-fluid">

                    <div class="box-body">
                        <div class="table-responsive pt-2">
                            <table class="table text-nowrap table-bordered dt-responsive" id="tableSolicitudes">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Código</th>
                                        <th>Solicitante</th>
                                        <th>Fecha de solicitud</th>
                                        <th>Estado</th>
                                        <th>Colaboradores</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hidden" id="parte-solicitudes-detalle">
            <SolicitudesColaborador @changeViewDetail="this.changeViewDetail" @reloadTable="reloadTable"
                :solicitudesColaborador="solicitudesColaborador" :archivosList="archivosList" />
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import { rutaBase } from "../../../Utils/utils.js";
import SolicitudesColaborador from "./SolicitudColaborador/Index.vue";
import { setSwal } from "../../../Utils/swal";
import Preloader from "@/Components/Preloader.vue";

import dayjs from "dayjs";
import { createVNode } from "vue";
export default {
    components: {
        AppLayout,
        breadcrumbs,
        SolicitudesColaborador,
        Preloader,
    },
    data() {
        var self = this;
        return {
            breadcrumbs: [
                {
                    label: "Solicitudes",
                    url: "/redirectpage/solicitud",
                    icon: "fa fa-book",
                },
            ],
            mensaje: "",
            isLoadingForm: false,
            table: [],
            tableDetalle: [],
            part: 0,
            solicitudesColaborador: [],
            archivosList: [],
            checkusuariolist: [],
            form: this.$inertia.form({
                id: 0,
                status: 0,
                id_solicitud: 0,
                comentario: "",
            }),
            conteoSolicitudes: {
                creados: 0,
                pendientes: 0,
                aprobados: 0,
                rechazados: 0
            }
        };
    },
    mounted() {
        this.createTable();
    },
    methods: {
        createTable() {
            var self = this;
            this.table = new DataTable("#tableSolicitudes", {
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
                },
                ajax: {
                    url: rutaBase + "/list/solicitud",
                    dataSrc: "",
                },
                responsive: true,
                loadingRecords: "Cargando...",
                autoFill: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 3, targets: -1 },
                ],
                drawCallback: function (settings) {
                    $("ul.pagination").addClass("pagination-sm");
                },
                createdRow: function (row, data, dataIndex) {
                    $(row)
                        .find(".btnColaboradoresSolicitud")
                        .on("click", function () {
                            self.solicitudesColaborador = data;
                            self.ChangeView(data);
                        });
                },
                columns: [
                    { data: "codigo", className: "text-center", width: 130 },
                    { data: "user_created", className: "text-center" },
                    {
                        data: "created_at",
                        width: 300,
                        className: "text-center",
                        render: function (data, type, row) {
                            return dayjs(data).format("DD/MM/YYYY");
                        },
                    },
                    {
                        data: "estado", className: "text-center", width: 80,
                        render: function (data, type, row) {

                            let claseColor = ''
                            switch (data.descripcion) {
                                case 'CREADO':
                                    claseColor = 'bg-color-custom-creado'
                                    break;
                                case 'PENDIENTE':
                                    claseColor = `bg-color-custom-pendiente`;
                                    break;
                                case 'APROBADO':
                                    claseColor = `bg-color-custom-aprobado`;
                                    break;
                                case 'RECHAZADO':
                                    claseColor = `bg-color-custom-rechazado`;
                                    break;
                            }
                            return `<div><span style="font-size:10.5px;" class="badge text-white ${claseColor}">${data.descripcion}</span></div>`;

                        },
                    },
                    {
                        data: null,
                        width: 100,
                        className: "text-center",
                        render: function (data, type, row) {
                            return `<button style="font-size: 12px;" class="btn btn-outline-primary btn-sm btnColaboradoresSolicitud"><i class="fas fa-users"></i></button>`;
                        },
                    },
                ],
                initComplete: function (settings, json) {
                    const response = json || [];
                    console.log(response);
                    self.conteoSolicitudes.creados = response.filter(r => r.estado.descripcion == 'CREADO').length;
                    self.conteoSolicitudes.pendientes = response.filter(r => r.estado.descripcion == 'PENDIENTE').length;
                    self.conteoSolicitudes.aprobados = response.filter(r => r.estado.descripcion == 'APROBADO').length;
                    self.conteoSolicitudes.rechazados = response.filter(r => r.estado.descripcion == 'RECHAZADO').length;
                }
            });
        },
        createTableDetalle(data) {
            var self = this;
            this.tableDetalle = new DataTable("#tableSolicitudesDetalle", {
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
                },
                responsive: true,
                loadingRecords: "Cargando...",
                autoFill: true,
                data: data.solicitud_colaborador,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -2 },
                    { responsivePriority: 3, targets: -1 },
                ],
                drawCallback: function (settings) {
                    $("ul.pagination").addClass("pagination-sm");
                },
                createdRow: function (row, data, dataIndex) {
                    $(row)
                        .find("#acciones1")
                        .on("click", function () {
                            self.archivosList = data.archivos;
                        });
                    $(row)
                        .find("#acciones3")
                        .on("click", function () {
                            console.log(data.id);
                            self.updateStatus(data.id, 3, data.id_solicitud);
                        });
                    $(row)
                        .find("#acciones4")
                        .on("click", function () {
                            self.updateStatus(data.id, 2, data.id_solicitud);
                        });
                },
                columns: [
                    { data: "user_id", width: 90, className: "text-center" },
                    { data: "nombre_completo"},
                    {
                        data: "sap_maestro_causales_terminos.name",
                        className: "text-center",
                    },
                    { data: "fecha_desvinculacion", className: "text-center" },
                    { data: "redireccion", className: "text-center" },
                    { data: "centro_costo", className: "text-center" },
                    { data: "comentario", className: "text-center" },
                    {
                        data: null,
                        className: "text-center",
                        width: 100,
                        render: function (data, type, row) {
                            var desc = "";
                            var color = "";

                            if (row.status == 1) {
                                desc = "pendiente";
                                color = "info";
                            } else if (row.status == 2) {
                                desc = "cancelado";
                                color = "danger";
                            } else if (row.status == 3) {
                                desc = "aprobado";
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
                            const rolesAdmin = [79, 78];
                            var botones = "";
                            var botonChecklist = "";
                            if (rolesAdmin.includes(rol) && row.status == 1) {
                                botones =
                                    '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones3" ><i class="fas fa-check text-success"></i> Aprobar</a></li>' +
                                    '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones4" ><i class="fas fa-times text-danger"></i> Desaprobar</a></li>';
                            }
                        
                            return (
                                '<div class="btn-group">' +
                                '<button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                '<i class="fa fa-cogs"></i>' +
                                "</button>" +
                                '<ul class="dropdown-menu">' +
                                '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones1"  data-bs-toggle="modal" data-bs-target="#modalArchivos"><i class="fas fa-file-alt text-info"></i> Archivos</a></li>' +
                                botonChecklist +
                                botones +
                                "</ul>" +
                                "</div>"
                            );
                        },
                    },
                ],
            });
        },
        ChangeView(data) {
            var togglerSolicitud = document.getElementById(
                "parte-solicitudes-vista"
            );
            var togglerDetalle = document.getElementById(
                "parte-solicitudes-detalle"
            );
            togglerSolicitud.classList.toggle("hidden");
            togglerDetalle.classList.toggle("hidden");
            this.createTableDetalle(data);
        },
        changeViewDetailChecklist() {
            var togglerDetalle = document.getElementById(
                "parte-solicitudes-detalle"
            );
            var togglerchecklist = document.getElementById(
                "parte-solicitudes-checklist"
            );
            togglerchecklist.classList.toggle("hidden");
            togglerDetalle.classList.toggle("hidden");
        },
        changeViewDetail() {
            var togglerSolicitud = document.getElementById(
                "parte-solicitudes-vista"
            );
            var togglerDetalle = document.getElementById(
                "parte-solicitudes-detalle"
            );
            togglerSolicitud.classList.toggle("hidden");
            togglerDetalle.classList.toggle("hidden");
            if (this.tableDetalle) {
                this.tableDetalle.destroy();
            }
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
            this.mensaje = "";
            this.isLoadingForm = false;
            this.reloadTable();
            this.changeViewDetail();
        },
        reloadTable() {
            this.table.ajax.reload();
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
