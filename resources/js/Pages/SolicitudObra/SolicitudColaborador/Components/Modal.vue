<template>
    <Dialog
        header="Archivos"
        :visible="visible"
        :closable="false"
        :draggable="false"
        pt:mask:class="backdrop-blur-sm"
        maximizable
        modal
        :style="{
            width: '50rem',
        }"
    >
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-group" v-for="ruta in this.archivosList">
                        <li class="list-group-item my-1">
                            <strong>
                                {{ ruta.titulo }}
                            </strong>
                            <div
                                class="d-flex justify-content-between align-items-center"
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
                                        class="btn btn-primary btn-sm"
                                        ><i
                                            class="fa-regular fa-folder-open"
                                        ></i
                                    ></a>
                                </div>
                                <div v-else>
                                    <a
                                        :href="ruta.path"
                                        target="_blank"
                                        :download="ruta.name"
                                        class="btn btn-primary btn-sm"
                                        ><i class="fa-solid fa-download"></i
                                    ></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="w-full flex justify-content-end pt-4">
                        <Button
                            class="h-2rem m-1"
                            label="Cerrar"
                            severity="danger"
                            icon="pi pi-times"
                            @click="this.setImagenes"
                        />
                    </div>
                </div>
            </div>
        </div>
    </Dialog>
</template>
<script>
import PrimeVueComponents from "../../../../../js/primevue.js";
export default {
    props: ["archivosList", "visible"],
    components: {
        ...PrimeVueComponents,
    },
    emits: ["setImagenes"],
    methods: {
        setImagenes() {
            this.$emit("setImagenes");
        },
    },
};
</script>
