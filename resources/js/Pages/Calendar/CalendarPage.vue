<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <breadcrumbs :modules="breadcrumbs" />
        <div class="box m-1 mt-5 bg-white p-3 border-round">
            <div class="container-fluid">
                <div class="box-body">
                    <DataTable dataKey="id" :value="dataTable.data" :rows="dataTable.rows" showGridlines paginator
                        :paginatorTemplate="dataTable.paginatorTemplate" :currentPageReportTemplate="dataTable.currentPageReportTemplate
                            " :rowsPerPageOptions="dataTable.rowsPerPageOptions" sortMode="single"
                        :globalFilterFields="dataTable.globalFilterFields" v-model:filters="dataTable.filters"
                        filterDisplay="menu" FilterMatchMode>
                        <template #header>
                            <div class="flex justify-content-between align-items-center">
                                <Button label="Nuevo" @click="modalvisible = true" size="small" style="
                                                font-size: 0.9rem;
                                                height: 30px;
                                            " icon="pi pi-plus" severity="success" />
                                <InputText placeholder="Buscador general" v-model="dataTable.filters['global'].value
                                    " style="font-size: 0.9rem; height: 30px" />
                            </div>
                        </template>
                        <template #empty>
                            <div class="w-full flex justify-content-center">
                                <span>No hay datos que mostrar</span>
                            </div>
                        </template>
                        <Column field="fecha_inicio" headerStyle="background-color:black; color:white" sortable
                            header="FECHA DE INICIO">
                        </Column>
                        <Column field="fecha_fin" headerStyle="background-color:black; color:white" sortable
                            header="FECHA DE FIN"></Column>
                        <Column field="semana" headerStyle="background-color:black; color:white" sortable
                            header="NR° DE SEMANA"></Column>
                        <Column :field="null" header="ACCIONES" headerStyle="background-color:black; color:white"
                            style="text-align: center">
                            <template #body="{ data }">
                                <Button icon="pi pi-pencil" class="ml-2" style="
                                            font-size: 0.9rem;
                                            height: 30px;
                                            width: 2rem !important;
                                            height: 2rem !important;
                                        " severity="info" v-tooltip.top="'editar'" />
                                <Button icon="pi pi-trash" class="ml-2" style="
                                            font-size: 0.9rem;
                                            height: 30px;
                                            width: 2rem !important;
                                            height: 2rem !important;
                                        " severity="danger" v-tooltip.top="'eliminar'" />

                            </template>
                        </Column>

                    </DataTable>
                </div>
            </div>
        </div>
        <Dialog v-model:visible="modalvisible" modal :header="this.formData.id == 0 ? 'REGISTRAR' : 'EDITAR'"
            :style="{ width: '25rem' }">
            <div class="mb-3 flex flex-column">
                <label for="input1" class="form-label">Fecha de inicio</label>
                <Calendar showIcon class="w-full" locale="es" v-model="this.formData.fecha_inicio" dateFormat="dd/mm/yy"
                    required />
            </div>
            <div class="mb-3 flex flex-column">
                <label for="input1" class="form-label">Fecha de fín</label>
                <Calendar showIcon class="w-full" locale="es" v-model="this.formData.fecha_fin" dateFormat="dd/mm/yy"
                    required />
            </div>
            <div class="mb-3 flex flex-column">
                <label for="input1" class="form-label">Número de semana</label>
                <input class="form-control" v-model="this.formData.semana" required />
            </div>
            <div class="flex justify-content-end gap-2">
                <Button type="button" label="Cancelar" severity="danger" @click="closeModal"></Button>
                <Button type="button" label="Guardar" severity="success" @click=""></Button>
            </div>
        </Dialog>
    </AppLayout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import breadcrumbs from "@/Components/Breadcrumbs.vue";
import Preloader from "@/Components/Preloader.vue";
import PrimeVueComponents from "../../../js/primevue.js";
import { FilterMatchMode } from "primevue/api";
import { rutaBase, dateFormatChange } from "../../../Utils/utils.js";

export default {
    components: {
        AppLayout,
        breadcrumbs,
        Preloader,
        ...PrimeVueComponents,

    },
    data() {
        var self = this;
        return {
            breadcrumbs: [
                {
                    label: "Calendario",
                    url: "redirectpage/calendar",
                    icon: "fa fa-calendar",
                },
            ],
            mensaje: "",
            isLoadingForm: false,
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
                    fecha_inicio: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    fecha_fin: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    semana: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                },
                globalFilterFields: [
                    "fecha_inicio",
                    "fecha_fin",
                    "semana",
                ],
            },
            modalvisible: false,
            formData: this.$inertia.form(
                {
                    id: 0,
                    fecha_inicio: "",
                    fecha_fin: "",
                    semana: ""
                }
            )
        };
    },
    mounted() {
        this.getData();
    },
    methods: {
        async getData() {
            self = this;

            this.mensaje = "Cargando datos espere ...";
            this.isLoadingForm = true;
            await axios
                .get(rutaBase + "/list/calendar")
                .then(async (response) => {
                    if (response.status == 200) {
                        this.dataTable.data = response.data;
                    }

                    this.mensaje = "";
                    this.isLoadingForm = false;
                });
        },
        closeModal() {
            this.modalvisible = false
            this.formData.reset();
        }
    },
}

</script>