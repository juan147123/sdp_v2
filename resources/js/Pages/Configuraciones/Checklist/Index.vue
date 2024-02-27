<template>
    <div class="card">
        <div class="d-flex justify-content-between align-items-center">
            <div class="m-1">
                <button
                    type="button"
                    id="btn-checklist"
                    class="btn rotate-btn btn-active"
                    :disabled="this.selected"
                    @click="changeButtonActive"
                >
                    <i class="fas fa-check-circle m-1"></i>Checklist
                </button>
                <button
                    type="button"
                    id="btn-users"
                    class="btn rotate-btn"
                    :disabled="this.selected"
                    @click="changeButtonActive"
                >
                    <i class="fas fa-users m-1"></i>Usuarios
                </button>
            </div>
            <div class="m-1">
                <button
                    class="btn btn-sm-modal bg-primary text-white"
                    @click="reloadChecklist"
                >
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button
                    class="btn btn-sm-modal bg-success text-white"
                    data-bs-toggle="modal"
                    id="btn-add-check"
                    data-bs-target="#checklistModal"
                    @click="cleanDataChecklist"
                    :disabled="this.selected"
                >
                    <i class="fas fa-plus"></i>
                </button>
                <button
                    class="btn btn-sm-modal bg-success text-white d-none"
                    data-bs-toggle="modal"
                    id="btn-add-user"
                    data-bs-target="#checklistUserArea"
                    @click="cleanDataChecklist"
                    :disabled="this.selected"
                >
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" id="card-body-checklist">
            <div class="text-center" v-if="this.checklist.length == 0">
                <p>No se encuentran datos.</p>
            </div>
            <div
                v-else
                class="d-flex justify-content-between data-container"
                v-for="check in checklist"
            >
                <div class="d-flex align-items-center">
                    <span
                        :class="
                            'badge rounded-pill ' +
                            (check.input == 0 ? 'bg-danger' : 'bg-success')
                        "
                        >{{
                            check.input == 0 ? "sin input" : "con input"
                        }}</span
                    >
                    &nbsp;
                    <p class="m-1">{{ check.descripcion }}</p>
                </div>
                <div class="d-flex align-items-center">
                    <i
                        class="fas fa-pencil-alt m-1 action-btns text-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#checklistModal"
                        @click="showDataChecklist(check)"
                    ></i>
                    <i
                        class="fas fa-trash m-1 action-btns text-danger"
                        @click="ondeleteCK(check)"
                    ></i>
                </div>
            </div>
        </div>
        <div class="card-body d-none" id="card-body-users">
            <div class="text-center" v-if="this.users.length == 0">
                <p>No se encuentran datos.</p>
            </div>
            <div
                v-else
                class="d-flex justify-content-between data-container border-bottom m-1"
                v-for="user in users"
            >
                <div class="d-flex flex-column">
                    <p class="m-1">
                        <strong>Nombres y apellidos :</strong> {{ user.name }}
                    </p>
                    <p class="m-1"><strong>Email :</strong> {{ user.email }}</p>
                </div>
                <div class="d-flex align-items-center">
                    <i
                        class="fas fa-pencil-alt m-1 action-btns text-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#checklistUserArea"
                        @click="showDataUser(user)"
                    ></i>
                    <i
                        class="fas fa-trash m-1 action-btns text-danger"
                        @click="ondeleteUser(user)"
                    ></i>
                </div>
            </div>
        </div>
    </div>
    <!-- modales -->
    <div class="modal" tabindex="-1" id="checklistModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{
                            this.formCheckList.id == 0
                                ? "Registrar"
                                : "Actualizar"
                        }}
                        item
                    </h5>
                    <button
                        type="button"
                        id="btnCloseModalChecklist"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="onSubmitCheckList">
                        <div class="mb-3">
                            <label for="" class="form-label"
                                >Nombre del item</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                v-model="this.formCheckList.descripcion"
                                placeholder="Ingrese el nombre"
                                required
                            />
                        </div>
                        <div class="mb-3 form-check">
                            <label>Requiere input de ingreso de datos</label>
                            <input
                                type="checkbox"
                                class="form-check-input"
                                v-model="this.formCheckList.input"
                                checked
                            />
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                data-bs-dismiss="modal"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary btn-sm"
                            >
                                {{
                                    this.formCheckList.id == 0
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
    <div class="modal" tabindex="-1" id="checklistUserArea">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{
                            this.formUserArea.id == 0
                                ? "Registrar"
                                : "Actualizar"
                        }}
                        Usuario
                    </h5>
                    <button
                        type="button"
                        id="btnCloseModalUserArea"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="onSubmitUserArea">
                        <div class="mb-3">
                            <label for="" class="form-label"
                                >Ingrese nombre completo</label
                            >
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                v-model="this.formUserArea.name"
                                placeholder=""
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label"
                                >Ingrese correo electrónico</label
                            >
                            <input
                                type="email"
                                class="form-control form-control-sm"
                                v-model="this.formUserArea.email"
                                placeholder="example@flesan.com.pe"
                                required
                            />
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                data-bs-dismiss="modal"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary btn-sm"
                            >
                                {{
                                    this.formUserArea.id == 0
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
    props: ["checklist", "selected", "formCheckList", "users", "formUserArea"],
    emits: [
        "reloadChecklist",
        "onSubmitCheckList",
        "onSubmitUserArea",
        "showDataChecklist",
        "showDataUser",
        "ondeleteCK",
        "ondeleteUser",
        "cleanDataChecklist",
    ],
    methods: {
        reloadChecklist() {
            this.$emit("reloadChecklist");
        },
        onSubmitCheckList() {
            this.$emit("onSubmitCheckList");
        },
        onSubmitUserArea() {
            this.$emit("onSubmitUserArea");
        },
        showDataChecklist(data) {
            this.$emit("showDataChecklist", data);
        },
        showDataUser(data) {
            this.$emit("showDataUser", data);
        },
        ondeleteCK(data) {
            this.$emit("ondeleteCK", data);
        },
        ondeleteUser(data) {
            this.$emit("ondeleteUser", data);
        },
        cleanDataChecklist() {
            this.$emit("cleanDataChecklist");
        },
        changeButtonActive() {
            $("#btn-checklist").toggleClass("btn-active");
            $("#btn-users").toggleClass("btn-active");
            $("#btn-add-check").toggleClass("d-none");

            $("#card-body-checklist").toggleClass("d-none");
            $("#card-body-users").toggleClass("d-none");
            $("#btn-add-user").toggleClass("d-none");
        },
    },
};
</script>
<style scoped>
.card {
    width: 100%;
    margin-left: 10px;
    padding: 10px;
}
.btn-sm-modal {
    font-size: 9px;
    margin: 0.5px;
}
.fas {
    cursor: pointer;
    font-size: 15px;
}
@media only screen and (min-width: 1367px) {
    .card {
        width: 50%;
    }
}
.rotate-btn {
    margin: 2px !important;
    font-size: 12px;
    background-color: rgb(216, 216, 216);
}
.btn-active {
    background-color: #dc3545;
    color: white;
}
</style>
