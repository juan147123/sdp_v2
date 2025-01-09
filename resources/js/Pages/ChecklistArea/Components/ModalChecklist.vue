<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <div class="card flex justify-content-center">
        <Dialog header="Checklist" :visible="visible" :closable="false" :draggable="false"
            pt:mask:class="backdrop-blur-sm" maximizable modal :style="{
                width: '40rem',
            }">
            <template #header>
                <div class="flex justify-content-between flex-wrap w-full">
                    <span class="font-bold white-space-nowrap">
                        <label for=""></label>Checklist
                    </span>
                    <Tag :severity="checkListSelected[0].status == 1 ? 'secondary' : 'success'"
                        :value="checkListSelected[0].status == 1 ? 'Pendiente' : 'Cerrado'" class="mr-3"></Tag>
                </div>
            </template>
            <div class="flex flex-column">
                <form @submit.prevent="submit">
                    <div>
                        <label class="mb-2">Este formulario aplica</label>
                        <div class="flex gap-3">
                            <div class="flex align-items-center">
                                <RadioButton v-model="formData.aplica" :value=true />
                                <label class="ml-2">Sí</label>
                            </div>
                            <div class="flex align-items-center">
                                <RadioButton v-model="formData.aplica" :value=false />
                                <label class="ml-2">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="w-full contenedor-checklist" v-for="(checkList, index) in this.checkListArea">
                        <Divider align="left" type="solid">
                            <b>{{ checkList.descripcion }}</b>
                        </Divider>
                        <div class="grid">
                            <div class="col-8">

                                <div class="mb-2">
                                    <label class="mb-2">¿Aplica {{ checkList.descripcion }}?</label>
                                    <div class="flex gap-3">
                                        <div class="flex align-items-center">
                                            <RadioButton v-model="formData[checkList.id]" :value=true />
                                            <label class="ml-2">Sí</label>
                                        </div>
                                        <div class="flex align-items-center">
                                            <RadioButton v-model="formData[checkList.id]" :value=false />
                                            <label class="ml-2">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 flex flex-column">
                                    <label class="mb-2">Indique un monto</label>
                                    <InputText placeholder=""
                                        v-model="formData[checkList.descripcion[0] + checkList.id]"
                                        style="font-size: 0.9rem; height: 30px" :disabled="!formData[checkList.id]" />
                                </div>
                            </div>
                            <div class="col-1">
                                <Divider layout="vertical" class="hidden md:flex"><b></b></Divider>
                            </div>
                            <div class="col-3 flex align-items-center justify-content-center">
                                <Button type="button" style="font-size: 0.9rem; height: 30px" icon="pi pi-envelope"
                                    label="Comentarios" severity="info" @click="showSidebarComentarios(checkList)">
                                </Button>
                            </div>

                        </div>
                    </div>
                    <div class="flex justify-content-end gap-2 mt-5">
                        <Button type="button" style="font-size: 0.9rem; height: 30px" label="Cancelar" severity="danger"
                            @click="showModalCheckList"></Button>
                        <Button type="submit" style="font-size: 0.9rem; height: 30px" label="Editar"
                            severity="info"></Button>
                        <Button type="submit" style="font-size: 0.9rem; height: 30px" label="Guardar"
                            severity="success"></Button>
                    </div>
                </form>
            </div>
        </Dialog>
        <SidebarComentarios :sidebarVisible="sidebarVisible" @showSidebarComentarios="showSidebarComentarios" :idComentario="idComentario"/>
    </div>
</template>

<script>
import { ref } from "vue";
import PrimeVueComponents from "../../../../js/primevue.js";
import { rutaBase, dateFormatChange } from "../../../../Utils/utils.js";
import Preloader from "@/Components/Preloader.vue";
import SidebarComentarios from "./SidebarComentarios.vue";

export default {

    props: ['visible', 'checkListSelected'],
    emits: ['showModalCheckList'],
    components: {
        ...PrimeVueComponents,
        Preloader,
        SidebarComentarios
    },
    data() {
        return {
            checked: false,
            formData: this.$inertia.form({
                aplica: null
            }),
            checkListArea: [],
            mensaje: "",
            isLoadingForm: false,
            sidebarVisible: false,
            idComentario: 0
        }
    },
    watch: {
        checkListSelected(newValue, oldValue) {
            if (newValue) {
                this.setInitialData(newValue)
            }
        }
    },
    methods: {
        async setInitialData(newValue) {
            await this.getChecklist()
            this.formData = this.$inertia.form(
                newValue[0]
            )
        },
        showModalCheckList() {
            this.$emit('showModalCheckList');
            this.formData.reset();
        },
        submit() {
            console.log(this.formData)
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
        buildDtoCheck(checkusuario) {
            if (checkusuario != undefined) {
                this.form.id = checkusuario.id;
                this.form.aplica = checkusuario.aplica;
                this.form.area_id = checkusuario.area_id;
                this.form.checklist = checkusuario.checklist;
                this.form.comentarios = checkusuario.comentarios;
                this.form.status = checkusuario.status;
                this.setDataJson(checkusuario.checklist);
            }
        },
        showSidebarComentarios(checkList) {
            this.sidebarVisible = !this.sidebarVisible
            if (checkList) {
                this.idComentario = checkList.descripcion[0] + this.checkListSelected[0].id + this.checkListSelected[0].id_solicitud + checkList.id;

            } else {
                this.idComentario = 0
            }
        }

    },

}
</script>