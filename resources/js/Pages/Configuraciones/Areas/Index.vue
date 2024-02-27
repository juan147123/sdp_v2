<template>
    <div class="card">
        <div
            class="d-flex card-header justify-content-between align-items-center"
        >
            <h5 class="d-flex align-items-center">
                <i class="fas fa-building m-1"></i>Áreas
            </h5>
            <div class="m-1">
                <button
                    class="btn btn-sm bg-success text-white"
                    data-bs-toggle="modal"
                    data-bs-target="#areaModal"
                    @click="cleanDataArea"
                >
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div
                class="d-flex justify-content-between"
                v-for="area in this.areas"
                :id="'area' + area.id"
            >
                <div
                    class="d-flex align-items-center label"
                    @click="showChecklist(area)"
                >
                    <p class="m-1">{{ area.descripcion }}</p>
                </div>
                <div class="d-flex align-items-center">
                    <i
                        class="fas fa-pencil-alt m-1 action-btns text-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#areaModal"
                        @click="showDataArea(area)"
                    ></i>
                    <i
                        class="fas fa-trash m-1 action-btns text-danger"
                        @click="ondeleteArea(area)"
                    ></i>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="areaModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ this.formArea.id == 0 ? "Registrar" : "Actualizar" }}
                        área
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        id="btnCloseModalArea"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="onSubmitArea">
                        <div class="mb-3">
                            <label for="" class="form-label"
                                >Nombre del área</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                v-model="this.formArea.descripcion"
                                aria-describedby="helpId"
                                placeholder="Ingrese el nombre"
                                required
                            />
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-danger btn-sm-modal"
                                data-bs-dismiss="modal"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary btn-sm-modal"
                            >
                                {{
                                    this.formArea.id == 0
                                        ? "Registrar"
                                        : "Actualizar"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["areas", "checklist", "formArea"],
    emits: [
        "showChecklist",
        "onSubmitArea",
        "showDataArea",
        "cleanDataArea",
        "ondeleteArea",
    ],
    methods: {
        showChecklist(data) {
            this.$emit("showChecklist", data);
        },
        onSubmitArea() {
            this.$emit("onSubmitArea");
        },
        showDataArea(data) {
            this.$emit("showDataArea", data);
        },
        cleanDataArea() {
            this.$emit("cleanDataArea");
        },
        ondeleteArea(data) {
            this.$emit("ondeleteArea", data);
        },
    },
};
</script>
<style scoped>
.card {
    width: 600px;
    padding: 10px;
}
.btn-sm {
    font-size: 9px;
}
.btn-sm-modal {
    font-size: 12px;
}

.fas {
    cursor: pointer;
    font-size: 15px;
}
.label {
    cursor: pointer;
    padding-right: 180px;
}
.selected {
    background-color: rgba(208, 209, 211, 0.74);
}
</style>
