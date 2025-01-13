<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <Dialog
        header="Checklist"
        :visible="modalcheckVisible"
        :closable="false"
        :draggable="false"
        pt:mask:class="backdrop-blur-sm"
        maximizable
        modal
        :style="{
            width: '70rem',
        }"
    >
        <div class="grid">
            <div class="col-3">
                <Listbox
                    v-model="selectedArea"
                    :options="checkListArea"
                    optionLabel="descripcion"
                    class="h-full md:w-14rem"
                />
            </div>
            <div class="col-9">
                <div
                    class="flex justify-content-between flex-wrap w-full"
                    v-if="selectedArea.length != 0"
                >
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
                <form @submit.prevent="submit">
                    <div
                        class="w-full contenedor-checklist pl-5 pr-5"
                        v-for="(checkList, index) in this.selectedArea
                            .configuracion"
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
                                                disabled
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
                                >
                                </Button>
                            </div>
                        </div>
                    </div>
                    <div
                        class="flex justify-content-end gap-2 mt-5"
                        v-if="selectedArea.length != 0"
                    >
                        <Button
                            type="button"
                            style="font-size: 0.9rem; height: 30px"
                            label="Cancelar"
                            severity="danger"
                            @click="showModalCheckList"
                        ></Button>
                        <Button
                            type="submit"
                            style="font-size: 0.9rem; height: 30px"
                            label="Guardar"
                            severity="success"
                        ></Button>
                    </div>
                </form>
            </div>
        </div>
    </Dialog>
    <SidebarComentarios
        :sidebarVisible="sidebarVisible"
        @showSidebarComentarios="showSidebarComentarios"
        :idComentario="idComentario"
    />
</template>
<script>
import Preloader from "@/Components/Preloader.vue";
import PrimeVueComponents from "../../../../../js/primevue.js";
import { rutaBase, dateFormatChange } from "../../../../../Utils/utils.js";
import * as mensajes from "../../../../../Utils/message.js";
import SidebarComentarios from "./SidebarComentarios.vue";
export default {
    props: ["modalcheckVisible", "solicitudAreaCheck"],
    emits: ["showModalCheckList"],
    components: {
        ...PrimeVueComponents,
        Preloader,
        SidebarComentarios,
    },
    data() {
        return {
            checkListArea: [],
            selectedArea: "",
            mensaje: "",
            isLoadingForm: false,
            formData: this.$inertia.form({}),
            checklistSolicitud: [],
            sidebarVisible: false,
            idComentario: 0,
        };
    },
    watch: {
        solicitudAreaCheck(newValue, oldValue) {
            if (newValue) {
                this.getChecklist();
            }
        },
        selectedArea(newValue, oldValue) {
            if (newValue) {
                this.setInitialData();
            }
        },
    },
    methods: {
        showModalCheckList() {
            this.$emit("showModalCheckList");
            this.selectedArea = [];
        },
        submit() {
            this.buildFormdata();
            this.editCheck(this.formData);
        },
        async getChecklist() {
            this.mensaje = "Espere mientras se cargan los checklist....";
            this.isLoadingForm = true;

            try {
                const response = await axios.get(
                    rutaBase + "/redirectpage/configuraciones/areas"
                );
                const rawData = response.data;

                // Transformar el objeto en un arreglo
                this.checkListArea = Object.values(rawData).map((item) => ({
                    id: item.id,
                    descripcion: item.descripcion,
                    configuracion: item.configuracion,
                }));

                this.mensaje = "";
            } catch (error) {
                console.error("Error al obtener los checklist:", error);
                this.mensaje = "Error al cargar los checklist.";
                this.checkListArea = [];
            } finally {
                this.isLoadingForm = false;
            }
        },
        async getChecklistSolicitud() {
            this.checkusuariolist = [];
            self = this;
            await axios
                .get(
                    `${rutaBase}/list/CheckAreaColaboradorByIds/${this.selectedArea.id}/${this.solicitudAreaCheck.id}`
                )
                .then(async (response) => {
                    this.checklistSolicitud = response.data;
                });
        },
        async setInitialData() {
            await this.getChecklistSolicitud();
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

        buildFormdata() {
            const checklistData = [];

            for (const checkList of this.selectedArea.configuracion) {
                const id = checkList.id;
                const descripcion = checkList.descripcion;

                // Construir el objeto correspondiente a cada checklist
                const data = {
                    id,
                    aplica: this.formData.aplica || false,
                    [`${descripcion[0]}${id}`]:
                        this.formData[`${descripcion[0]}${id}`] || null,
                    [`${id}`]: this.formData[`${id}`] || null,
                    [`checkarea${descripcion[0]}${id}`]:
                        this.formData[`checkarea${descripcion[0]}${id}`] ||
                        false,
                    [`checklider${descripcion[0]}${id}`]:
                        this.formData[`checklider${descripcion[0]}${id}`] ||
                        false,
                };

                checklistData.push(data);
            }

            // Asignar la lista construida al formData
            this.formData.checklist = checklistData;

            console.log("Checklist construido:", checklistData);
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
