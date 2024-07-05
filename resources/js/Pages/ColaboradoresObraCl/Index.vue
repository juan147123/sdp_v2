<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <breadcrumbs :modules="breadcrumbs" />
        <div class="box m-1 mt-5 bg-white p-3 border-round">
            <div class="container-fluid">
                <div class="box-body">
                    <DataTable
                        v-model:selection="colaboradoresDetalle"
                        dataKey="user_id"
                        :value="dataTable.data"
                        FilterMatchMode 
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
                    >
                        <template #header>
                            <div
                                class="flex justify-content-end align-items-center"
                            >
                                <InputText
                                    placeholder="Buscador general"
                                    v-model="dataTable.filters['global'].value"
                                    style="font-size: 0.9rem; height: 30px"
                                />

                                <Button
                                    label="Limpiar"
                                    icon="pi pi-check-circle"
                                    class="ml-2"
                                    style="font-size: 0.9rem; height: 30px"
                                    severity="danger"
                                    @click="onClickClean"
                                    v-tooltip.top="'Limpiar seleccionados'"
                                />
                                <Button
                                    label="Nuevo"
                                    icon="pi pi-plus"
                                    class="ml-2"
                                    style="font-size: 0.9rem; height: 30px"
                                    severity="info"
                                    @click="showModal"
                                    :disabled="
                                        this.colaboradoresDetalle.length == 0
                                    "
                                    v-tooltip.top="'Nueva solicitud'"
                                />
                            </div>
                        </template>
                        <template #empty>
                            <div class="w-full flex justify-content-center">
                                <span>No hay datos que mostrar</span>
                            </div>
                        </template>
                        <Column
                            headerStyle="width: 3rem;background-color:black;"
                        >
                            <template #body="{ data }">
                                <Checkbox
                                    v-model="colaboradoresDetalle"
                                    :disabled="
                                        data.solicitudes == 0 ? false : true
                                    "
                                    :value="data"
                                />
                            </template>
                        </Column>
                        <Column
                            filterField="user_id"
                            field="user_id"
                            headerStyle="width:30px;background-color:black; color:white;"
                            sortable
                            style="text-align: center"
                            header="NP"
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
                                >
                                </MultiSelect>
                            </template>
                        </Column>
                        <Column
                            filterField="full_name"
                            field="full_name"
                            headerStyle="background-color:black; color:white;"
                            sortable
                            header="Nombres y apellidos"
                            :showFilterMatchModes="false"
                        >
                            <template #filter="{ filterModel }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    :options="filtersDropdownData.full_name"
                                    placeholder="Cualquiera"
                                    class="p-column-filter"
                                    optionLabel="full_name"
                                    optionValue="full_name"
                                    filter
                                >
                                </MultiSelect>
                            </template>
                        </Column>

                        <Column
                            filterField="razon_social"
                            field="razon_social"
                            header="Empresa"
                            headerStyle="background-color:black; color:white"
                            sortable
                            :showFilterMatchModes="false"
                        >
                            <template #filter="{ filterModel }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    :options="filtersDropdownData.razon_social"
                                    placeholder="Cualquiera"
                                    class="p-column-filter"
                                    optionLabel="razon_social"
                                    optionValue="razon_social"
                                    filter
                                >
                                </MultiSelect>
                            </template>
                        </Column>
                        <Column
                            filterField="full_unidad"
                            field="full_unidad"
                            header="Unidad de negocio"
                            headerStyle="background-color:black; color:white"
                            sortable
                            :showFilterMatchModes="false"
                        >
                            <template #filter="{ filterModel }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    :options="filtersDropdownData.full_unidad"
                                    placeholder="Cualquiera"
                                    class="p-column-filter"
                                    optionLabel="full_unidad"
                                    optionValue="full_unidad"
                                    filter
                                >
                                </MultiSelect>
                            </template>
                        </Column>

                        <Column
                            filterField="full_ceco"
                            field="full_ceco"
                            header="Centro de costo"
                            headerStyle="background-color:black; color:white"
                            sortable
                            :showFilterMatchModes="false"
                        >
                            <template #filter="{ filterModel }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    :options="filtersDropdownData.full_ceco"
                                    placeholder="Cualquiera"
                                    class="p-column-filter"
                                    optionLabel="full_ceco"
                                    optionValue="full_ceco"
                                    filter
                                >
                                </MultiSelect>
                            </template>
                        </Column>
                        <Column
                            filterField="full_dep"
                            field="full_dep"
                            header="Departamento"
                            headerStyle="background-color:black; color:white"
                            sortable
                            :showFilterMatchModes="false"
                        >
                            <template #filter="{ filterModel }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    :options="filtersDropdownData.full_dep"
                                    placeholder="Cualquiera"
                                    class="p-column-filter"
                                    optionLabel="full_dep"
                                    optionValue="full_dep"
                                    filter
                                >
                                </MultiSelect>
                            </template>
                        </Column>

                        <Column
                            field="planta_noplanta"
                            header="Ubicación"
                            headerStyle="background-color:black; color:white"
                            style="text-align: center"
                            sortable
                        ></Column>
                        <Column
                            field="solicitudes"
                            header="Tiene solicitud"
                            headerStyle="background-color:black; color:white"
                            style="text-align: center"
                            sortable
                            ><template #body="{ data }">
                                <Tag
                                    value="4"
                                    :severity="
                                        data.solicitudes != 0
                                            ? 'danger'
                                            : 'success'
                                    "
                                >
                                    {{
                                        data.solicitudes != 0 ? "Sí" : "No"
                                    }}</Tag
                                >
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
        <FormularioMultiple
            :terminos="this.terminos"
            :visible="this.visible"
            :colaboradoresDetalle="colaboradoresDetalle"
            @showModal="showModal"
            @getData="getData"
        />
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import Preloader from "@/Components/Preloader.vue";
import { rutaBase } from "../../../Utils/utils.js";
import FormularioMultiple from "./Formularios/FormularioMultiple.vue";
import PrimeVueComponents from "../../../js/primevue.js";
import { FilterMatchMode } from "primevue/api";
import setLocaleES from "../../primevue.config.js";

export default {
    components: {
        AppLayout,
        breadcrumbs,
        Preloader,
        FormularioMultiple,
        ...PrimeVueComponents,
    },
    setup() {
        setLocaleES();
    },
    data() {
        return {
            breadcrumbs: [
                {
                    label: "Colaboradores",
                    url: "redirectpage/colaboradores/pe",
                    icon: "fa fa-users",
                },
            ],
            user_id: [],
            mensaje: "",
            isLoadingForm: false,
            visible: false,
            colaboradoresDetalle: [],
            terminos: [],
            data: [],
            selectedData: [],
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
                    user_id: { value: null, matchMode: FilterMatchMode.IN },
                    full_name: { value: null, matchMode: FilterMatchMode.IN },
                    razon_social: {
                        value: null,
                        matchMode: FilterMatchMode.IN,
                    },
                    full_unidad: {
                        value: null,
                        matchMode: FilterMatchMode.IN,
                    },
                    full_ceco: {
                        value: null,
                        matchMode: FilterMatchMode.IN,
                    },
                    full_dep: {
                        value: null,
                        matchMode: FilterMatchMode.IN,
                    },
                },
                globalFilterFields: [
                    "user_id",
                    "full_name",
                    "razon_social",
                    "full_unidad",
                    "full_ceco",
                    "full_dep",
                    "planta_noplanta",
                    "solicitudes",
                ],
            },
            filtersDropdownData: {
                user_id: [],
                full_name: [],
                razon_social: [],
                full_unidad: [],
                full_ceco: [],
                full_dep: [],
            },
        };
    },
    async mounted() {
        await this.getData();
        this.initializeDropdownsData();
    },
    methods: {
        onClickClean() {
            this.colaboradoresDetalle = [];
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
        showModal() {
            this.visible = !this.visible;
            if (this.visible == true) {
                this.getMotivos();
            } else {
                this.colaboradoresDetalle = [];
            }
        },
        async getData() {
            await axios
                .get(rutaBase + "/colaboradores/obra/cl")
                .then(async (response) => {
                    if (response.status == 200) {
                        this.dataTable.data = response.data;
                    }
                });
        },
        initializeDropdownsData() {
            this.filtersDropdownData.user_id = [
                ...new Set(
                    this.dataTable.data
                        .filter((s) => s.user_id != "")
                        .map((s) => s.user_id)
                ),
            ].map((o) => {
                return { user_id: o };
            });

            this.filtersDropdownData.full_name = [
                ...new Set(
                    this.dataTable.data.filter((s) => s.full_name != "")
                ),
            ].map((o) => {
                return { full_name: o.full_name };
            });

            this.filtersDropdownData.razon_social = [
                ...new Map(
                    this.dataTable.data
                        .filter((s) => s.razon_social != "")
                        .map((item) => [item.razon_social, item])
                ).values(),
            ].map((o) => {
                return { razon_social: o.razon_social };
            });

            this.filtersDropdownData.full_unidad = [
                ...new Map(
                    this.dataTable.data
                        .filter((s) => s.full_unidad != "")
                        .map((item) => [item.full_unidad, item])
                ).values(),
            ].map((o) => {
                return { full_unidad: o.full_unidad };
            });

            this.filtersDropdownData.full_ceco = [
                ...new Map(
                    this.dataTable.data
                        .filter((s) => s.full_ceco != "")
                        .map((item) => [item.full_ceco, item])
                ).values(),
            ].map((o) => {
                return { full_ceco: o.full_ceco };
            });

            this.filtersDropdownData.full_dep = [
                ...new Map(
                    this.dataTable.data
                        .filter((s) => s.full_dep != "")
                        .map((item) => [item.full_dep, item])
                ).values(),
            ].map((o) => {
                return { full_dep: o.full_dep };
            });
        },
    },
};
</script>
