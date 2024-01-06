<template>
    <AppLayout>
        <div id="parte-solicitudes-vista">
            <breadcrumbs :modules="breadcrumbs" />
            <div class="box m-1 mt-5">
                <table
                    class="table table-hover align-middle"
                    id="tableSolicitudes"
                >
                    <thead class="table-dark">
                        <tr>
                            <th>Detalle</th>
                            <th>Código</th>
                            <th>Solicitante</th>
                            <th>Fecha de solicitud</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="hidden" id="parte-solicitudes-detalle">
            <SolicitudesColaborador
                @changeViewDetail="this.changeViewDetail"
                :solicitudesColaborador="solicitudesColaborador"
                :archivosList="archivosList"
            />
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import { rutaBase } from "../../../Utils/utils.js";
import SolicitudesColaborador from "./SolicitudColaborador/Index.vue";

import dayjs from "dayjs";
export default {
    components: {
        AppLayout,
        breadcrumbs,
        SolicitudesColaborador,
    },
    data() {
        var self = this;
        return {
            breadcrumbs: [
                {
                    label: "Solicitudes",
                    url: "/redirectPage/solicitud",
                    icon: "fa fa-book",
                },
            ],
            table: [],
            tableDetalle: [],
            part: 0,
            solicitudesColaborador: [],
            archivosList: [],
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
                    { responsivePriority: 2, targets: -1 },
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
                    {
                        data: "codigo",
                        width: 100,
                        className: "text-center",
                        render: function (data, type, row) {
                            return `<button style="font-size: 12px;" class="btn btn-outline-primary btn-sm btnColaboradoresSolicitud"><i class="fas fa-users"></i></button>`;
                        },
                    },
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
                    { responsivePriority: 2, targets: -1 },
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
                            const rolesAdmin = [79, 78];
                            $botones = "";
                            if (rolesAdmin.includes(rol)) {
                                $botones =
                                    '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones2" ><i class="fas fa-tasks text-primary"></i> Checklist</a></li>' +
                                    '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones3" ><i class="fas fa-check text-success"></i> Aprobar</a></li>' +
                                    '<li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones4" ><i class="fas fa-times text-danger"></i> Desaprobar</a></li>';
                            }

                            return `<div class="btn-group">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-cogs"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" style="cursor:pointer;font-size:11.5px;" id="acciones1"  data-bs-toggle="modal" data-bs-target="#modalArchivos"><i class="fas fa-file-alt text-info"></i> Archivos</a></li>
                                            
                                        </ul>
                                    </div>`;
                        },
                    },
                ],
            });
        },
    },
};
</script>
