<template>
    <Preloader v-if="isLoadingForm" :mensaje="mensaje" />
    <AppLayout>
        <div class="calendar-container flex">
            <div class="form-container m-2 pl-5 pr-5 pt-2 card w-50">
                <div class="flex gap-3">
                    <div class="p-field flex flex-column">
                        <label for="startDate">Fecha de inicio</label>
                        <input id="startDate" type="date" class="p-inputtext p-component" v-model="startDate" />
                    </div>
                    <div class="p-field flex flex-column">
                        <label for="endDate">Fecha de fin</label>
                        <input id="endDate" type="date" class="p-inputtext p-component" v-model="endDate" />
                    </div>
                </div>
                <div class="p-field flex flex-column">
                    <label for="weekNumber">Número de semana</label>
                    <div class="flex gap-2">
                        <input id="weekNumber" type="number" class="p-inputtext p-component" v-model="weekNumber"
                            min="1" max="52" />
                        <button class="p-button p-component p-button-success" @click="validateAndAddEvent">
                            Validar y Registrar
                        </button>
                    </div>
                </div>
                <div class="mt-3 scrollable-list">
                    <span>Semanas</span>
                    <hr />
                    <div class="mb-3" v-for="event in this.events_month" :key="event.start + event.end">
                        <div class="flex flex-column">
                            <div>
                                <i class="pi pi-calendar"></i> {{ event.title }}
                            </div>
                            <div class="ml-2">
                                Del {{ event.start }} al {{ event.end }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <FullCalendar class="card m-2 custom-calendar w-full" :options="calendarOptions" />
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
            events_month: [],
            calendarOptions: {
                plugins: [dayGridPlugin, interactionPlugin],
                initialView: "dayGridMonth",
                locale: esLocale,
                themeSystem: "bootstrap",
                events: [], // Lista de eventos existentes
                datesSet: this.handleDatesSet,
            },
        };
    },
    methods: {
        getWeekNumber(date) {
            const d = new Date(date);
            const oneJan = new Date(d.getFullYear(), 0, 1);
            const numberOfDays = Math.floor((d - oneJan) / (24 * 60 * 60 * 1000));
            return Math.ceil((d.getDay() + 1 + numberOfDays) / 7);
        },
        handleDatesSet({ start }) {
            const visibleYear = start.getFullYear();
            const visibleMonth = start.getMonth();

            // Filtrar eventos para el mes visible
            this.events_month = this.calendarOptions.events.filter((event) => {
                const eventStart = new Date(event.start);
                const eventEnd = new Date(event.end);

                return (
                    eventStart.getFullYear() === visibleYear &&
                    eventEnd.getFullYear() === visibleYear &&
                    eventStart.getMonth() === visibleMonth &&
                    eventEnd.getMonth() === visibleMonth
                );
            });

            console.log("Eventos del mes visible:", this.events_month);
        },
        validateAndAddEvent() {
            const overlappingEvent = this.calendarOptions.events.find(
                (event) =>
                    new Date(this.startDate) <= new Date(event.end) &&
                    new Date(this.endDate) >= new Date(event.start)
            );

            /*   if (overlappingEvent) {
                  this.mensaje = "Ya existe un evento registrado en este rango de fechas.";
                  return;
              }
   */
            this.calendarOptions.events.push({
                title: `Semana N° ${this.weekNumber}`,
                start: this.startDate,
                end: this.endDate,
            });

            this.mensaje = "Evento registrado correctamente.";
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
