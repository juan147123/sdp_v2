<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <breadcrumbs :modules="breadcrumbs" />
        <div class="box m-1 mt-5">
                <div class="container-fluid">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table
                                class="table text-nowrap table-bordered dt-responsive"
                                id="tableColaboradoresPe"
                            >
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Dni</th>
                                        <th>Nombres y Apellidos de solicitud</th>
                                        <th>Empresa</th>
                                        <th>Centro de costo</th>
                                        <th>Área</th>
                                        <th>Solicitud</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import Preloader from "@/Components/Preloader.vue";
import { setSwal } from "../../../Utils/swal";
import { rutaBase } from "../../../Utils/utils.js";

export default {
    components: {
        AppLayout,
        breadcrumbs,
        Preloader,
    },
    data() {
        var self = this;
        return {
            breadcrumbs: [
                {
                    label: "Colaboradores",
                    url: "redirectpage/colaboradores/pe",
                    icon: "fa fa-users",
                },
            ],
            mensaje: "",
            isLoadingForm: false,
            table:[]
        };
    },
    mounted(){
        this.createTable();
    },
    methods:{
        createTable(){
            var self = this;
            this.table = new DataTable("#tableColaboradoresPe", {
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
                },
                ajax: {
                    url: rutaBase + "/colaboradores/pe",
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
                    {
                        data: null,
                        className: "text-center",

                        render: function (data, type, row) {
                            return type === "display"
                                ? `<input type="checkbox" class="checkbox-datatable">`
                                : "";
                        },
                    },
                    {
                        data: "dni",
                        className: "text-center",
                    },
                    {
                        data: null,
                        width: 400,
                        render: function (data, type, row) {
                            return row.nombres + " " + row.apellido;
                        },
                    },
                    { data: "empresa", className: "text-center" },
                    {
                        data: null,
                        width: 300,
                        render: function (data, type, row) {
                            return (
                                row.centro_costo +
                                " - " +
                                row.des_centro_gestion
                            );
                        },
                    },
                    {
                        data: "area",
                        width: 300,
                        render: function (data, type, row) {
                            return data;
                        },
                    },
                    {
                        data: "solicitudes",
                        className: "text-center",
                        render: function (data, type, row) {
                            if (data == 0) {
                                return `<span class="badge bg-success text-white">No</span>`;
                            } else {
                                return `<span class="badge bg-danger text-white">Si</span>`;
                            }
                        },
                    },
                ],
            });
        }
    }
};
</script>
