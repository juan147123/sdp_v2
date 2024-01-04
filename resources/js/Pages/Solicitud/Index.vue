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
                @ChangeView="this.ChangeView(0)"
                :solicitudesColaborador="solicitudesColaborador"
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
            part: 0,
            solicitudesColaborador: [],
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
                            self.ChangeView(1);
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

                            return `<div><span class="badge bg-${status[0]} text-white">${status[1]}</span></div>`;
                        },
                    },
                ],
            });
        },
        ChangeView(part) {
            var togglerSolicitud = document.getElementById(
                "parte-solicitudes-vista"
            );
            var togglerDetalle = document.getElementById(
                "parte-solicitudes-detalle"
            );
            togglerSolicitud.classList.toggle("hidden");
            togglerDetalle.classList.toggle("hidden");
        },
    },
};
</script>
