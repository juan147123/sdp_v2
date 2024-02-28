<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <div id="parte-solicitudes-vista">
            <breadcrumbs :modules="breadcrumbs" />
            <div class="box m-1 mt-5">
                <div class="container-fluid">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table
                                class="table text-nowrap table-bordered dt-responsive"
                                id="tableSolicitudesArea"
                            >
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
            <SolicitudesColaborador
                @changeViewDetail="this.changeViewDetail"
                @reloadTable="reloadTable"
                :solicitudesColaborador="solicitudesColaborador"
                :archivosList="archivosList"
            />
        </div>
       <!--  <div class="hidden" id="parte-solicitudes-checklist">
            <Checklist
                :checksUsuario="this.checkusuariolist"
                @changeViewDetailChecklist="changeViewDetailChecklist"
            />
        </div> -->
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import { rutaBase } from "../../../Utils/utils.js";
import { setSwal } from "../../../Utils/swal";
import Preloader from "@/Components/Preloader.vue";
import dayjs from "dayjs";
import SolicitudesColaborador from "./SolicitudColaborador/Index.vue";

export default {
    components: {
        AppLayout,
        breadcrumbs,
        Preloader,
        SolicitudesColaborador
    },
    data() {
        var self = this;
        return {
            breadcrumbs: [
                {
                    label: "Solicitudes",
                    url: "/solicitudes/area",
                    icon: "fa fa-book",
                },
            ],
            mensaje: "",
            isLoadingForm: false,
            table: [],
            tableDetalle: [],
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
        };
    },
    mounted() {
        this.createTable();
    },
    methods: {
        createTable() {
            var self = this;
            this.table = new DataTable("#tableSolicitudesArea", {
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
                },
                ajax: {
                    url: rutaBase + "/list/solicitudes/area",
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
                        data: "status",
                        className: "text-center",
                        render: function (data, type, row) {
                            const counts = row.solicitud_colaborador.reduce(
                                (acc, solicitud) => {
                                    acc[solicitud.status] =
                                        (acc[solicitud.status] || 0) + 1;
                                    return acc;
                                },
                                {}
                            );

                            const totalCantidad =
                                row.solicitud_colaborador.length;
                            const status =
                                counts[2] === totalCantidad
                                    ? ["danger", "cancelado"]
                                    : counts[1] >= 1
                                    ? [
                                          "info",
                                          `colaboradores pendientes: ${counts[1]}`,
                                      ]
                                    : ["success", "completo"];

                            return `<div><span style="font-size:11.5px;" class="badge bg-${status[0]} text-white">${status[1]}</span></div>`;
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
            });
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
        reloadTable() {
            this.table.ajax.reload();
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

        //DETALLE
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
                        .find("#acciones2")
                        .on("click", function () {
                            self.getChecklist(data);
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
                            var obra = self.solicitudesColaborador.obra;
                            var botonChecklist = "";
                            if (obra != 1) {
                                botonChecklist =
                                    '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones2" ><i class="fas fa-tasks text-primary"></i> Checklist</a></li>';
                            }

                            return (
                                '<div class="btn-group">' +
                                '<button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                '<i class="fa fa-cogs"></i>' +
                                "</button>" +
                                '<ul class="dropdown-menu">' +
                                botonChecklist +
                                "</ul>" +
                                "</div>"
                            );
                        },
                    },
                ],
            });
        },
    },
};
</script>
