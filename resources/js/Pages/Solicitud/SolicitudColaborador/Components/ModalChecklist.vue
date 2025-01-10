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
            width: '60rem',
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
                                    >¿Aplica {{ checkList.descripcion }}?</label
                                >
                                <div class="flex gap-3 ml-3">
                                    <div class="flex align-items-center">
                                        <RadioButton
                                            v-model="formData[checkList.id]"
                                            :value="true"
                                            :disabled="
                                                formData.aplica != true ||
                                                checklistSolicitud?.status == 0
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
                                                checklistSolicitud?.status == 0
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
                                    >Marcar si entregó en su totalidad</label
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
                                                checklistSolicitud?.status == 0
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-1">
                            <Divider layout="vertical" class="hidden md:flex"
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
            </div>
        </div>
        <div class="flex justify-content-end gap-2 mt-5">
            <Button
                type="button"
                style="font-size: 0.9rem; height: 30px"
                label="Cancelar"
                severity="danger"
            ></Button>
            <Button
                type="button"
                style="font-size: 0.9rem; height: 30px"
                label="Editar"
                severity="info"
            ></Button>
        </div>
    </Dialog>
</template>
<script>
import Preloader from "@/Components/Preloader.vue";
import PrimeVueComponents from "../../../../../js/primevue.js";
import { rutaBase, dateFormatChange } from "../../../../../Utils/utils.js";
import * as mensajes from "../../../../../Utils/message.js";

export default {
    props: ["modalcheckVisible", "solicitudAreaCheck"],
    emits: ["showModalCheckList"],
    components: {
        ...PrimeVueComponents,
        Preloader,
    },
    data() {
        return {
            checkListArea: [],
            selectedArea: "",
            mensaje: "",
            isLoadingForm: false,
            formData: this.$inertia.form({}),
        };
    },
    watch: {
        solicitudAreaCheck(newValue, oldValue) {
            if (newValue) {
                this.getChecklist();
            }
        },
    },
    methods: {
        showModalCheckList() {
            this.$emit("showModalCheckList");
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
    },
};
</script>
