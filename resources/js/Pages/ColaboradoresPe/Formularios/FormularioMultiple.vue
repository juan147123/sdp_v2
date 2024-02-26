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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSolicitudMultiple">
                        Solicitud de desvinculación
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        id="btn-close-solicitud-unica"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="contenedor-multiform">
                            <div class="row text-white text-center mb-2">
                                <div class="col col-md-1 header-table start">
                                    Doc Usuario
                                </div>
                                <div class="col col-md-3 header-table">
                                    Nombres y apellidos
                                </div>
                                <div class="col col-md-2 header-table">
                                    Motivo
                                </div>
                                <div class="col col-md-2 header-table">
                                    Fecha
                                </div>
                                <div class="col col-md-2 header-table">
                                    Correo de redirección
                                </div>
                                <div class="col col-md-2 header-table end">
                                    Documentos
                                </div>
                            </div>
                            <div
                                v-for="(colaborador, index) in this
                                    .colaboradoresDetalle"
                                class="row"
                            >
                                <div
                                    class="col d-flex align-items-center col-md-1"
                                >
                                    <input
                                        class="input-multiple"
                                        type="text"
                                        :name="'userId' + index"
                                        :id="'userId' + index"
                                        :value="
                                            colaborador.user_id ||
                                            colaborador.dni
                                        "
                                        readonly
                                    />
                                </div>
                                <div
                                    class="col col-md-3 d-flex align-items-center"
                                >
                                    <input
                                        type="text"
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
                                <div
                                    class="col col-md-2 d-flex align-items-center"
                                >
                                    <select
                                        class="form-select select-sm form-select-modal"
                                        style="font-size: 10px; width: 100%"
                                        :id="'motivo' + index"
                                        :name="'motivo' + index"
                                    >
                                        <option value="">
                                            Selecciones Motivo
                                        </option>
                                        <option
                                            v-for="termino in this.terminos"
                                            :value="termino.externalcode"
                                        >
                                            {{ termino.name }}
                                        </option>
                                    </select>
                                </div>
                                <div
                                    class="col col-md-2 d-flex align-items-center"
                                >
                                    <input
                                        class="form-control form-control-sm"
                                        :id="'fechaMotivo' + index"
                                        :name="'fechaMotivo' + index"
                                        style="font-size: 12px"
                                        type="date"
                                        @change="createDtoForms"
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
                                <div
                                    class="col col-md-2 d-flex align-items-center"
                                >
                                    <input
                                        class="form-control form-control-sm"
                                        style="font-size: 12px"
                                        type="email"
                                        :name="'redireccion' + index"
                                        :id="'redireccion' + index"
                                    />
                                </div>
                                <div
                                    class="col col-md-2 d-flex align-items-center"
                                >
                                    <input
                                        class="form-control form-control-sm"
                                        type="file"
                                        :id="'archivos' + index"
                                        :name="'archivos' + index"
                                        @change="createDtoForms"
                                        multiple
                                    />
                                </div>
                                <hr style="margin: 5px" />
                            </div>
                            <div class="text-end">
                                <div class="text-end m-3">
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        data-bs-dismiss="modal"
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
                        </div>
                    </div>
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
    emits: ["reloadTable"],
    components: {
        Preloader,
    },
    data() {
        return {
            mensaje: "",
            isLoadingForm: false,
        };
    },
    mounted() {
        $(".form-select-modal").select2({
            dropdownParent: $("#modalSolicitudMultiple"),
            tags: true,
        });
    },
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
            this.isLoadingForm = true;
            this.mensaje =
                "registrando la solicitud, esto demarara según la cantidad y tamaño de los archivos";

            const form = document.getElementById("formSolicitud");
            const formData = new FormData(form);
            var user_id = this.colaboradoresDetalle[0].dni;
            var nombre_completo =
                this.colaboradoresDetalle[0].nombres +
                " " +
                this.colaboradoresDetalle[0].apellido;

            formData.append("user_id", user_id);
            formData.append("nombre_completo", nombre_completo);
            axios
                .post(rutaBase + "/create/solicitud", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    self.reloadTable();
                    this.isLoadingForm = false;
                    this.mensaje = "";
                    $("#btn-close-solicitud-unica").trigger("click");
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
    },
};
</script>
<style scoped>
.modal {
    margin-top: 50px;
    margin-left: 1.5%;
}
.modal-dialog {
    width: 100% !important;
    height: 100%;
    margin: 0;
    padding: 0;
}

.modal-content {
    width: 97vw !important;
    margin: auto !important;
}
h5 {
    font-size: 14px;
}
.modal-footer {
    padding-bottom: 2px;
    padding-top: 15px;
}
i {
    font-size: 12px;
}
.header-table {
    background-color: #4e4e4e;
    color: white;
    border: 1px solid #ffffff;
    text-align: center;
    font-size: 12px;
    padding: 5px;
}
.start {
    border-radius: 10px 0 0 0;
}
.end {
    border-radius: 0 10px 0 0;
}
.form-vacio {
    background-color: #ffcccc !important;
}
.input-multiple {
    font-size: 12px;
    border: none;
    text-align: center;
    background-color: transparent;
    width: 100%;
}
</style>
