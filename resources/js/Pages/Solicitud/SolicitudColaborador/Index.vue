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
                                field="sap_maestro_causales_terminos.name"
                                filterField="sap_maestro_causales_terminos"
                                header="Motivo de desvinculación"
                                headerStyle="background-color:black; color:white"
                                style="text-align: center"
                                sortable
                                :showFilterMatchModes="false"
                            >
                                <template #body="{ data }">
                                    {{
                                        data.sap_maestro_causales_terminos.name
                                    }}
                                </template>
                                <template #filter="{ filterModel }">
                                    <MultiSelect
                                        v-model="filterModel.value"
                                        :options="
                                            filtersDropdownData.sap_maestro_causales_terminos
                                        "
                                        placeholder="Cualquiera"
                                        class="p-column-filter"
                                        optionLabel="sap_maestro_causales_terminos.name"
                                        optionValue="sap_maestro_causales_terminos"
                                    >
                                    </MultiSelect
                                ></template>
                            </Column>
                            <Column
                                filterField="fecha_desvinculacion"
                                field="fecha_desvinculacion"
                                header="Fecha a desvincular"
                                headerStyle="background-color:black; color:white"
                                sortable
                                :showFilterMatchModes="false"
                            >
                                <template #body="{ data }">
                                    <div>
                                        {{
                                            dateFormatChangeApi(
                                                data.fecha_desvinculacion
                                            )
                                        }}
                                    </div>
                                </template>
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
                                            setTagStatusValue(data).descripcion
                                        "
                                        :severity="
                                            setTagStatusValue(data).color
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
                                            style="
                                                width: 2rem !important;
                                                height: 2rem !important;
                                            "
                                            >{{}}
                                        </SplitButton>
                                        <Button
                                            v-if="
                                                this.solicitud_selected.obra ==
                                                0
                                            "
                                            icon="pi pi-check-square"
                                            @click="showModalCheckList(data)"
                                            class="ml-2"
                                            style="
                                                width: 2rem !important;
                                                height: 2rem !important;
                                            "
                                        />
                                        <Button
                                            v-if="
                                                data.estadoadmin?.id == 7 &&
                                                data.enable == 1
                                            "
                                            icon="pi pi-exclamation-triangle "
                                            class="ml-2"
                                            style="
                                                width: 2rem !important;
                                                height: 2rem !important;
                                            "
                                            severity="warning"
                                            @click="alertaAdminObra(data)"
                                        />
                                        <Button
                                            v-if="
                                                data.estadovisitador?.id == 7 &&
                                                data.enable == 1
                                            "
                                            icon="pi pi-exclamation-triangle "
                                            class="ml-2"
                                            style="
                                                width: 2rem !important;
                                                height: 2rem !important;
                                            "
                                            severity="warning"
                                            @click="alertaAVisitadorObra(data)"
                                        />

                                        <Button
                                            v-if="
                                                data.estadorrhh?.id == 7 &&
                                                data.enable == 1
                                            "
                                            icon="pi pi-exclamation-triangle "
                                            class="ml-2"
                                            style="
                                                width: 2rem !important;
                                                height: 2rem !important;
                                            "
                                            severity="warning"
                                            @click="alertaRrhh(data)"
                                        />
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

                    <Dialog
                        v-model:visible="visibleComentarioAdmin"
                        modal
                        header=" "
                        :style="{ width: '25rem' }"
                    >
                        <template #header>
                            <span class="p-text-secondary">{{
                                this.title
                            }}</span>
                        </template>
                        <div class="pl-1 p-text-secondary border p-3">
                            {{ this.comentario }}
                        </div>

                        <div class="flex flex-column pt-4">
                            <div
                                class="mb-3 p-text-secondary text-left"
                                style="font-size: 14px"
                            >
                                ¿Desea cancelar la solicitud y enviar al flujo
                                inicial a este colaborador?
                            </div>
                            <div class="flex justify-content-end gap-2">
                                <Button
                                    type="button"
                                    label="Cancelar Solicitud"
                                    severity="info"
                                    @click="desactivarSolicitudcolaborador()"
                                    class="h-2rem"
                                ></Button>
                                <Button
                                    type="button"
                                    label="Cancelar"
                                    @click="visibleComentarioAdmin = false"
                                    class="h-2rem"
                                ></Button>
                            </div>
                        </div>
                    </Dialog>
                </div>
            </div>
        </div>
        <ModalChecklist
            :modalcheckVisible="modalcheckVisible"
            :solicitudAreaCheck="solicitudAreaCheck"
            @showModalCheckList="showModalCheckList"
        />
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
import { rutaBase, dateFormatChange } from "../../../../Utils/utils.js";
import ModalChecklist from "./Components/ModalChecklist.vue";
export default {
    props: ["solicitud_selected", "details"],
    emits: ["ChangeView", "getData"],
    components: {
        AppLayout,
        breadcrumbs,
        Modal,
        Preloader,
        ...PrimeVueComponents,
        ModalChecklist,
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
            title: "",
            isLoadingForm: false,
            ids: [],
            visible: false,
            visibleComentarioAdmin: false,
            optionDropdown: null,
            archivosList: [],
            solicitudAreaCheck: [],
            comentario: "",
            id_deactivate: 0,
            id_data_deactivate: 0,
            modalcheckVisible: false,
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
                data: self.solicitud_selected.solicitud_colaborador2,
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
                    user_id: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    nombre_completo: {
                        value: null,
                        matchMode: FilterMatchMode.CONTAINS,
                    },
                    sap_maestro_causales_terminos: {
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
                    "user_id",
                    "nombre_completo",
                    "sap_maestro_causales_terminos",
                    "centro_costo",
                    "estado",
                ],
            },
            filtersDropdownData: {
                user_id: [],
                nombre_completo: [],
                sap_maestro_causales_terminos: [],
                centro_costo: [],
                estado: [],
            },
        };
    },
    watch: {
        details: function (newValue, oldValue) {
            if (newValue == true) {
                this.initializeDropdownsData();
            }
        },
        solicitud_selected: function (newValue, oldValue) {
            if (newValue) {
                this.dataTable.data =
                    this.solicitud_selected.solicitud_colaborador2;
            }
        },
    },
    methods: {
        getData() {
            this.$emit("getData");
        },
        ChangeView() {
            this.$emit("ChangeView");
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

            this.filtersDropdownData.nombre_completo = [
                ...new Set(
                    this.dataTable.data
                        .filter((s) => s.nombre_completo != "")
                        .map((s) => s.nombre_completo)
                ),
            ].map((o) => {
                return { nombre_completo: o };
            });

            this.filtersDropdownData.sap_maestro_causales_terminos = [
                ...new Map(
                    this.dataTable.data
                        .filter(
                            (s) =>
                                s.sap_maestro_causales_terminos != "" &&
                                s.sap_maestro_causales_terminos.name != null
                        )
                        .map((s) => [
                            s.sap_maestro_causales_terminos.name,
                            s.sap_maestro_causales_terminos,
                        ])
                ).values(),
            ].map((o) => {
                return { sap_maestro_causales_terminos: o };
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
                        this.setImagenes(data);
                    },
                },
            ];
        },
        alertaAdminObra(data) {
            this.visibleComentarioAdmin = !this.visibleComentarioAdmin;
            this.title = "Comentario del Administrador de Obra";
            this.comentario = data.comentario_admin_obra;

            this.id_deactivate = data.id;
            this.id_data_deactivate = data.id_solicitud;
        },

        alertaAVisitadorObra(data) {
            this.visibleComentarioAdmin = !this.visibleComentarioAdmin;
            this.title = "Comentario del Visitador de Obra";
            this.comentario = data.comentario_visitador;
            this.id_deactivate = data.id;
            this.id_data_deactivate = data.id_solicitud;
        },
        alertaRrhh(data) {
            this.visibleComentarioAdmin = !this.visibleComentarioAdmin;
            this.title = "Comentario del administrador de RRHH";
            this.comentario = data.comentario_rrhh;
            this.id_deactivate = data.id;
            this.id_data_deactivate = data.id_solicitud;
        },
        async desactivarSolicitudcolaborador() {
            try {
                const response = await axios.delete(
                    rutaBase +
                        "/solicitud/colaborador/delete/" +
                        this.id_deactivate +
                        "/" +
                        this.id_data_deactivate
                );

                this.getData();
                this.id_deactivate = 0;
                this.id_data_deactivate = 0;
                this.comentario = "";
                this.visibleComentarioAdmin = false;
                this.ChangeView();
            } catch (error) {
                console.error("error:", error);
            }
        },
        dateFormatChangeApi(data) {
            return dateFormatChange(data);
        },
        setTagStatusValue(data) {
            var descripcion = "";
            var color = "";
            if (
                (data.estadoadmin && data.estadoadmin.id == 7) ||
                (data.estadovisitador && data.estadovisitador.id == 7) ||
                (data.estadorrhh && data.estadorrhh.id == 7)
            ) {
                descripcion = "RECHAZADO";
                color = "danger";
            } else if (data.estadorrhh && data.estadorrhh.id == 6) {
                descripcion = "APROBADO";
                color = "success";
            } else if (
                data.status == 1 ||
                data.status == 2 ||
                data.status == 3
            ) {
                descripcion = "PENDIENTE";
                color = "warning";
            } else if (data.status == 4 || data.status == 6) {
                descripcion = "APROBADO";
                color = "success";
            } else {
                descripcion = "RECHAZADO";
                color = "danger";
            }
            return {
                descripcion: descripcion,
                color: color,
            };
        },
        showModalCheckList(data) {
            this.modalcheckVisible = !this.modalcheckVisible;
            this.solicitudAreaCheck = data;
        },
    },
};
</script>
