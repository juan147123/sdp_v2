<template>
    <!-- Modal -->
    <div
        class="modal fade"
        id="modalSolicitudUnica"
        tabindex="-1"
        role="dialog"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        aria-labelledby="modalSolicitudUnica"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSolicitudUnica">
                        Solicitud de desvinculación
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form
                            @submit.prevent="submit"
                            id="formSolicitud"
                            enctype="multipart/form-data"
                        >
                            <div class="mb-3 d-flex flex-column">
                                <label for="" class="form-label"
                                    >Motivo de desvinculación</label
                                >
                                <select
                                    class="form-select form-select-sm form-select-modal"
                                    name="motivoForm"
                                    id="motivoForm"
                                    required
                                >
                                    <option selected value="">
                                        Seleccione
                                    </option>
                                    <option
                                        v-for="termino in this.terminos"
                                        :value="termino.externalcode"
                                    >
                                        {{ termino.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label for="" class="form-label"
                                        >Fecha de desvinculación</label
                                    >
                                    <input
                                        type="date"
                                        class="form-control form-control-sm"
                                        name="fechaForm"
                                        id="fechaForm"
                                        :value="
                                            new Date()
                                                .toISOString()
                                                .slice(0, 10)
                                        "
                                        :min="
                                            new Date(
                                                new Date().getFullYear(),
                                                new Date().getMonth(),
                                                1
                                            )
                                                .toISOString()
                                                .split('T')[0]
                                        "
                                        required
                                    />
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label for="" class="form-label"
                                        >Correo de redirección</label
                                    >
                                    <input
                                        type="email"
                                        class="form-control form-control-sm"
                                        name="redireccionForm"
                                        id="redireccionForm"
                                    />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="filesForm" class="form-label"
                                    >Adjuntar archivos</label
                                >
                                <input
                                    class="form-control form-control-sm"
                                    type="file"
                                    id="filesForm"
                                    name="filesForm"
                                    multiple
                                    required
                                />
                            </div>

                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    data-bs-dismiss="modal"
                                    @click="this.onClickCleanFormUnico"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm"
                                >
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["terminos"],
    mounted() {
        $(".form-select-modal").select2({
            dropdownParent: $("#modalSolicitudUnica"),
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
    },
};
</script>
