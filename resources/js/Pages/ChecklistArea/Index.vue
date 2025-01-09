<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <div v-if="this.details != true">
            <breadcrumbs :modules="breadcrumbs" />
            <!-- <div class="col-md-12 mt-2">
                <div class="row">
                    <div class="col-lg-3 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-color-custom-pendiente elevation-1"><i
                                    class="fas fa-bookmark"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text color-custom-pendiente">PENDIENTES</span>
                                <span class="info-box-number">{{
                                    conteoSolicitudes.PENDIENTE
                                    }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-color-custom-aprobado elevation-1"><i
                                    class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text color-custom-aprobado">APROBADOS</span>
                                <span class="info-box-number">{{
                                    conteoSolicitudes.APROBADO
                                    }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-color-custom-rechazado elevation-1"><i
                                    class="fas fa-times-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text color-custom-rechazado">RECHAZADOS</span>
                                <span class="info-box-number">{{
                                    conteoSolicitudes.RECHAZADO
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="box m-1 mt-5 bg-white p-3 border-round">
                <div class="container-fluid">
                    <div class="box-body">
                        <DataTable dataKey="id" :value="dataTable.data" :rows="dataTable.rows" showGridlines paginator
                            :paginatorTemplate="dataTable.paginatorTemplate" :currentPageReportTemplate="dataTable.currentPageReportTemplate
                                " :rowsPerPageOptions="dataTable.rowsPerPageOptions" sortMode="single"
                            :globalFilterFields="dataTable.globalFilterFields" v-model:filters="dataTable.filters"
                            filterDisplay="menu" FilterMatchMode>

                            <template #empty>
                                <div class="w-full flex justify-content-center">
                                    <span>No hay datos que mostrar</span>
                                </div>
                            </template>
                            <Column field="codigo" header="Código de solicitud"
                                headerStyle="background-color:black; color:white" sortable filterField="codigo"
                                :showFilterMatchModes="false">
                                <template #filter="{ filterModel }">
                                    <MultiSelect v-model="filterModel.value" :options="filtersDropdownData.codigo"
                                        placeholder="Cualquiera" class="p-column-filter" optionLabel="codigo"
                                        optionValue="codigo">
                                    </MultiSelect>
                                </template>
                            </Column>
                            <Column field="user_created" header="Solicitante"
                                headerStyle="background-color:black; color:white" sortable filterField="user_id"
                                :showFilterMatchModes="false">
                            </Column>
                            <Column field="centro_costo" header="Centro de costo"
                                headerStyle="background-color:black; color:white" sortable filterField="centro_costo"
                                :showFilterMatchModes="false">
                                <template #filter="{ filterModel }">
                                    <MultiSelect v-model="filterModel.value" :options="filtersDropdownData.centro_costo
                                        " placeholder="Cualquiera" class="p-column-filter" optionLabel="centro_costo"
                                        optionValue="centro_costo">
                                    </MultiSelect>
                                </template>
                            </Column>

                            <Column field="created_at" header="Fecha de creación"
                                headerStyle="background-color:black; color:white" sortable filterField="created_at"
                                :showFilterMatchModes="false">
                                <template #body="{ data }">
                                    <div>
                                        {{
                                            dateFormatChangeApi(data.created_at)
                                        }}
                                    </div>
                                </template>
                            </Column>

                            <Column field="obra" header="Origen" headerStyle="background-color:black; color:white"
                                sortable filterField="obra" :showFilterMatchModes="false">
                                <template #body="{ data }">
                                    {{ data.obra == 0 ? "PLANTA" : "OBRA" }}
                                </template>
                            </Column>

                            <Column :field="null" filterField="user_id" header="Colaboradores"
                                headerStyle="background-color:black; color:white" sortable style="text-align: center"
                                :showFilterMatchModes="false">
                                <template #body="{ data }">
                                    <Button icon="pi pi-users" class="ml-2" style="
                                            font-size: 0.9rem;
                                            height: 30px;
                                            width: 2rem !important;
                                            height: 2rem !important;
                                        " severity="info" @click="ChangeView(data)" v-tooltip.top="'colaboradores'" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
         <SolicitudesColaborador @ChangeView="this.ChangeView" @getData="this.getData"
            :solicitud_selected="solicitud_selected" :details="this.details" />
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import { rutaBase, dateFormatChange } from "../../../Utils/utils.js";
import SolicitudesColaborador from "./SolicitudColaborador/Index.vue";
import { setSwal } from "../../../Utils/swal";
import Preloader from "@/Components/Preloader.vue";
import setLocaleES from "../../primevue.config.js";
import dayjs from "dayjs";
import { FilterMatchMode } from "primevue/api";
import PrimeVueComponents from "../../../js/primevue.js";

export default {
    components: {
        AppLayout,
        breadcrumbs,
        SolicitudesColaborador,
        Preloader,
        ...PrimeVueComponents,
    },
    setup() {
        setLocaleES();
    },
    data() {
        return {
            breadcrumbs: [
                {
                    label: "Solicitudes",
                    url: "/redirectpage/solicitud/rrhh",
                    icon: "fa fa-book",
                },
            ],
            mensaje: "",
            isLoadingForm: false,
            table: [],
            tableDetalle: [],
            conteoSolicitudes: {
                CREADO: 0,
                PEDIENTE: 0,
                APROBADO: 0,
                RECHAZADO: 0,
            },
            part: 0,
            solicitud_selected: [],
            form: this.$inertia.form({
                id: 0,
                status: 0,
                id_solicitud: 0,
                comentario: "",
            }),
            dataTable: {
                rows: 10,
                data: [],
                rowsPerPageOptions: [10, 20, 50, 100],
                paginatorTemplate:
                    "RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink",
                currentPageReportTemplate:
                    "Página {currentPage} de {totalPages}",
                filters: {
                    global: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    codigo: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    centro_costo: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    created_at: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    estado: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                },
                globalFilterFields: [
                    "user_id",
                    "nombre_completo",
                    "sap_maestro_causales_terminos",
                    "fecha_desvinculacion",
                    "centro_costo",
                    "comentario",
                ],
            },
            filtersDropdownData: {
                user_id: [],
                nombre_completo: [],
                sap_maestro_causales_terminos: [],
                centro_costo: [],
                comentario: [],
            },
            details: false,
            visible: false,
            archivosList: [],
        };
    },
    async mounted() {
        await this.getData();
    },
    methods: {


        /* NUEVO CODIGO */
        async getData() {
            self = this;

            this.mensaje = "Cargando datos espere ...";
            this.isLoadingForm = true;
            await axios
                .get(rutaBase + "/list/solicitudes/area")
                .then(async (response) => {
                    console.log(response)
                    this.dataTable.data = response.data;
                    if (this.details == true) {
                        let oldId = self.solicitud_selected.id;
                        let newselected = self.dataTable.data.find(
                            (item) => item.id === oldId
                        );
                        this.solicitud_selected = newselected;
                        if (newselected) {
                            // Si el elemento existe, asignarlo a solicitud_selected
                            this.solicitud_selected = newselected;
                        } else {
                            // Si el elemento no existe, ejecutar la función changevire
                            this.ChangeView();
                        }
                    }

                    this.mensaje = "";
                    this.isLoadingForm = false;
                });
        },

        dateFormatChangeApi(data) {
            return dateFormatChange(data);
        },

        ChangeView(data) {
            this.details = !this.details;
            this.solicitud_selected = data ? data : [];
        },


    },
};
</script>
