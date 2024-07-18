<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <breadcrumbs :modules="breadcrumbs" />
        <div class="box m-1 mt-5 bg-white p-3 border-round">
            <div class="container-fluid">
                <div class="box-body">
                    <DataTable
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
                            </div>
                        </template>
                        <template #empty>
                            <div class="w-full flex justify-content-center">
                                <span>No hay datos que mostrar</span>
                            </div>
                        </template>
                        <Column
                            field="avatar"
                            class="text-center w-1rem"
                            headerStyle="background-color:black; color:white;"
                            header="Avatar"
                        >
                            <template #body="{ data }">
                                <Avatar
                                    :image="data.avatar"
                                    class="bg-white"
                                    size="xlarge"
                                    shape="circle"
                                />
                            </template>
                        </Column>
                        <Column
                            filterField="name"
                            field="name"
                            headerStyle="background-color:black; color:white;"
                            sortable
                            header="Nombres y apellidos"
                            :showFilterMatchModes="false"
                        >
                            <template #filter="{ filterModel }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    :options="filtersDropdownData.name"
                                    placeholder="Cualquiera"
                                    class="p-column-filter"
                                    optionLabel="name"
                                    optionValue="name"
                                    filter
                                >
                                </MultiSelect>
                            </template>
                        </Column>
                        <Column
                            filterField="username"
                            field="username"
                            headerStyle="background-color:black; color:white;"
                            sortable
                            header="Nombres y apellidos"
                            :showFilterMatchModes="false"
                        >
                            <template #filter="{ filterModel }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    :options="filtersDropdownData.username"
                                    placeholder="Cualquiera"
                                    class="p-column-filter"
                                    optionLabel="username"
                                    optionValue="username"
                                    filter
                                >
                                </MultiSelect>
                            </template>
                        </Column>
                        <Column
                            :field="null"
                            class="text-center"
                            headerStyle="background-color:black; color:white;"
                            header="Acciones"
                        >
                            <template #body="{ data }">
                                <!-- {{ data.id_aplicacion_usuario }} -->
                                <Button
                                    icon="pi pi-trash"
                                    style="
                                        font-size: 0.9rem;
                                        width: 2rem !important;
                                        height: 2rem !important;
                                    "
                                    @click="
                                        ondeleteUser(data.id_aplicacion_usuario)
                                    "
                                />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import { rutaBase, dateFormatChange } from "../../../Utils/utils.js";
import { setSwal } from "../../../Utils/swal";
import Preloader from "@/Components/Preloader.vue";
import setLocaleES from "../../primevue.config.js";
import dayjs from "dayjs";
import { FilterMatchMode } from "primevue/api";
import PrimeVueComponents from "../../../js/primevue.js";
import * as mensajes from "../../../Utils/message.js";
export default {
    components: {
        AppLayout,
        breadcrumbs,
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
                    label: "Usuarios",
                    url: "/redirectpage/usuarios",
                    icon: "fa fa-users",
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
                    name: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    username: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                },
                globalFilterFields: [
                    "name",
                    "username"
                ],
            },
            filtersDropdownData: {
                name: [],
                username: []
            },
        };
    },
    async mounted() {
        await this.getData();
        this.initializeDropdownsData();
    },
    methods: {
        async getData() {
            this.mensaje = "Cargando datos espere ...";
            this.isLoadingForm = true;
            self = this;
            await axios
                .get(rutaBase + "/list/usuarios")
                .then(async (response) => {
                    if (response.status == 200) {
                        this.dataTable.data = response.data;
                    }
                    this.mensaje = "";
                    this.isLoadingForm = false;
                });
        },
        ondeleteUser(id) {
            setSwal({
                value: "deleteForm",
                callback: () => {
                    this.delete(id);
                },
            });
        },
        async delete(id) {
            this.mensaje = "Cargando datos espere ...";
            this.isLoadingForm = true;
            self = this;
            await axios
                .delete(rutaBase + "/delete/usuarios/" + id)
                .then(async (response) => {
                    if (response.status == 200) {
                        await this.getData();
                    }
                    this.mensaje = "";
                    this.isLoadingForm = false;
                    this.$toast.add({
                        severity: "success",
                        position: "top-right",
                        summary: "Notificación",
                        detail: mensajes.MENSAJE_EXITO,
                        life: 3000,
                    });
                });
        },
        initializeDropdownsData() {
            this.filtersDropdownData.name = [
                ...new Set(
                    this.dataTable.data
                        .filter((s) => s.name != "")
                        .map((s) => s.name)
                ),
            ].map((o) => {
                return { name: o };
            });
            this.filtersDropdownData.username = [
                ...new Set(
                    this.dataTable.data
                        .filter((s) => s.username != "")
                        .map((s) => s.username)
                ),
            ].map((o) => {
                return { username: o };
            });
        },
        dateFormatChangeApi(data) {
            return dateFormatChange(data);
        },
    },
};
</script>
