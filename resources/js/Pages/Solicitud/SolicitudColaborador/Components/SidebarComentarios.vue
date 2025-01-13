<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <Sidebar :visible="sidebarVisible" position="right" :showCloseIcon="false">
        <template #header>
            <div class="flex align-items-center justify-content-between w-full">
                <span class="font-bold">Comentarios</span>

                <div class="flex gap-1">
                    <i @click="getDatacomentarios" class="pi pi-refresh" style="cursor: pointer;color: green"></i>
                    <i @click="showSidebarComentarios" style="cursor: pointer;color: red" class="pi pi-times"></i>
                </div>
            </div>
        </template>

        <ScrollPanel style="width: 100%; height: 75%">
            <div class="p-2 shadow-2 mb-3" v-for="comentario in this.comentariosList">
                <div class="flex align-items-center text-primary mb-2">
                    <div>
                        {{ comentario.usuario_name }}
                        {{ comentario.usuario_mail }}
                    </div>
                    
                    <Tag :severity="comentario.origen == 'area' ? 'info' : 'success'"
                    :value="comentario.origen" class="mr-3"></Tag>
                </div>
                <p class="text-sm m-0 text-justify">
                    {{ comentario.comentario }}
                </p>
            </div>
        </ScrollPanel>

        <div class="absolute bottom-0 w-full flex  gap-2 pb-5 pr-5">
            <Textarea class="flex-grow-1 p-inputtextarea" rows="3" style="resize: none;" v-model="this.comentario"
                placeholder="Escribe un comentario...">
    </Textarea>
            <Button type="button" @click="create" icon="pi pi-send" class="p-button p-button-rounded"
                severity="success">
            </Button>
        </div>
    </Sidebar>
</template>


<script>
import { ref } from "vue";
import PrimeVueComponents from "../../../../primevue.js";
import { rutaBase, dateFormatChange } from "../../../../../Utils/utils.js";
import * as mensajes from "../../../../../Utils/message.js";
import Preloader from "@/Components/Preloader.vue";
export default {

    props: ['sidebarVisible', "idComentario"],
    emits: ['showSidebarComentarios'],
    components: {
        ...PrimeVueComponents,
        Preloader
    },
    data() {
        return {
            comentario: "",
            comentariosList: [],
            mensaje: "",
            isLoadingForm: false,
        }
    },
    watch: {
        idComentario: function (newValue, oldValue) {
            if (newValue != 0) {
                this.getDatacomentarios();
            }
        },
    },
    methods: {
        showSidebarComentarios() {
            this.$emit("showSidebarComentarios");
        },

        async getDatacomentarios() {
            this.mensaje = "espere mientras se cargan los comentarios....";
            this.isLoadingForm = true;
            self = this;
            await axios
                .get(rutaBase + "/list/comentarios/"+this.idComentario)
                .then(async (response) => {
                    this.comentariosList = response.data;
                    this.mensaje = "";
                    this.isLoadingForm = false;
                });
        },
        async create() {
            const data = this.$inertia.form({
                "comentario": this.comentario,
                "id_solicitud": this.idComentario,
                "origen": "lider",
                "usuario_mail": this.$page.props.auth.user.username,
                "usuario_name": this.$page.props.auth.user.name,
            });


            await axios
                .post(rutaBase + "/create/comentarios", data)
                .then(async (response) => {
                    if (response.status == 200) {
                        this.getDatacomentarios();
                        this.comentario = ""
                        this.$toast.add({
                            severity: "success",
                            position: "top-right",
                            summary: "Notificación",
                            detail: mensajes.MENSAJE_REGISTRADO,
                            life: 3000,
                        });
                    }
                });
        },
    }

}
</script>