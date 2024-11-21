<template>
    <Toast />
    <ConfirmPopup group="headless" autoHide="false">
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="border-round p-3">
                <div class="m-2">
                    <label for="fecha_inicio">Fecha de inicio</label>
                    <input type="date" v-model="fecha_inicio" class="form-control">
                </div>
                <div class="m-2">
                    <label for="fecha_fin">Fecha fin</label>
                    <input type="date" v-model="fecha_fin" class="form-control">
                </div>

                <div class="flex align-items-center gap-2 mt-3">
                    <Button label="Exportar" @click="acceptCallback" severity="success" size="small"></Button>
                    <Button label="Cancelar" @click="rejectCallback" severity="danger" size="small"></Button>
                </div>
            </div>
        </template>
    </ConfirmPopup>
    <div class="card flex justify-content-center">
        <Button @click="requireConfirmation($event)" label="Exportar" icon="pi pi-file-excel" style="
                                                font-size: 0.9rem;
                                                height: 30px;
                                            " severity="success"></Button>
    </div>
</template>

<script>
import PrimeVueComponents from "../../../../../js/primevue.js";
import { rutaBase, dateFormatChange } from "../../../../../Utils/utils.js";

export default {
    components: {
        ...PrimeVueComponents,
    },
    data() {
        return {
            fecha_inicio: this.formatDate(new Date(2019, 0, 1)),
            fecha_fin: this.formatDate(new Date()),
        }
    },

    methods: {
        requireConfirmation(event) {
            this.$confirm.require({
                target: event.currentTarget,
                group: 'headless',
                message: 'Save your current process?',
                accept: () => {
                    this.getData();
                },
                reject: () => {

                }
            });
        },
        formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        async getData() {
            try {
                const response = await axios.get(
                    `${rutaBase}/exportar-solicitudes/${this.fecha_inicio}/${this.fecha_fin}`,
                    { responseType: 'blob' } // Necesario para manejar archivos
                );

                if (response.status === 200) {
                    // Crear un enlace para descargar el archivo
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;

                    // Nombre del archivo
                    link.setAttribute('download', `solicitudes_${this.fecha_inicio}_a_${this.fecha_fin}.xlsx`);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();

                    this.$toast.add({ severity: 'success', summary: 'Confirmed', detail: 'Archivo exportado', life: 3000 });
                } else {
                    this.$toast.add({ severity: 'error', summary: 'Rejected', detail: 'Ocurrió un error', life: 3000 });
                }
            } catch (error) {
                console.error(error);
                this.$toast.add({ severity: 'error', summary: 'Rejected', detail: 'Ocurrió un error', life: 3000 });
            }
        }

    }
};
</script>
