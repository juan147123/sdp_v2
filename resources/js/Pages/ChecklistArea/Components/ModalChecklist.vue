<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <div class="card flex justify-content-center">
        <Dialog
            header="Checklist"
            :visible="visible"
            :closable="false"
            :draggable="false"
            pt:mask:class="backdrop-blur-sm"
            maximizable
            modal
            :style="{
                width: '40rem',
            }"
        >
            <template #header>
                <div class="flex justify-content-between flex-wrap w-full">
                    <span class="font-bold white-space-nowrap">
                        <label for=""></label>Checklist
                    </span>
                    <Tag
                        :severity="
                            checklistSolicitud?.status == 1
                                ? 'secondary'
                                : 'success'
                        "
                        :value="
                            checklistSolicitud?.status == 1
                                ? 'Pendiente'
                                : 'Cerrado'
                        "
                        class="mr-3"
                    ></Tag>
                </div>
            </template>
            <div class="flex flex-column">
                <form>
                    <div>
                        <label class="mb-2">Este formulario aplica</label>
                        <div class="flex gap-3 ml-3">
                            <div class="flex align-items-center">
                                <RadioButton
                                    v-model="formData.aplica"
                                    :value="true"
                                    :disabled="
                                        checklistSolicitud?.status == 0
                                    "
                                />
                                <label class="ml-2">Sí</label>
                            </div>
                            <div class="flex align-items-center">
                                <RadioButton
                                    v-model="formData.aplica"
                                    :value="false"
                                    :disabled="
                                        checklistSolicitud?.status == 0
                                    "
                                />
                                <label class="ml-2">No</label>
                            </div>
                        </div>
                    </div>
                    <div
                        class="w-full contenedor-checklist"
                        v-for="(checkList, index) in this.checkListArea"
                    >
                        <Divider align="left" type="solid">
                            <b>{{ checkList.descripcion }}</b>
                        </Divider>
                        <div class="grid">
                            <div class="col-8">
                                <div class="mb-2">
                                    <label class="mb-2"
                                        >¿Aplica
                                        {{ checkList.descripcion }}?</label
                                    >
                                    <div class="flex gap-3 ml-3">
                                        <div class="flex align-items-center">
                                            <RadioButton
                                                v-model="formData[checkList.id]"
                                                :value="true"
                                                :disabled="
                                                    formData.aplica != true ||
                                                    checklistSolicitud?.status ==
                                                        0
                                                "
                                            />
                                            <label class="ml-2">Sí</label>
                                        </div>
                                        <div class="flex align-items-center">
                                            <RadioButton
                                                v-model="formData[checkList.id]"
                                                :value="false"
                                                :disabled="
                                                    formData.aplica != true ||
                                                    checklistSolicitud?.status ==
                                                        0
                                                "
                                            />
                                            <label class="ml-2">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="mb-2 flex flex-column ml-3"
                                    v-if="checkList.input != 0"
                                >
                                    <label class="mb-2">Indique un monto</label>
                                    <InputText
                                        placeholder=""
                                        v-model="
                                            formData[
                                                checkList.descripcion[0] +
                                                    checkList.id
                                            ]
                                        "
                                        style="font-size: 0.9rem; height: 30px"
                                        :disabled="
                                            !formData[checkList.id] ||
                                            formData.aplica != true ||
                                            checklistSolicitud?.status == 0
                                        "
                                    />
                                </div>
                                <div>
                                    <label class="mb-2"
                                        >Marcar si entregó en su
                                        totalidad</label
                                    >
                                    <div class="flex gap-5 ml-3">
                                        <div class="mb-2 flex flex-column">
                                            <label class="mb-2"> Lider</label>
                                            <Checkbox
                                                v-model="
                                                    formData[
                                                        'checklider' +
                                                            checkList
                                                                .descripcion[0] +
                                                            checkList.id
                                                    ]
                                                "
                                                :binary="true"
                                                disabled
                                            />
                                        </div>
                                        <div class="mb-2 flex flex-column">
                                            <label class="mb-2">Área</label>
                                            <Checkbox
                                                v-model="
                                                    formData[
                                                        'checkarea' +
                                                            checkList
                                                                .descripcion[0] +
                                                            checkList.id
                                                    ]
                                                "
                                                :binary="true"
                                                :disabled="
                                                    !formData[checkList.id] ||
                                                    formData.aplica != true ||
                                                    checklistSolicitud?.status ==
                                                        0
                                                "
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1">
                                <Divider
                                    layout="vertical"
                                    class="hidden md:flex"
                                    ><b></b
                                ></Divider>
                            </div>
                            <div
                                class="col-3 flex align-items-center justify-content-center"
                            >
                                <Button
                                    type="button"
                                    style="font-size: 0.9rem; height: 30px"
                                    icon="pi pi-envelope"
                                    label="Comentarios"
                                    severity="info"
                                    @click="showSidebarComentarios(checkList)"
                                    :disabled="formData.aplica != true"
                                >
                                </Button>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-content-end gap-2 mt-5">
                        <Button
                            type="button"
                            style="font-size: 0.9rem; height: 30px"
                            label="Cancelar"
                            severity="danger"
                            @click="showModalCheckList"
                        ></Button>
                        <Button
                            type="button"
                            style="font-size: 0.9rem; height: 30px"
                            label="Editar"
                            severity="info"
                            :disabled="
                                formData.aplica != true ||
                                checklistSolicitud?.status == 0
                            "
                            @click="submit(1)"
                        ></Button>
                        <Button
                            type="button"
                            style="font-size: 0.9rem; height: 30px"
                            label="Guardar y cerrar"
                            severity="success"
                            @click="submit(2)"
                            :disabled="checklistSolicitud?.status == 0"
                        ></Button>
                    </div>
                </form>
            </div>
        </Dialog>
        <SidebarComentarios
            :sidebarVisible="sidebarVisible"
            @showSidebarComentarios="showSidebarComentarios"
            :idComentario="idComentario"
        />
    </div>
</template>

<script>
import { ref } from "vue";
import PrimeVueComponents from "../../../../js/primevue.js";
import { rutaBase, dateFormatChange } from "../../../../Utils/utils.js";
import Preloader from "@/Components/Preloader.vue";
import SidebarComentarios from "./SidebarComentarios.vue";
import * as mensajes from "../../../../Utils/message.js";

export default {
    props: ["visible", "checkListSelected"],
    emits: ["showModalCheckList"],
    components: {
        ...PrimeVueComponents,
        Preloader,
        SidebarComentarios,
    },
    data() {
        return {
            checked: false,
            formData: this.$inertia.form({
                aplica: false,
                checklist: null,
            }),
            checkListArea: [],
            mensaje: "",
            isLoadingForm: false,
            sidebarVisible: false,
            idComentario: 0,
            checklistSolicitud: [],
        };
    },
    watch: {
        checkListSelected(newValue, oldValue) {
            if (newValue) {
                this.getChecklistSolicitud();
                this.setInitialData(newValue);
            }
        },
    },
    methods: {
        async setInitialData(newValue) {
            await this.getChecklist();

            // Asignar datos base a formData
            this.formData = this.$inertia.form(this.checklistSolicitud);

            // Verificar si el campo checklist existe y no está vacío
            if (this.checklistSolicitud?.checklist) {
                try {
                    // Convertir la cadena JSON en un array de objetos
                    const checklistArray = JSON.parse(
                        this.checklistSolicitud.checklist
                    );

                    // Recorrer cada objeto del checklist y agregar claves/valores al formData
                    checklistArray.forEach((item) => {
                        for (const [key, value] of Object.entries(item)) {
                            this.formData[key] = value; // Agregar clave y valor directamente
                        }
                    });
                } catch (error) {
                    console.error(
                        "Error al analizar el JSON del checklist:",
                        error
                    );
                }
            }
        },

        showModalCheckList() {
            this.$emit("showModalCheckList");
            this.formData.reset();
        },
        submit(submitType) {
            this.isLoadingForm = false;
            this.mensaje = "espere mientras se guardan los datos ....";
            this.buildFormdata();
            if (submitType == 1) {
                this.editCheck(this.formData);
            } else {
                this.formData.status = 0;
                this.editAndCloseCheck(this.formData);
            }
        },
        editCheck(form) {
            axios
                .put(
                    rutaBase +
                        "/update/CheckAreaColaborador/" +
                        this.checklistSolicitud.id,
                    form
                )
                .then((response) => {
                    this.isLoadingForm = false;
                    this.mensaje = "";
                    this.showModalCheckList();
                    this.$toast.add({
                        severity: "success",
                        position: "top-right",
                        summary: "Notificación",
                        detail: mensajes.MENSAJE_REGISTRADO,
                        life: 3000,
                    });
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        editAndCloseCheck(form) {
            axios
                .put(
                    rutaBase +
                        "/update/CheckAreaColaborador/" +
                        this.checklistSolicitud.id,
                    form
                )
                .then((response) => {
                    this.isLoadingForm = false;
                    this.mensaje = "";
                    this.showModalCheckList();
                    this.$toast.add({
                        severity: "success",
                        position: "top-right",
                        summary: "Notificación",
                        detail: mensajes.MENSAJE_REGISTRADO,
                        life: 3000,
                    });
                })
                .catch((error) => {
                    console.error(error);
                });
        },

        buildFormdata() {
            const checklistData = [];
            for (const checkList of this.checkListArea) {
                const id = checkList.id;
                const descripcion = checkList.descripcion[0];

                const data = {
                    id,
                    aplica: this.formData.aplica,
                    [`${descripcion}${id}`]:
                        this.formData[`${descripcion}${id}`],
                    [`${id}`]: this.formData[`${id}`],
                    [`checkarea${descripcion[0]}${id}`]:
                        this.formData[`checkarea${descripcion}${id}`] || false,
                };
                checklistData.push(data);
            }
            this.formData.checklist = checklistData;
        },
        async getChecklist() {
            this.mensaje = "espere mientras se cargan los checklist....";
            this.isLoadingForm = true;
            this.checkusuariolist = [];
            self = this;
            await axios
                .get(rutaBase + "/configuraciones/area")
                .then(async (response) => {
                    this.checkListArea = response.data;
                    self.mensaje = "";
                    self.isLoadingForm = false;
                });
        },
        async getChecklistSolicitud() {
            this.checkusuariolist = [];
            self = this;
            await axios
                .get(
                    rutaBase +
                        "/list/CheckAreaColaboradorBiSolicitudArea/" +
                        this.checkListSelected.id
                )
                .then(async (response) => {
                    this.checklistSolicitud = response.data;
                });
        },
        buildDtoCheck(checkusuario) {
            if (checkusuario != undefined) {
                this.form.id = checkusuario.id;
                this.form.aplica = checkusuario.aplica;
                this.form.area_id = checkusuario.area_id;
                this.form.checklist = checkusuario.checklist;
                this.form.comentarios = checkusuario.comentarios;
                this.form.status = checkusuario.status;
            }
        },
        showSidebarComentarios(checkList) {
            this.sidebarVisible = !this.sidebarVisible;
            if (checkList) {
                this.idComentario =
                    checkList.descripcion[0] +
                    this.checklistSolicitud.id +
                    this.checklistSolicitud.id_solicitud +
                    checkList.id;
            } else {
                this.idComentario = 0;
            }
        },
    },
};
</script>
