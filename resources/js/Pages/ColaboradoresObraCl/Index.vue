<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <breadcrumbs :modules="breadcrumbs" />
        <div class="accordion mt-3" id="accordionFilter">
            <div class="accordion-item">
                <h5 class="accordion-header" id="headingFilter">
                    <button
                        class="accordion-button button-sm"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseFilter"
                        aria-expanded="true"
                        aria-controls="collapseFilter"
                    >
                        FILTROS
                    </button>
                </h5>
                <div
                    id="collapseFilter"
                    class="accordion-collapse collapse show"
                    aria-labelledby="headingFilter"
                    data-bs-parent="#accordionFilter"
                >
                    <div class="accordion-body">
                        <div class="mt-2 text-end">
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                title="Limpiar los selectores"
                                @click="onClickClean"
                            >
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-3 d-flex flex-column">
                                <label
                                    for="empresa_pe"
                                    class="form-label label-filter"
                                    >Empresa:</label
                                >
                                <select
                                    class="form-select column_filter select-filtros"
                                    name="empresa_pe"
                                    id="empresa_pe"
                                    index="4"
                                    v-model="this.empresa"
                                >
                                    <option value="">Seleccione</option>
                                    <option
                                        v-for="empresa in this.empresas"
                                        :value="empresa.id"
                                    >
                                        {{ empresa.descripcion }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label
                                    for="unidad_pe"
                                    class="form-label label-filter"
                                    >Unidad:</label
                                >
                                <select
                                    class="form-select column_filter select-filtros"
                                    name="unidad_pe"
                                    id="unidad_pe"
                                    index="5"
                                    v-model="this.unidad"
                                >
                                    <option value="" :codEmpresa="''">
                                        Seleccione
                                    </option>
                                    <option
                                        v-for="unidad in this.unidades"
                                        :value="unidad.codigo_unidad"
                                        :codEmpresa="unidad.rut"
                                    >
                                        {{ unidad.codigo_unidad }}
                                        -
                                        {{ unidad.descripcion }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label
                                    for="centroCosto_pe"
                                    class="form-label label-filter"
                                    >Centro de costo:</label
                                >
                                <select
                                    class="form-select column_filter select-filtros"
                                    name="centroCosto_pe"
                                    id="centroCosto_pe"
                                    index="6"
                                    v-model="this.centrocosto"
                                >
                                    <option
                                        value=""
                                        :area="''"
                                        :unidad="''"
                                        :codEmpresa="''"
                                    >
                                        Seleccione
                                    </option>
                                    <option
                                        v-for="centroCosto in this.centrosCosto"
                                        :value="centroCosto.centro_costo"
                                        :unidad="centroCosto.codigo_unidad"
                                        :codEmpresa="centroCosto.rut"
                                    >
                                        {{ centroCosto.centro_costo }}
                                        -
                                        {{ centroCosto.descripcion }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label
                                    for="area"
                                    class="form-label label-filter"
                                    >Departamento:</label
                                >

                                <select
                                    class="form-select column_filter select-filtros"
                                    name="area_pe"
                                    id="area_pe"
                                    index="7"
                                    v-model="this.area"
                                >
                                    <option
                                        value=""
                                        :unidad="''"
                                        :codEmpresa="''"
                                    >
                                        Seleccione
                                    </option>
                                    <option
                                        v-for="area in this.areas"
                                        :value="area.descripcion"
                                        :unidad="area.codigo_unidad"
                                        :centro_costo="area.centro_costo"
                                        :codEmpresa="area.rut"
                                    >
                                        {{ area.departamento }}
                                        -
                                        {{ area.descripcion }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex flex-column">
                                <label
                                    for="area"
                                    class="form-label label-filter"
                                    >Planta:</label
                                >

                                <select
                                    class="form-select column_filter select-filtros"
                                    name="p_np"
                                    id="p_np"
                                    index="8"
                                    v-model="this.p_np"
                                >
                                    <option value="">Seleccione</option>
                                    <option value="PL">PLANTA</option>
                                    <option value="NP">NO PLANTA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box m-1 mt-5">
            <div class="container-fluid">
                <div class="box-body">
                    <div class="mt-2 mb-3 text-end">
                        <Button 
                            label="Nuevo" icon="pi pi-plus"
                            class="h-2rem"
                            style="font-size: 0.9rem;"
                            severity="info"
                            @click="showModal"
                            :disabled="this.colaboradoresDetalle.length == 0"
                        />
                    </div>
                    <div class="table-responsive">
                        <table
                            class="table text-nowrap table-bordered dt-responsive"
                            id="tableColaboradoresCl"
                        >
                            <thead class="table-dark">
                                <tr>
                                    <th id="button-plus"></th>
                                    <th>#</th>
                                    <th>NP Usuario</th>
                                    <th>Nombres y Apellidos de solicitud</th>
                                    <th>Empresa</th>
                                    <th>Unidad</th>
                                    <th>Centro de costo</th>
                                    <th>Departamento</th>
                                    <th>Planta</th>
                                    <th>Solicitud</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <Formulario
            :terminos="this.terminos"
            :colaboradoresDetalle="colaboradoresDetalle"
            @reloadTable="reloadTable"
        />
        <FormularioMultiple
            :terminos="this.terminos"
            :visible="this.visible"
            :colaboradoresDetalle="colaboradoresDetalle"
            @reloadTable="reloadTable"
            @showModal="showModal"
            @onClickCleanDetalleColaborador="onClickCleanDetalleColaborador"
        />
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import Preloader from "@/Components/Preloader.vue";
import { setSwal } from "../../../Utils/swal";
import { rutaBase } from "../../../Utils/utils.js";
import Formulario from "./Formularios/Formulario.vue";
import FormularioMultiple from "./Formularios/FormularioMultiple.vue";
import * as mensajes from "../../../Utils/message.js";
import PrimeVueComponents from "../../../js/primevue.js";
export default {
    components: {
        AppLayout,
        breadcrumbs,
        Preloader,
        Formulario,
        FormularioMultiple,
           ...PrimeVueComponents,
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
            visible: false,
            table: [],
            empresas: [],
            unidades: [],
            areas: [],
            centrosCosto: [],
            empresa: "",
            unidad: "",
            departamento: "",
            centrocosto: "",
            area: "",
            p_np: "",
            colaboradoresDetalle: [],
            terminos: [],
        };
    },
    mounted() {
        this.createTable();
        $(".select-filtros").select2();
        this.filterTable();
    },
    methods: {
        createTable() {
            var self = this;
            this.table = new DataTable("#tableColaboradoresCl", {
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
                },
                ajax: {
                    url: rutaBase + "/colaboradores/obra/cl",
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

                    var tableData = self.table.rows().data();

                    tableData.each(function (colaborador) {
                        var empresa = {
                            rut: colaborador.rut,
                            descripcion: colaborador.razon_social.toUpperCase(),
                        };
                        self.addToEmpresaArray(empresa);

                        var unidad = {
                            rut: colaborador.rut,
                            codigo_unidad: colaborador.id_unidad_negocio,
                            descripcion:
                                colaborador.unidad_negocio.toUpperCase(),
                        };
                        self.addToUnidadArray(unidad);

                        var departamento = {
                            rut: colaborador.rut,
                            codigo_unidad: colaborador.id_unidad_negocio,
                            centro_costo: colaborador.centro_costo,
                            descripcion:
                                colaborador.nombre_departamento.toUpperCase(),
                        };
                        self.addToAreaArray(departamento);

                        var cege = {
                            rut: colaborador.empresa,
                            codigo_unidad: colaborador.id_unidad_negocio,
                            centro_costo: colaborador.centro_costo,
                            departamento: colaborador.departamento,
                            descripcion:
                                colaborador.nombre_centro_costo.toUpperCase(),
                        };
                        self.addToCentroGestionArray(cege);
                    });
                },
                createdRow: function (row, data, dataIndex) {
                    $(row)
                        .find(".btnColaboradoresSolicitud")
                        .on("click", function () {
                            self.solicitudesColaborador = data;
                            self.ChangeView(data);
                        });
                    $(row)
                        .find(".checkbox-datatable")
                        .on("change", function () {
                            const centroExists = self.colaboradoresDetalle.some(
                                (item) =>
                                    item.centro_costo !== data.centro_costo
                            );

                            if (this.checked) {
                                if (centroExists) {
                                    this.checked = false;
                                    self.setToast2();
                                } else if (
                                    self.colaboradoresDetalle.length < 15
                                ) {
                                    self.colaboradoresDetalle.push(data);
                                } else {
                                    this.checked = false;
                                    self.setToast();
                                }
                            } else {
                                const indexToRemove =
                                    self.colaboradoresDetalle.indexOf(data);
                                if (indexToRemove !== -1) {
                                    self.colaboradoresDetalle.splice(
                                        indexToRemove,
                                        1
                                    );
                                }
                            }
                        });
                },

                columns: [
                    {
                        data: null,
                        className: "text-center pr-0",

                        render: function (data, type, row) {
                            return "";
                        },
                    },
                    {
                        data: null,
                        className: "text-left m-0 pl-2 pr-3",

                        render: function (data, type, row) {
                            var solicitudes = row.solicitudes;
                            return type === "display"
                                ? `<input type="checkbox" class="checkbox-datatable" ${
                                      solicitudes === 0 ? "" : "disabled"
                                  }>`
                                : "";
                        },
                    },
                    {
                        data: "user_id",
                        className: "text-center",
                    },
                    {
                        data: null,
                        width: 400,
                        render: function (data, type, row) {
                            return row.first_name + " " + row.last_name;
                        },
                    },
                    { data: "razon_social", className: "text-center" },
                    {
                        data: "id_unidad_negocio",
                        width: 300,
                        render: function (data, type, row) {
                            return data + " - " + row.unidad_negocio;
                        },
                    },
                    {
                        data: null,
                        width: 300,
                        render: function (data, type, row) {
                            return (
                                row.centro_costo +
                                " - " +
                                row.nombre_centro_costo
                            );
                        },
                    },
                    {
                        data: "departamento",
                        width: 300,
                        render: function (data, type, row) {
                            return data + " - " + row.nombre_departamento;
                        },
                    },
                    {
                        data: "planta_noplanta",
                        className: "text-center",
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
        },

        addToEmpresaArray(empresa) {
            // Verificar si la empresa ya existe en el array
            const empresaExistente = this.empresas.find(
                (e) => e.rut === empresa.rut
            );

            if (!empresaExistente) {
                this.empresas.push(empresa);
            }
        },
        setToast() {
            this.$toast.add({
                severity: "warn",
                position: "top-right",
                summary: "Importante",
                detail: mensajes.MENSAJE_MAX,
                life: 3000,
            });
        },
        setToast2() {
            this.$toast.add({
                severity: "warn",
                position: "top-right",
                summary: "Importante",
                detail: "Tienen que registrar el mismo centro de costo",
                life: 3000,
            });
        },
        addToUnidadArray(unidad) {
            // Verificar si la unidad ya existe en el array
            const unidadExistente = this.unidades.find(
                (e) =>
                    e.rut === unidad.rut &&
                    e.codigo_unidad === unidad.codigo_unidad
            );

            if (!unidadExistente) {
                this.unidades.push(unidad);
            }
        },

        addToCentroGestionArray(cege) {
            // Verificar si el cege ya existe en el array
            const unidadExistente = this.centrosCosto.find(
                (e) =>
                    e.rut === cege.rut &&
                    e.codigo_unidad === cege.codigo_unidad &&
                    e.centro_costo === cege.centro_costo
            );

            if (!unidadExistente) {
                this.centrosCosto.push(cege);
            }
        },
        addToAreaArray(area) {
            // Verificar si el area ya existe en el array
            const unidadExistente = this.areas.find(
                (e) =>
                    e.rut === area.rut &&
                    e.codigo_unidad === area.codigo_unidad &&
                    e.centro_costo === area.centro_costo &&
                    e.descripcion === area.descripcion
            );

            if (!unidadExistente) {
                this.areas.push(area);
            }
        },
        filterTable() {
            self = this;
            $(".column_filter").on("change", function () {
                var index = $(this).attr("index");
                var value = $(this).val();
                self.filterColumn(index, value);
            });
        },
        filterColumn(i, value) {
            self = this;
            self.table.column(i).search(value).draw();
        },
        onClickCleanDetalleColaborador() {
            this.colaboradoresDetalle = [];
            this.table
                .column(1)
                .nodes()
                .to$()
                .find(".checkbox-datatable")
                .prop("checked", false);
        },
        onClickClean() {
            $("#empresa_pe").val("").trigger("change");
            $("#unidad_pe").val("").trigger("change");
            $("#centroCosto_pe").val("").trigger("change");
            $("#area_pe").val("").trigger("change");
            $("#p_np").val("").trigger("change");
            this.onClickCleanDetalleColaborador();
        },
        async getMotivos() {
            this.terminos = [];
            this.mensaje = "Espere mientras cargamos el formulario";
            this.isLoadingForm = true;
            await axios
                .get(rutaBase + "/terminos/list")
                .then(async (response) => {
                    this.mensaje = "";
                    this.isLoadingForm = false;
                    this.terminos = response.data;
                    $(".form-select-modal-multiple").select2({
                        dropdownParent: $("#modalSolicitudMultiple"),
                        tags: true,
                    });
                });
        },
        reloadTable() {
            this.table.ajax.reload();
        },
        showModal() {
            this.visible = !this.visible;
            if (this.visible == true) {
                this.getMotivos();
            }
        },
    },
};
</script>
