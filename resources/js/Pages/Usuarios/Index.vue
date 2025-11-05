<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <AppLayout>
        <breadcrumbs :modules="breadcrumbs" />
        <div class="box m-1 mt-5 bg-white p-3 border-round">
            <div class="container-fluid">
                <div class="box-body">
                    <div class="flex justify-content-end align-items-center mb-3">
                        <Button label="Registrar" severity="success" class="h-2rem" @click="visible = true" />
                    </div>

                    <DataTable dataKey="user_id" :value="dataTable.data" FilterMatchMode :rows="dataTable.rows"
                        showGridlines paginator :paginatorTemplate="dataTable.paginatorTemplate"
                        :currentPageReportTemplate="dataTable.currentPageReportTemplate
                            " :rowsPerPageOptions="dataTable.rowsPerPageOptions" sortMode="single"
                        :globalFilterFields="dataTable.globalFilterFields" v-model:filters="dataTable.filters"
                        filterDisplay="menu">
                        <template #header>
                            <div class="flex justify-content-end align-items-center">
                                <InputText placeholder="Buscador general" v-model="dataTable.filters['global'].value"
                                    style="font-size: 0.9rem; height: 30px" />
                            </div>
                        </template>
                        <template #empty>
                            <div class="w-full flex justify-content-center">
                                <span>No hay datos que mostrar</span>
                            </div>
                        </template>
                        <Column field="avatar" class="text-center w-1rem"
                            headerStyle="background-color:black; color:white;" header="Avatar">
                            <template #body="{ data }">
                                <Avatar :image="data.avatar" class="bg-white" size="xlarge" shape="circle" />
                            </template>
                        </Column>
                        <Column filterField="name" field="name" headerStyle="background-color:black; color:white;"
                            sortable header="Nombres y apellidos" :showFilterMatchModes="false">
                            <template #filter="{ filterModel }">
                                <MultiSelect v-model="filterModel.value" :options="filtersDropdownData.name"
                                    placeholder="Cualquiera" class="p-column-filter" optionLabel="name"
                                    optionValue="name" filter>
                                </MultiSelect>
                            </template>
                        </Column>
                        <Column filterField="username" field="username"
                            headerStyle="background-color:black; color:white;" sortable header="Correo corporativo"
                            :showFilterMatchModes="false">
                            <template #filter="{ filterModel }">
                                <MultiSelect v-model="filterModel.value" :options="filtersDropdownData.username"
                                    placeholder="Cualquiera" class="p-column-filter" optionLabel="username"
                                    optionValue="username" filter>
                                </MultiSelect>
                            </template>
                        </Column>
                        <Column filterField="nombre_rol" field="nombre_rol"
                            headerStyle="background-color:black; color:white;" sortable header="Permisos"
                            :showFilterMatchModes="false">
                            <template #body="{ data }">
                                <div v-for="(rol, index) in getNombresRolesLegibles(data.objeto_permitido)"
                                    :key="index">
                                    {{ rol }}
                                </div>
                            </template>
                        </Column>
                        <Column :field="null" class="text-center" headerStyle="background-color:black; color:white;"
                            header="Acciones">
                            <template #body="{ data }">
                                <!-- {{ data.id_aplicacion_usuario }} -->
                                <Button icon="pi pi-pencil" class="m-1" severity="info" style="
                                        font-size: 0.9rem;
                                        width: 2rem !important;
                                        height: 2rem !important;
                                    " @click="
                                        showUser(data)
                                        " />
                                <Button icon="pi pi-trash" class="m-1" style="
                                        font-size: 0.9rem;
                                        width: 2rem !important;
                                        height: 2rem !important;
                                    " @click="
                                        ondeleteUser(data.id_aplicacion_usuario)
                                        " />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <Dialog v-model:visible="visible" :header="this.form.id_aplicacion_usuario == 0 ? 'Registrar' : 'Actualizar'"
            :style="{ width: '40rem' }">
            <div class="flex align-items-center gap-3 mb-3">
                <label for="username" class="font-semibold w-6rem">Nombre completo</label>
                <InputText v-model="form.name" class="flex-auto" autocomplete="off" />
            </div>
            <div class="flex align-items-center gap-3 mb-5">
                <label for="email" class="font-semibold w-6rem">Correo corporativo</label>
                <InputText v-model="form.username" class="flex-auto" autocomplete="off" />
            </div>
            <div class="flex justify-content-end gap-2">
                <Button class="h-2rem" type="button" label="Cancelar" severity="danger"
                    @click="onCancel"></Button>
                <Button class="h-2rem" type="button" :label="this.form.id_aplicacion_usuario == 0 ? 'Registrar' : 'Actualizar'" severity="success" @click="submit"></Button>
            </div>
        </Dialog>
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
            visible: false,
            part: 0,
            solicitud_selected: [],
            form: this.$inertia.form({
                id_aplicacion_usuario: 0,
                name: "",
                username: "",
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
                    nombre_rol: {
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
                username: [],
                nombre_rol: []
            },
        };
    },
    async mounted() {
        await this.getData();
        this.initializeDropdownsData();
    },
    methods: {
        getNombresRolesLegibles(codigosRoles) {
            const mapaRoles = {
                'LIDEROBRACL': 'Administrador obra RRHH',
                'ADMRRHH': 'Administrador RRHH',
                'APROBOBRA': 'Administrador de obra',
                'APROBVISITADOR': 'Gerente de proyecto',
                'SUPERAD': 'Superadministrador',
                'LIDERCL': 'Líder Chile',
                'LIDERPE': 'Líder Perú',
                'ADMRAREA': 'Administrador de Área'
            };

            return codigosRoles
                .split(',')
                .map(rol => mapaRoles[rol] || rol);
        }
        ,
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
        showUser(data) {
            this.form.id_aplicacion_usuario = data.id_aplicacion_usuario;
            this.form.name = data.name;
            this.form.username = data.username;
            this.visible = true;
        },
        onCancel(){
            this.visible = false;
            this.form = this.$inertia.form({
                id_aplicacion_usuario: 0,
                name: "",
                username: "",
            });
        },
        submit() {
            // Validar campos requeridos
            if (!this.form.name || !this.form.username) {
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Campos requeridos',
                    detail: 'Por favor, complete todos los campos.',
                    life: 3000
                });
                return;
            }

            // Si pasa la validación, continúa
            if (this.form.id_aplicacion_usuario == 0) {
                this.create();
            } else {
                this.update();
            }
        },
        async create() {
            this.mensaje = "Registrando datos espere ...";
            this.isLoadingForm = true;
            self = this;
            await this.$inertia.post(
                rutaBase + "/create/usuarios",
                this.form,
                {
                    onFinish: async () => {
                        await this.getData();
                        this.mensaje = "";
                        this.isLoadingForm = false;
                        this.visible = false;
                        this.form = this.$inertia.form({
                            id_aplicacion_usuario: 0,
                            name: "",
                            username: "",
                        });
                        this.$toast.add({
                            severity: "success",
                            position: "top-right",
                            summary: "Notificación",
                            detail: mensajes.MENSAJE_EXITO,
                            life: 3000,
                        });
                    },
                }
            );
        },
        async update() {
            this.mensaje = "Actualizando datos espere ...";
            this.isLoadingForm = true;
            self = this;
            await this.$inertia.put(
                rutaBase + "/update/usuarios",
                this.form,
                {
                    onFinish: async () => {
                        await this.getData();
                        this.mensaje = "";
                        this.form = this.$inertia.form({
                            id_aplicacion_usuario: 0,
                            name: "",
                            username: "",
                        });
                        this.visible = false;
                        this.isLoadingForm = false;
                        this.$toast.add({
                            severity: "success",
                            position: "top-right",
                            summary: "Notificación",
                            detail: mensajes.MENSAJE_EXITO,
                            life: 3000,
                        });
                    },
                }
            );
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
            this.filtersDropdownData.nombre_rol = [
                ...new Set(
                    this.dataTable.data
                        .filter((s) => s.nombre_rol != "")
                        .map((s) => s.nombre_rol)
                ),
            ].map((o) => {
                return { nombre_rol: o };
            });
        },
        dateFormatChangeApi(data) {
            return dateFormatChange(data);
        },
    },
};
</script>
