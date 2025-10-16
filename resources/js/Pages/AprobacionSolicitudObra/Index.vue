  <template> 
   <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <div v-if="this.details != true">
            <breadcrumbs :modules="breadcrumbs" />
            <div class="col-md-12 mt-2">
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
            </div>
            <div class="box m-1 mt-5 bg-white p-3 border-round">
                <div class="container-fluid">
                    <div class="box-body">
                        <DataTable dataKey="id" :value="dataTable.data" :rows="dataTable.rows" showGridlines paginator
                            :paginatorTemplate="dataTable.paginatorTemplate" :currentPageReportTemplate="dataTable.currentPageReportTemplate
                                " :rowsPerPageOptions="dataTable.rowsPerPageOptions" sortMode="single"
                            :globalFilterFields="dataTable.globalFilterFields" v-model:filters="dataTable.filters"
                            filterDisplay="menu" FilterMatchMode>
                            <template #header>
                                <div class="flex justify-content-end align-items-center">
                                    <InputText placeholder="Buscador general" v-model="dataTable.filters['global'].value
                                        " style="font-size: 0.9rem; height: 30px" />
                                </div>
                            </template>
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
                            <Column field="estado.descripcion" filterField="estado" header="Estado"
                                headerStyle="background-color:black; color:white" style="text-align: center" sortable
                                :showFilterMatchModes="false">
                                <template #body="{ data }">
                                    <Tag :value="data.estado.id == 2 ||
                                        data.estado.id == 3
                                        ? 'APROBADO'
                                        : data.estado.descripcion
                                        " :severity="data.estado.id == 2 ||
                                            data.estado.id == 3
                                            ? 'success'
                                            : data.estado.color
                                            " />
                                </template>
                                <template #filter="{ filterModel }">
                                    <MultiSelect v-model="filterModel.value" :options="filtersDropdownData.estado"
                                        placeholder="Cualquiera" class="p-column-filter"
                                        optionLabel="estado.descripcion" optionValue="estado">
                                    </MultiSelect>
                                </template>
                            </Column>
                            <Column
                            header="Siguiente Aprobador"
                            headerStyle="background-color:black; color:white"
                            style="text-align:center"
                            :bodyClass="'wrap-cell max-w-email'"
                            >
                            <template #body="{ data }">
                                <span v-if="getNextApproverEmail(data)">{{ getNextApproverEmail(data) }}</span>
                                <Tag v-else value="Sin ruta" severity="warning" />
                            </template>
                            </Column>
                            <Column :field="null" filterField="user_id" header="Colaboradores"
                                headerStyle="background-color:black; color:white" sortable style="text-align: center"
                                :showFilterMatchModes="false">
                                <template #body="{ data }">
                                    <Button icon="pi pi-users" class="ml-2" style="font-size: 0.9rem; height: 30px;     width: 2rem !important;
                                            height: 2rem !important;" severity="info" @click="ChangeView(data)"
                                        v-tooltip.top="'colaboradores'" />
                                    <Button icon="pi pi-folder" class="ml-2" style="
                                            font-size: 0.9rem;
                                            height: 30px;
                                            width: 2rem !important;
                                            height: 2rem !important;
                                        " severity="info" @click="setImagenes(data.archivos)"
                                        v-tooltip.top="'Variables'" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <SolicitudesColaborador @ChangeView="this.ChangeView" @getData="this.getData"
            :solicitud_selected="solicitud_selected" :details="this.details" />
            <Modal :archivosList="this.archivosList" :visible="this.visible" @setImagenes="this.setImagenes" />
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
import Modal from "./SolicitudColaborador/Components/Modal.vue";

export default {
    components: {
        AppLayout,
        breadcrumbs,
        SolicitudesColaborador,
        Preloader,
        Modal,
        ...PrimeVueComponents,
    },
    setup() {
        setLocaleES();
        return {};
    },
    data() {
        return {
            breadcrumbs: [
                {
                    label: "Solicitudes",
                    url: "/redirectpage/solicitud/aprobar",
                    icon: "fa fa-book",
                },
            ],
            conteoSolicitudes: {
                CREADO: 0,
                PEDIENTE: 0,
                APROBADO: 0,
                RECHAZADO: 0,
            },
            mensaje: "",
            isLoadingForm: false,
            table: [],
            tableDetalle: [],
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
            archivosList:[],
        };
    },
    async mounted() {
        await this.getData();
        this.initializeDropdownsData();
    },
    methods: {
        setImagenes(data) {
            this.archivosList = data ;
            this.visible = !this.visible;
        },
        async updateStatus(id, status, id_solicitud) {
            this.form.id = id;
            this.form.status = status;
            this.form.id_solicitud = id_solicitud;
            this.mensaje = "espere mientras se efectuan los cambios....";
            this.isLoadingForm = true;

            await new Promise((resolve) => {
                setSwal({
                    value: "updateStatusInput",
                    data: status,
                    callback: async (comentario) => {
                        resolve();
                        this.update(comentario);
                    },
                });
            });
        },

        update(comentario) {
            this.form.comentario = comentario;
            this.form.put(this.route("solicitud.colaborador.update.status"), {
                onFinish: () => {
                    this.onFinish();
                },
            });
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

        /* NUEVO CODIGO */
        async getData() {
            self = this;

            this.mensaje = "Cargando datos espere ...";
            this.isLoadingForm = true;
            await axios
                .get(rutaBase + "/list/solicitud/aprobar")
                .then(async (response) => {
                    if (response.status == 200) {
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
                    }

                    this.mensaje = "";
                    this.isLoadingForm = false;
                });
        },
        initializeDropdownsData() {
            this.filtersDropdownData.codigo = [
                ...new Set(
                    this.dataTable.data
                        .filter((s) => s.codigo != "")
                        .map((s) => s.codigo)
                ),
            ].map((o) => {
                return { codigo: o };
            });

            this.filtersDropdownData.centro_costo = [
                ...new Set(
                    this.dataTable.data
                        .filter((s) => s.centro_costo != "")
                        .map((s) => s.centro_costo)
                ),
            ].map((o) => {
                return { centro_costo: o };
            });

            this.filtersDropdownData.estado = [
                ...new Map(
                    this.dataTable.data
                        .filter(
                            (s) =>
                                s.estado != "" && s.estado.descripcion != null
                        )
                        .map((s) => [s.estado.descripcion, s.estado])
                ).values(),
            ].map((o) => {
                return { estado: o };
            });

            // Asegurar que todos los estados deseados estén presentes
            this.setDAtaDashboard();
        },
        setDAtaDashboard() {
            const estadoCount = this.dataTable.data.reduce((acc, s) => {
                const descripcion =
                    s.estado && s.estado.descripcion
                        ? s.estado.descripcion
                        : "desconocido";
                acc[descripcion] = (acc[descripcion] || 0) + 1;
                return acc;
            }, {});
            const estadosDeseados = [
                "CREADO",
                "PENDIENTE",
                "APROBADO",
                "RECHAZADO",
            ];
            this.conteoSolicitudes = {};

            estadosDeseados.forEach((estado) => {
                if (estado === "APROBADO") {
                    // Sumar todas las variantes de "APROBADO"
                    this.conteoSolicitudes["APROBADO"] =
                        (estadoCount["APROBADO"] || 0) +
                        (estadoCount["APROBADO ADMINISTRADOR"] || 0) +
                        (estadoCount["SOLICITUD APROBADA TOTAL"] || 0) +
                        (estadoCount["PENDIENTE APROBAR POR GERENTE DE PROYECTO"] || 0);
                } else if (estado === "RECHAZADO") {
                    // Sumar todas las variantes de "RECHAZADO"
                    this.conteoSolicitudes["RECHAZADO"] =
                        (estadoCount["SOLICITUD RECHAZADA"] || 0) +
                        (estadoCount["RECHAZADO RRHH"] || 0) +
                        (estadoCount["RECHAZADO ADMINISTRADOR"] || 0) +
                        (estadoCount["SOLICITUD RECHAZADA TOTAL"] || 0);
                } else if (estado === "PENDIENTE") {
                    // Sumar todas las variantes de "RECHAZADO"
                    this.conteoSolicitudes["PENDIENTE"] =
                        (estadoCount[
                            "PENDIENTE APROBAR POR ADMINISTRADOR DE OBRA"
                        ] || 0);
                } else {
                    // Para otros estados, asignar el valor directamente
                    this.conteoSolicitudes[estado] = estadoCount[estado] || 0;
                }
            });
        },
        dateFormatChangeApi(data) {
            return dateFormatChange(data);
        },
        ChangeView(data) {
            this.setDAtaDashboard();
            this.details = !this.details;
            this.solicitud_selected = data ? data : [];
        },
        getNextApproverEmail(row) {
            const c = row?.aprobadores_correos || {};
            const desc = (row?.estado?.descripcion || '').toUpperCase();

            if (desc.includes('APROBADO')) return null;
            if (desc.includes('CREADO')) return c.a1 || null;
            if (desc.includes('PENDIENTE APROBAR POR ADMINISTRADOR')) return c.a1 || null;

            
            return c.a2 || c.a1 || null;
        }
    },
};
</script>
<style scoped>

:deep(.p-datatable-table){ table-layout: fixed; width: 100%; }
:deep(.p-datatable-thead th),
:deep(.p-datatable-tbody td){ padding: 6px 8px; }

/* 2) Columnas con ancho reducido (1..5 y 7) */
:deep(.p-datatable-thead th:nth-child(1)),
:deep(.p-datatable-tbody td:nth-child(1)){ width: 115px; text-align:center; }  /* Código */

:deep(.p-datatable-thead th:nth-child(2)),
:deep(.p-datatable-tbody td:nth-child(2)){ width: 170px; }                     /* Solicitante */

:deep(.p-datatable-thead th:nth-child(3)),
:deep(.p-datatable-tbody td:nth-child(3)){ width: 110px; text-align:center; }  /* Centro de costo */

:deep(.p-datatable-thead th:nth-child(4)),
:deep(.p-datatable-tbody td:nth-child(4)){ width: 110px; text-align:center; }  /* Fecha */

:deep(.p-datatable-thead th:nth-child(5)),
:deep(.p-datatable-tbody td:nth-child(5)){ width: 160px; text-align:center; }  /* Estado */

:deep(.p-datatable-thead th:nth-child(7)),
:deep(.p-datatable-tbody td:nth-child(7)){ width: 125px;  text-align:center; }  /* Colaboradores (iconos) */

/* 3) Siguiente Aprobador: sin ancho fijo y con salto de línea */
:deep(.p-datatable-thead th:nth-child(6)),
:deep(.p-datatable-tbody td:nth-child(6)){ width: 180px; text-align:center; }

</style>