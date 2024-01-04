<template>
    <div
        class="modal fade"
        id="modalArchivos"
        tabindex="-1"
        aria-labelledby="archivos"
        aria-hidden="true"
        data-bs-backdrop="static"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archivos">Archivos</h5>

                    <button
                        type="button"
                        class="btn-close"
                        id="btn-close-modal-control"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <ul
                                    class="list-group"
                                    v-for="ruta in this.archivosList"
                                >
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center my-1"
                                    >
                                        {{ ruta.name }}
                                        <div
                                            v-if="
                                                ruta.extension == 'jpg' ||
                                                ruta.extension == 'png' ||
                                                ruta.extension == 'pdf' ||
                                                ruta.extension == 'webp'
                                            "
                                        >
                                            <a
                                                :href="ruta.path"
                                                target="_blank"
                                                class="btn btn-primary"
                                                >Abrir archivo</a
                                            >
                                        </div>
                                        <div v-else>
                                            <a
                                                :href="ruta.path"
                                                target="_blank"
                                                :download="ruta.name"
                                                class="btn btn-primary"
                                                >Descargar archivo</a
                                            >
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-danger btn-modal"
                        data-bs-dismiss="modal"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["archivosList"],
};
</script>
