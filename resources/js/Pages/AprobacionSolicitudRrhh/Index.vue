<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <div v-if="this.details != true">
            <breadcrumbs :modules="breadcrumbs" />
            <div class="box m-1 mt-5 bg-white p-3 border-round">
                <div class="container-fluid">
                    <div class="box-body">
                        <DataTable
                            dataKey="id"
                            :value="dataTable.data"
                            :rows="dataTable.rows"
                            showGridlines
                            paginator
                            :paginatorTemplate="dataTable.paginatorTemplate"
                            :currentPageReportTemplate="
                                dataTable.currentPageReportTemplate
                            "
                            :rowsPerPageOptions="dataTable.rowsPerPageOptions"
                            sortMode="single"
                            :globalFilterFields="dataTable.globalFilterFields"
                            v-model:filters="dataTable.filters"
                            filterDisplay="menu"
                            FilterMatchMode
                        >
                            <template #header>
                                <div
                                    class="flex justify-content-end align-items-center"
                                >
                                    <InputText
                                        placeholder="Buscador general"
                                        v-model="
                                            dataTable.filters['global'].value
                                        "
                                        style="font-size: 0.9rem; height: 30px"
                                    />
                                </div>
                            </template>
                            <template #empty>
                                <div class="w-full flex justify-content-center">
                                    <span>No hay datos que mostrar</span>
                                </div>
                            </template>
                            <Column
                                field="codigo"
                                header="Código de solicitud"
                                headerStyle="background-color:black; color:white"
                                sortable
                                filterField="codigo"
                                :showFilterMatchModes="false"
                            >
                                <template #filter="{ filterModel }">
                                    <MultiSelect
                                        v-model="filterModel.value"
                                        :options="filtersDropdownData.codigo"
                                        placeholder="Cualquiera"
                                        class="p-column-filter"
                                        optionLabel="codigo"
                                        optionValue="codigo"
                                    >
                                    </MultiSelect>
                                </template>
                            </Column>
                            <Column
                                field="user_created"
                                header="Solicitante"
                                headerStyle="background-color:black; color:white"
                                sortable
                                filterField="user_id"
                                :showFilterMatchModes="false"
                            >
                            </Column>
                            <Column
                                field="centro_costo"
                                header="Centro de costo"
                                headerStyle="background-color:black; color:white"
                                sortable
                                filterField="centro_costo"
                                :showFilterMatchModes="false"
                            >
                                <template #filter="{ filterModel }">
                                    <MultiSelect
                                        v-model="filterModel.value"
                                        :options="
                                            filtersDropdownData.centro_costo
                                        "
                                        placeholder="Cualquiera"
                                        class="p-column-filter"
                                        optionLabel="centro_costo"
                                        optionValue="centro_costo"
                                    >
                                    </MultiSelect
                                ></template>
                            </Column>
                            <Column
                                field="created_at"
                                header="Fecha de creación"
                                headerStyle="background-color:black; color:white"
                                sortable
                                filterField="created_at"
                                :showFilterMatchModes="false"
                            >
                                <template #body="{ data }">
                                    <div>
                                        {{
                                            dateFormatChangeApi(data.created_at)
                                        }}
                                    </div>
                                </template>
                            </Column>
                            <Column
                                field="estado.descripcion"
                                filterField="estado"
                                header="Estado"
                                headerStyle="background-color:black; color:white"
                                style="text-align: center"
                                sortable
                                :showFilterMatchModes="false"
                            >
                                <template #body="{ data }">
                                    <Tag
                                        :value="
                                            data.estado.id == 3
                                                ? 'PENDIENTE'
                                                : data.estado.descripcion
                                        "
                                        :severity="
                                            data.estado.id == 3
                                                ? 'warning'
                                                : data.estado.color
                                        "
                                    />
                                </template>
                                <template #filter="{ filterModel }">
                                    <MultiSelect
                                        v-model="filterModel.value"
                                        :options="filtersDropdownData.estado"
                                        placeholder="Cualquiera"
                                        class="p-column-filter"
                                        optionLabel="estado.descripcion"
                                        optionValue="estado"
                                    >
                                    </MultiSelect
                                ></template>
                            </Column>
                            <Column
                                :field="null"
                                filterField="user_id"
                                header="Colaboradores"
                                headerStyle="background-color:black; color:white"
                                sortable
                                style="text-align: center"
                                :showFilterMatchModes="false"
                            >
                                <template #body="{ data }">
                                    <Button
                                        icon="pi pi-users"
                                        class="ml-2"
                                        style="font-size: 0.9rem; height: 30px"
                                        severity="info"
                                        @click="ChangeView(data)"
                                        v-tooltip.top="'colaboradores'"
                                    />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <SolicitudesColaborador
            @ChangeView="this.ChangeView"
            @getData="this.getData"
            :solicitud_selected="solicitud_selected"
            :details="this.details"
        />
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
                    url: "/redirectpage/solicitud/aprobar",
                    icon: "fa fa-book",
                },
            ],
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
        };
    },
    async mounted() {
        await this.getData();
        this.initializeDropdownsData();
    },
    methods: {
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
                .get(rutaBase + "/list/solicitud/rrhh")
                .then(async (response) => {
                    if (response.status == 200) {
                        let filteredData = response.data.filter((item) => {
                            // Solo mantener los elementos si no tienen estado.id == 5 y aprobado_visitador_obra no es 7
                            return !(
                                item.estado.id === 5 &&
                                item.solicitud_colaborador.some(
                                    (colaborador) =>
                                        colaborador.aprobado_visitador_obra ===
                                        7
                                )
                            );
                        });

                        // Asignar la lista filtrada a la tabla de datos
                        this.dataTable.data = filteredData;

                        if (this.details == true) {
                            let oldId = self.solicitud_selected.id;
                            let newselected = self.dataTable.data.find(
                                (item) => item.id === oldId
                            );
                            this.solicitud_selected = newselected;
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
