<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <!-- Modal -->
    <div
        class="modal fade"
        id="modalSolicitudMultiple"
        tabindex="-1"
        role="dialog"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        aria-labelledby="modalSolicitudMultiple"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Solicitud de desvinculación</h5>
                    <button
                        type="button"
                        class="btn-close"
                        id="btn-close-solicitud-multiple"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <form
                        @submit.prevent="submit"
                        id="formSolicitudMultiple"
                        enctype="multipart/form-data"
                    >
                        <div class="row">
                            <div
                                class="col-md-6"
                                v-for="(colaborador, index) in this
                                    .colaboradoresDetalle"
                            >
                                <div class="card p-3 mt-1 mb-3">
                                    <div class="card-body">
                                        <div class="mb-3 d-flex">
                                            <input
                                                class="input-multiple"
                                                style="
                                                    width: 60px;
                                                    font-weight: bold;
                                                "
                                                type="text"
                                                :name="'userId' + index"
                                                :id="'userId' + index"
                                                :value="
                                                    colaborador.user_id ||
                                                    colaborador.dni
                                                "
                                                readonly
                                            />
                                            /
                                            <input
                                                type="text"
                                                style="font-weight: bold"
                                                class="input-multiple"
                                                :name="'nombreCompleto' + index"
                                                :id="'nombreCompleto' + index"
                                                :value="
                                                    (colaborador.first_name ||
                                                        colaborador.nombres) +
                                                    ' ' +
                                                    (colaborador.last_name ||
                                                        colaborador.apellido)
                                                "
                                            />
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label"
                                                >Motivo de desvinculación</label
                                            >
                                            <select
                                                class="form-select select-sm form-select-modal-multiple"
                                                :id="'motivo' + index"
                                                :name="'motivo' + index"
                                                required
                                            >
                                                <option value="">
                                                    Selecciones Motivo
                                                </option>
                                                <option
                                                    v-for="termino in this
                                                        .terminos"
                                                    :value="
                                                        termino.externalcode
                                                    "
                                                >
                                                    {{ termino.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label"
                                                >Fecha de desvinculación</label
                                            >
                                            <input
                                                class="form-control form-control-sm"
                                                :id="'fechaMotivo' + index"
                                                :name="'fechaMotivo' + index"
                                                style="font-size: 12px"
                                                type="date"
                                                required
                                                :min="
                                                    new Date(
                                                        new Date().getFullYear(),
                                                        new Date().getMonth(),
                                                        1
                                                    )
                                                        .toISOString()
                                                        .split('T')[0]
                                                "
                                            />
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label"
                                                >Correo de redirección</label
                                            >
                                            <input
                                                class="form-control form-control-sm"
                                                style="font-size: 12px"
                                                type="email"
                                                :name="'redireccion' + index"
                                                :id="'redireccion' + index"
                                            />
                                        </div>
                                        <div class="mb-3">
                                            <label
                                                for="filesForm"
                                                class="form-label"
                                                >Adjuntar archivos</label
                                            >
                                            <input
                                                class="form-control form-control-sm"
                                                type="file"
                                                :id="'archivos' + index"
                                                :name="
                                                    'archivos' + index + '[]'
                                                "
                                                multiple
                                                required
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-end m-3">
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    data-bs-dismiss="modal"
                                    @click="onClickCleanDetalleColaborador"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm ml-1"
                                >
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Preloader from "@/Components/Preloader.vue";
import { rutaBase } from "../../../../Utils/utils.js";
import { setSwal } from "../../../../Utils/swal";
export default {
    props: ["terminos", "colaboradoresDetalle"],
    emits: ["reloadTable","onClickCleanDetalleColaborador"],
    components: {
        Preloader,
    },
    data() {
        return {
            mensaje: "",
            isLoadingForm: false,
        };
    },
    mounted() {},
    methods: {
        onClickCleanFormUnico() {
            $("#motivoForm").val("").trigger("change");
            $("#fechaForm")
                .val(new Date().toISOString().slice(0, 10))
                .trigger("change");
            $("#redireccionForm").val("");
            $("#filesForm").val("");
        },
        submit() {
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
                    self.reloadTable();
                    this.isLoadingForm = false;
                    this.mensaje = "";
                    $("#btn-close-solicitud-multiple").trigger("click");
                    self.onClickCleanDetalleColaborador();
                    setSwal({
                        value: "create",
                    });
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        reloadTable() {
            this.$emit("reloadTable");
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
