<template>
    <Dialog
        header="Registrar solicitud"
        :visible="visible"
        :closable="false"
        modal
    >
        <form class="grid">
            <div
                :class="colaboradoresDetalle.length == 1 ? 'col-12' : 'col-6'"
                v-for="(colaborador, index) in this.colaboradoresDetalle"
            >
                <div class="card" style="width: 100%">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="input1" class="form-label"
                                >Motivo de desvinculación</label
                            >
                            <Dropdown
                                :options="this.terminos"
                                option-label="name"
                                filter
                                :class="`w-full-important`"
                                v-model="this.dropdownValues['motivo' + index]"
                                placeholder="Seleccione"
                            >
                                <template #option="slotProps">
                                    <div
                                        class="flex align-items-center dropdown-option"
                                    >
                                        <div
                                            :style="{
                                                'white-space': 'normal',
                                                'word-wrap': 'break-word',
                                            }"
                                        >
                                            {{ slotProps.option.name }}
                                        </div>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                        <div class="mb-3">
                            <label for="input1" class="form-label"
                                >Email address</label
                            >
                            <InputText
                                type="email"
                                class="form-control"
                                id="input1"
                                aria-describedby="emailHelp"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full flex justify-content-end p-2">
                <Button
                    class="h-2rem m-1"
                    label="Cancelar"
                    severity="danger"
                    icon="pi pi-times"
                    @click="showModal"
                />
                <Button
                    class="h-2rem m-1"
                    label="Guardar"
                    severity="success"
                    icon="pi pi-check"
                    @click="submit"
                />
            </div>
        </form>
    </Dialog>
</template>
<script>
import Preloader from "@/Components/Preloader.vue";
import { rutaBase } from "../../../../Utils/utils.js";
import { setSwal } from "../../../../Utils/swal";
import PrimeVueComponents from "../../../../js/primevue.js";
import * as mensajes from "../../../../Utils/message.js";
export default {
    props: ["terminos", "colaboradoresDetalle", "visible"],
    emits: ["reloadTable", "onClickCleanDetalleColaborador", "showModal"],
    components: {
        Preloader,
        ...PrimeVueComponents,
    },
    data() {
        return {
            mensaje: "",
            isLoadingForm: false,
            dropdownValues: {},
        };
    },
    mounted() {},
    watch: {
        visible: function (newValue, oldValue) {
            if (newValue == 1) {
                this.armarVModel();
            }
        },
    },
    methods: {
        armarVModel() {
            this.colaboradoresDetalle.forEach((colaborador, index) => {
                const keys = Object.keys(colaborador);
                console.log(colaborador);
                keys.forEach((key) => {
                    this.dropdownValues["user_id" + index] =
                        colaborador.user_id;
                    this.dropdownValues["nombre_completo" + index] =
                        colaborador.first_name + " " + colaborador.last_name;
                    this.dropdownValues["motivo" + index] = "";
                    this.dropdownValues["fecha_desvinculacion" + index] = "";
                    this.dropdownValues["redireccion" + index] = "";
                    this.dropdownValues["rut_empresa" + index] =
                        colaborador.rut;
                    this.dropdownValues["centro_costo" + index] =
                        colaborador.centro_costo;
                });
            });
            console.log(this.dropdownValues);
        },
        onClickCleanFormUnico() {
            $("#motivoForm").val("").trigger("change");
            $("#fechaForm")
                .val(new Date().toISOString().slice(0, 10))
                .trigger("change");
            $("#redireccionForm").val("");
            $("#filesForm").val("");
        },
        submit() {
            return "";
            var self = this;
            const form = document.getElementById("formSolicitudMultiple");
            const formData = new FormData(form);

            this.isLoadingForm = true;
            this.mensaje =
                "registrando la solicitud, esto demorara según la cantidad y tamaño de los archivos";

            axios
                .post(rutaBase + "/create/solicitud/multiple", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    this.isLoadingForm = false;
                    this.mensaje = "";
                    $("#btn-close-solicitud-multiple").trigger("click");
                    self.onClickCleanDetalleColaborador();
                    self.reloadTable();
                    this.$toast.add({
                        severity: "success",
                        position: "top-right",
                        summary: "Notificación",
                        detail: mensajes.MENSAJE_EXITO,
                        life: 3000,
                    });
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        reloadTable() {
            this.$emit("reloadTable");
        },
        showModal() {
            this.$emit("showModal");
            this.dropdownValues = {};
        },
        onClickCleanDetalleColaborador() {
            this.$emit("onClickCleanDetalleColaborador");
        },
    },
};
</script>
<style scoped>
.input-multiple {
    font-size: 12px;
    border: none;
    background-color: transparent;
    width: 100%;
}
</style>
