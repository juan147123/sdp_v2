<template>
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
                                <InputText
                                    placeholder="Buscador general"
                                    v-model="dataTable.filters['global'].value"
                                    style="font-size: 0.9rem; height: 30px"
                                />
                            </div>
                        </template>
                        <template #empty>
                            <div class="w-full flex justify-content-center">
                                <span>Cargando datos</span>
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
                                    :options="filtersDropdownData.centro_costo"
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
                                <Badge
                                    :value="
                                        data.status === 1
                                            ? 'pendiente'
                                            : data.status === 2
                                            ? 'cancelado'
                                            : data.status === 3
                                            ? 'aprobado'
                                            : ''
                                    "
                                    :severity="
                                        data.status === 1
                                            ? 'warning'
                                            : data.status === 2
                                            ? 'danger'
                                            : data.status === 3
                                            ? 'success'
                                            : ''
                                    "
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
                            sortable
                            :showFilterMatchModes="false"
                        >
                            <template #body="{ data }">
                                <div class="text-center">
                                    <SplitButton
                                        menuButtonIcon="pi pi-cog"
                                        :model="getItems(data)"
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

export default {
    props: ["solicitud_selected"],
    emits: ["ChangeView"],
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
                comentario: "",
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
                data: self.solicitud_selected.solicitud_colaborador,
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
    methods: {
        ChangeView() {
            this.$emit("ChangeView");
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
        getItems(data) {
            return [
                {
                    label: "Archivos",
                    icon: "pi pi-folder",
                    command: () => {
                        this.setImagenes(data); // Llama a la función con data
                    },
                },
            ];
        },
    },
};
</script>
