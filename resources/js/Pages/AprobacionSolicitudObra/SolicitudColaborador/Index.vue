<template>
    <div v-if="this.details != false">
        <breadcrumbs :modules="breadcrumbs" />
        <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
        <div class="mt-4">
            <Button
                icon="pi pi-arrow-left"
                class="ml-2"
                label="regresar"
                style="font-size: 0.9rem; height: 30px"
                severity="danger"
                @click="ChangeView"
            />
        </div>
        <div class="contenedor-solicitud">
            <div class="box m-1 mt-3 bg-white p-3 border-round">
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
                                    class="flex justify-content-between align-items-center"
                                >
                                    <h5>
                                        Solicitud:
                                        {{ this.solicitud_selected.codigo }}
                                    </h5>
                                    <div>
                                        <InputText
                                            placeholder="Buscador general"
                                            v-model="
                                                dataTable.filters['global']
                                                    .value
                                            "
                                            style="
                                                font-size: 0.9rem;
                                                height: 30px;
                                            "
                                        />
                                        <SplitButton
                                            menuButtonIcon="pi pi-cog"
                                            :model="getItemsAll()"
                                            style="
                                                font-size: 0.9rem;
                                                height: 30px;
                                                margin-left: 5px;
                                            "
                                            :disabled="
                                                this.solicitud_selected
                                                    .status == 5 ||
                                                this.solicitud_selected
                                                    .status == 6
                                            "
                                        >
                                            {{}}
                                        </SplitButton>
                                    </div>
                                </div>
                            </template>
                            <template #empty>
                                <div class="w-full flex justify-content-center">
                                    <span>No hay datos que mostrar</span>
                                </div>
                            </template>
                            <Column
                                filterField="user_id"
                                field="user_id"
                                header="NP Usuario"
                                headerStyle="background-color:black; color:white"
                                sortable
                                :showFilterMatchModes="false"
                            >
                                <template #filter="{ filterModel }">
                                    <MultiSelect
                                        v-model="filterModel.value"
                                        :options="filtersDropdownData.user_id"
                                        placeholder="Cualquiera"
                                        class="p-column-filter"
                                        optionLabel="user_id"
                                        optionValue="user_id"
                                        filter
                                    >
                                    </MultiSelect>
                                </template>
                            </Column>
                            <Column
                                filterField="nombre_completo"
                                field="nombre_completo"
                                header="Nombres y apellidos"
                                headerStyle="background-color:black; color:white"
                                sortable
                                :showFilterMatchModes="false"
                            >
                                <template #filter="{ filterModel }">
                                    <MultiSelect
                                        v-model="filterModel.value"
                                        :options="
                                            filtersDropdownData.nombre_completo
                                        "
                                        placeholder="Cualquiera"
                                        class="p-column-filter"
                                        optionLabel="nombre_completo"
                                        optionValue="nombre_completo"
                                        filter
                                    >
                                    </MultiSelect>
                                </template>
                            </Column>
                            <Column
                                filterField="sap_maestro_causales_terminos"
                                field="sap_maestro_causales_terminos.name"
                                header="Motivo de desvinculación"
                                headerStyle="background-color:black; color:white"
                                sortable
                                :showFilterMatchModes="false"
                            >
                                <template #filter="{ filterModel }">
                                    <MultiSelect
                                        v-model="filterModel.value"
                                        :options="
                                            filtersDropdownData.sap_maestro_causales_terminos
                                        "
                                        placeholder="Cualquiera"
                                        class="p-column-filter"
                                        optionLabel="sap_maestro_causales_terminos"
                                        optionValue="sap_maestro_causales_terminos"
                                        filter
                                    >
                                    </MultiSelect>
                                </template>
                            </Column>
                            <Column
                                filterField="fecha_desvinculacion"
                                field="fecha_desvinculacion"
                                header="Fecha a desvincular"
                                headerStyle="background-color:black; color:white"
                                sortable
                                :showFilterMatchModes="false"
                            >
                            </Column>
                            <Column
                                filterField="centro_costo"
                                field="centro_costo"
                                header="Centro de costo"
                                headerStyle="background-color:black; color:white"
                                sortable
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
                                        filter
                                    >
                                    </MultiSelect>
                                </template>
                            </Column>
                            <Column
                                filterField="status"
                                field="status"
                                header="Estado"
                                headerStyle="background-color:black; color:white"
                                style="text-align: center"
                                sortable
                                :showFilterMatchModes="false"
                            >
                                <template #body="{ data }">
                                    <Tag
                                        :value="data.estado.descripcion"
                                        :severity="data.estado.color"
                                    />
                                </template>
                                <template #filter="{ filterModel }">
                                    <MultiSelect
                                        v-model="filterModel.value"
                                        :options="filtersDropdownData.status"
                                        placeholder="Cualquiera"
                                        class="p-column-filter"
                                        optionLabel="status"
                                        optionValue="status"
                                        filter
                                    >
                                    </MultiSelect>
                                </template>
                            </Column>
                            <Column
                                :field="null"
                                header="Acciones"
                                headerStyle="background-color:black; color:white"
                                :showFilterMatchModes="false"
                            >
                                <template #body="{ data }">
                                    <div class="text-center">
                                        <SplitButton
                                            menuButtonIcon="pi pi-cog"
                                            :model="getItems(data)"
                                            style="
                                                width: 2rem !important;
                                                height: 2rem !important;
                                            "
                                        >
                                            {{}}
                                        </SplitButton>
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                    <Modal
                        :archivosList="this.archivosList"
                        :visible="this.visible"
                        @setImagenes="this.setImagenes"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import Modal from "./Components/Modal.vue";
import Preloader from "@/Components/Preloader.vue";
import { setSwal } from "../../../../Utils/swal";
import { FilterMatchMode } from "primevue/api";
import PrimeVueComponents from "../../../../js/primevue.js";
import setLocaleES from "../../../primevue.config.js";
import * as mensajes from "../../../../Utils/message.js";

export default {
    props: ["solicitud_selected", "details"],
    emits: ["ChangeView", "getData"],
    components: {
        AppLayout,
        breadcrumbs,
        Modal,
        Preloader,
        ...PrimeVueComponents,
    },
    setup() {
        setLocaleES();
    },
    data() {
        var self = this;
        return {
            items: [
                {
                    label: "Vue Website",
                    icon: "pi pi-external-link",
                    command: () => {},
                },
            ],
            checkView: false,
            mensaje: "",
            isLoadingForm: false,
            ids: [],
            visible: false,
            optionDropdown: null,
            archivosList: [],
            form: this.$inertia.form({
                id: 0,
                status: 0,
                id_solicitud: 0,
                comentario_admin_obra: "",
            }),
            breadcrumbs: [
                {
                    label: "Solicitudes",
                    url: "/redirectpage/solicitud",
                    icon: "fa fa-book",
                },
                {
                    label: "Colaboradores",
                    url: "",
                    icon: "fa fa-users",
                },
            ],
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
                    user_created: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    centro_costo: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    estado: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                },
                globalFilterFields: [
                    "codigo",
                    "user_created",
                    "centro_costo",
                    "created_at",
                    "estado",
                ],
            },
            filtersDropdownData: {
                codigo: [],
                user_created: [],
                centro_costo: [],
                created_at: [],
                estado: [],
            },
        };
    },
    watch: {
        details: function (newValue, oldValue) {
            if (newValue == true) {
                this.dataTable.data =
                    this.solicitud_selected.solicitud_colaborador;
                this.initializeDropdownsData();
            }
        },
        solicitud_selected: function (newValue, oldValue) {
            if (newValue) {
                this.dataTable.data =
                    this.solicitud_selected.solicitud_colaborador;
            }
        },
    },
    methods: {
        ChangeView() {
            this.$emit("ChangeView");
        },

        getData() {
            this.$emit("getData");
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

            this.filtersDropdownData.user_created = [
                ...new Set(
                    this.dataTable.data
                        .filter((s) => s.user_created != "")
                        .map((s) => s.user_created)
                ),
            ].map((o) => {
                return { user_created: o };
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
                ...new Set(
                    this.dataTable.data
                        .filter((s) => s.estado != "")
                        .map((s) => s.estado)
                ),
            ].map((o) => {
                return { estado: o };
            });
        },
        setImagenes(data) {
            this.visible = !this.visible;
            this.archivosList = data ? data.archivos : [];
        },
        getItemsAll() {
            return [
                {
                    label: "Aprobar solicitudes",
                    icon: "pi pi-check",
                    command: () => {
                        this.updateAllStatus(5);
                    },
                },
                {
                    label: "Desaprobar solicitudes",
                    icon: "pi pi-times",
                    command: () => {
                        this.updateAllStatus(6);
                    },
                },
            ];
        },
        getItems(data) {
            const items = [
                {
                    label: "Archivos",
                    icon: "pi pi-folder",
                    command: () => {
                        this.setImagenes(data);
                    },
                },
                {
                    label: "Aprobar",
                    icon: "pi pi-check",
                    command: () => {
                        this.updateStatus(data.id, 5, data.id_solicitud);
                    },
                },
                {
                    label: "Desaprobar",
                    icon: "pi pi-times",
                    command: () => {
                        this.updateStatus(data.id, 6, data.id_solicitud);
                    },
                },
            ];

            return items.filter((item) => {
                if (
                    item.label === "Aprobar" &&
                    (data.status == 5 || data.status == 6)
                ) {
                    return false;
                }
                if (
                    item.label === "Desaprobar" &&
                    (data.status == 5 || data.status == 6)
                ) {
                    return false;
                }
                return true;
            });
        },

        async updateStatus(id, status, id_solicitud) {
            this.form.id = id;
            this.form.status = status;
            this.form.id_solicitud = id_solicitud;

            await new Promise((resolve) => {
                setSwal({
                    value: "updateStatusInput",
                    callback: async (comentario_admin_obra) => {
                        resolve();
                        this.update(comentario_admin_obra);
                    },
                });
            });
        },
        async update(comentario_admin_obra) {
            this.form.comentario_admin_obra = comentario_admin_obra;
            await axios
                .put(
                    this.route("solicitud.colaborador.update.status.cc"),
                    this.form
                )
                .then(async (response) => {
                    this.getData();
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
        async updateAll(status) {
            this.solicitud_selected.solicitud_colaborador.forEach(
                (solicitudColacorador) => {
                    if (solicitudColacorador.status == 7) {
                        this.ids.push(solicitudColacorador.id);
                    }
                }
            );

            await axios
                .put(this.route("solicitud.colaborador.update.masive.cc"), {
                    ids: this.ids,
                    status: status,
                    id_solicitud: this.solicitud_selected.id,
                })
                .then(async (response) => {
                    this.getData();
                });

            /*  this.$inertia.put(
                this.route("solicitud.colaborador.update.masive.cc"),
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
            ); */
        },
    },
};
</script>
