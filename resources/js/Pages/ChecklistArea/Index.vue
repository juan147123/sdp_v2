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
            />
        </div>

        <!-- Modal -->
        <div
            class="modal fade"
            id="modalCheckListArea"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modalTitleId"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>CHECKLIST</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <form class="p-1">
                            <div class="form-check mb-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    v-model="form.aplica"
                                />
                                <label class="form-check-label" for="">
                                    ¿Aplica formulario?</label
                                >
                            </div>
                            <div v-for="check in this.checkusuariolist">
                                <div
                                    class="d-flex justify-content-between mb-2"
                                >
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            :id="'checkbox' + check.id"
                                        />
                                        <label
                                            class="form-check-label ml-2"
                                            for=""
                                        >
                                            {{ check.descripcion }}</label
                                        >
                                    </div>
                                </div>
                                <input
                                    v-if="check.input == 1"
                                    type="text"
                                    required
                                    class="form-control form-control-sm mb-3 ml-4 w-50"
                                    :id="'input' + check.id"
                                    :placeholder="
                                        'Ingrese datos de ' + check.descripcion
                                    "
                                />
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label"
                                    >Comentarios</label
                                >
                                <textarea
                                    type="text"
                                    class="form-control"
                                    v-model="form.comentarios"
                                ></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-sm btn-danger"
                            data-bs-dismiss="modal"
                        >
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-sm btn-primary">
                            {{ form.status == 1 ? "Editar" : "Guardar" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import { rutaBase } from "../../../Utils/utils.js";
import Preloader from "@/Components/Preloader.vue";
import SolicitudesColaborador from "./SolicitudColaborador/Index.vue";
import dayjs from "dayjs";
import { setSwal } from "../../../Utils/swal";

export default {
    components: {
        AppLayout,
        breadcrumbs,
        Preloader,
        SolicitudesColaborador,
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
            checkusuariolist: [],
            form: this.$inertia.form({
                id: 0,
                aplica: null,
                area_id: 0,
                checklist: [],
                comentarios: "",
                status: null,
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
                        .find("#acciones2")
                        .on("click", function () {
                            self.getChecklist(data);
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
                                    '<button class="btn btn-outline-primary" style="cursor:pointer;font-size:11.5px;" id="acciones2" data-bs-toggle="modal" data-bs-target="#modalCheckListArea" ><i class="fas fa-list"></i></button>';
                            }

                            return botonChecklist;
                        },
                    },
                ],
            });
        },
        async getChecklist(check) {
            var checkUsuario = check.check_area_colaboradores[0];
            this.mensaje = "espere mientras se cargan los checklist....";
            this.isLoadingForm = true;
            this.checkusuariolist = [];
            self = this;
            await axios
                .get(rutaBase + "/configuraciones/area")
                .then(async (response) => {
                    this.checkusuariolist = response.data;
                    this.buildDtoCheck(checkUsuario);
                    self.mensaje = "";
                    self.isLoadingForm = false;
                });
        },
        buildDtoCheck(checkusuario) {
            this.form.reset();
            if (checkusuario != undefined) {
                this.form.id = checkusuario.id;
                this.form.aplica = checkusuario.aplica;
                this.form.area_id = checkusuario.area_id;
                this.form.checklist = checkusuario.checklist;
                this.form.comentarios = checkusuario.comentarios;
                this.form.status = checkusuario.status;
                this.setDataJson(checkusuario.checklist);
            }
        },
        setDataJson(json) {
            var jsonArray = JSON.parse(json);
            setTimeout(() => {
                jsonArray.forEach((dataObject) => {
                    for (const key in dataObject) {
                        const value = dataObject[key];
                        if (key.startsWith("checkbox")) {
                            $(`#${key}`).prop("checked", value);
                        } else {
                            $(`#${key}`).val(value);
                        }
                    }
                });
            }, 100);
        },
    },
};
</script>
