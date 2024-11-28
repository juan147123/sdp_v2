<template>
    <Preloader v-if="isLoadingForm" :mensaje="mensaje" />
    <AppLayout>
        <div class="calendar-container flex">
            <div class="form-container m-2 pl-5 pr-5 pt-2 card w-50">
                <div class="flex gap-3">
                    <div class="p-field flex flex-column">
                        <label for="startDate">Fecha de inicio</label>
                        <input
                            id="startDate"
                            type="date"
                            class="p-inputtext p-component"
                            v-model="startDate"
                        />
                    </div>
                    <div class="p-field flex flex-column">
                        <label for="endDate">Fecha de fin</label>
                        <input
                            id="endDate"
                            type="date"
                            class="p-inputtext p-component"
                            v-model="endDate"
                        />
                    </div>
                </div>
                <div class="p-field flex flex-column">
                    <label for="weekNumber">Número de semana</label>
                    <div class="flex gap-2">
                        <input
                            id="weekNumber"
                            type="number"
                            class="p-inputtext p-component"
                            v-model="weekNumber"
                            min="1"
                            max="52"
                        />
                        <button
                            class="p-button p-component p-button-success"
                            @click="validateAndAddEvent"
                        >
                            Registrar
                        </button>
                    </div>
                </div>
                <div class="mt-3 scrollable-list">
                    <span>Semanas</span>
                    <hr />
                    <div
                        class="mb-3"
                        v-for="event in this.events_month"
                        :key="event.start + event.end"
                    >
                        <div class="flex flex-column">
                            <div>
                                <i class="pi pi-calendar"></i> {{ event.title }}
                            </div>
                            <div class="ml-2">
                                Del
                                {{ this.dateFormatChangeApi(event.start) }} al
                                {{ this.dateFormatChangeApi(event.end) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <FullCalendar
                class="card m-2 custom-calendar w-full"
                :options="calendarOptions"
            />
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import Preloader from "@/Components/Preloader.vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";
import { rutaBase, dateFormatChange } from "../../../Utils/utils.js";
import { setSwal } from "../../../Utils/swal";
import * as mensajes from "../../../Utils/message.js";

export default {
    components: {
        AppLayout,
        Preloader,
        FullCalendar,
    },
    data() {
        return {
            mensaje: "",
            isLoadingForm: false,
            startDate: "",
            endDate: "",
            weekNumber: null,
            visibleYear: 0,
            visibleMonth: 0,
            middleMonth: 0,
            events_month: [],
            calendarOptions: {
                plugins: [dayGridPlugin, interactionPlugin],
                initialView: "dayGridMonth",
                locale: esLocale,
                timeZone: "America/Lima",
                themeSystem: "bootstrap",
                events: [], // Lista de eventos existentes
                datesSet: this.handleDatesSet,
                eventClick: this.handleEventClick,
            },
        };
    },
    mounted() {
        this.getData();
    },
    methods: {
        handleDatesSet({ start, end }) {
            this.visibleYear = start.getFullYear();
            this.visibleMonth = start.getMonth();
            this.middleMonth =
                new Date((start.getTime() + end.getTime()) / 2).getMonth() + 1;

            // Actualizar startDate y endDate con el rango del mes visible
            this.startDate = new Date(
                this.visibleYear,
                this.visibleMonth + 1,
                1
            )
                .toISOString()
                .split("T")[0]; // Primer día del mes
            this.endDate = new Date(this.visibleYear, this.visibleMonth + 2, 0)
                .toISOString()
                .split("T")[0]; // Último día del mes

            // Filtrar eventos para el mes visible

            this.setDataToList();
        },
        validateAndAddEvent() {
            const overlappingEvent = this.calendarOptions.events.find(
                (event) =>
                    new Date(this.startDate) <= new Date(event.end) &&
                    new Date(this.endDate) >= new Date(event.start)
            );

            if (overlappingEvent) {
                this.$toast.add({
                    severity: "warn",
                    position: "top-right",
                    summary: "Notificación",
                    detail: mensajes.MENSAJE_EXISTE_CALENDAR,
                    life: 3000,
                });
                return;
            }
            if (
                this.weekNumber == null ||
                this.startDate == "" ||
                this.endDate == ""
            ) {
                this.$toast.add({
                    severity: "error",
                    position: "top-right",
                    summary: "Notificación",
                    detail: mensajes.DATOS_INCOMPLETOS,
                    life: 3000,
                });
                return;
            }
            var data = {
                title: `Semana N° ${this.weekNumber}`,
                start: this.startDate,
                end: this.endDate,
            };
            this.create(data);
        },
        dateFormatChangeApi(data) {
            return dateFormatChange(data);
        },
        setDataToList() {
            this.events_month = this.calendarOptions.events.filter((event) => {
                const eventStart = new Date(event.start);
                const eventEnd = new Date(event.end);
                const mid =
                    new Date(
                        (eventStart.getTime() + eventEnd.getTime()) / 2
                    ).getMonth() + 1;

                return (
                    eventStart.getFullYear() === this.visibleYear &&
                    mid === this.middleMonth
                );
            });
        },
        async getData() {
            await axios
                .get(rutaBase + "/list/calendar")
                .then(async (response) => {
                    if (response.status == 200) {
                        this.calendarOptions.events = response.data;
                        this.setDataToList();
                    }
                });
        },

        async create(data) {
            await axios
                .post(rutaBase + "/create/calendar", data)
                .then(async (response) => {
                    if (response.status == 200) {
                        await this.getData();
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

        async handleEventClick(info) {
            setSwal({
                value: "deleteCalendar",
                callback: () => {
                    this.deleteEvent(info.event.id);
                },
            });
        },

        // Método que elimina un evento del servidor y de la vista
        async deleteEvent(eventId) {
            await axios
                .delete(`${rutaBase}/delete/calendar/${eventId}`)
                .then((response) => {
                    if (response.status == 200) {
                        this.getData();
                        this.$toast.add({
                            severity: "success",
                            position: "top-right",
                            summary: "Notificación",
                            detail: mensajes.MENSAJE_EXITO,
                            life: 3000,
                        });
                    }
                })
                .catch((error) => {
                    console.error("Error al eliminar el evento:", error);
                });
        },
    },
};
</script>

<style scoped>
.scrollable-list {
    height: 450px;
    /* Altura máxima del listado */
    overflow-y: auto;
    /* Permite el desplazamiento vertical */
    border: 1px solid #ccc;
    /* Borde para delimitar el área */
    padding: 10px;
    background-color: #f9f9f9;
    /* Fondo para destacar */
}
</style>
