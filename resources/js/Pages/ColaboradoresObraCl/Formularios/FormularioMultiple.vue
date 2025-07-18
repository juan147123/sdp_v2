<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <Dialog
        header="Registrar solicitud"
        :visible="visible"
        :closable="false"
        :draggable="false"
        pt:mask:class="backdrop-blur-sm"
        maximizable
        modal
        :style="{
            width: colaboradoresDetalle.length === 1 ? '50rem' : '70rem',
        }"
    >
        <form @submit.prevent="submit">
            <div
                class="m-2 p-2 flex flex-column border p-2"
                :id="'variable' + 0"
            >
                <label for="input1" class="form-label">Variable</label>
                <input
                    type="file"
                    class="w-50"
                    multiple
                    @change="handleFileChange($event, 0, 'variable')"
                    required
                />
            </div>
            <div class="grid">
                <div
                    :class="
                        colaboradoresDetalle.length == 1 ? 'col-12' : 'col-6'
                    "
                    v-for="(colaborador, index) in this.colaboradoresDetalle"
                >
                    <div class="card" style="width: 100%">
                        <div class="card-body">
                            <div class="mb-3 flex flex-column">
                                <div class="align-items-center">
                                    <i
                                        class="pi pi-user mr-2 ml-1"
                                        style="font-size: 1rem"
                                    ></i>
                                    {{ colaborador.first_name }}
                                    {{ colaborador.last_name }}
                                    ( NP: {{ colaborador.user_id }} )
                                </div>
                            </div>
                            <div class="mb-3 flex flex-column">
                                <label for="input1" class="form-label"
                                    >Motivo de desvinculación</label
                                >
                                <Dropdown
                                    :options="this.terminos"
                                    option-label="name"
                                    filter
                                    :class="`w-full`"
                                    v-model="this.formData['motivo' + index]"
                                    placeholder="Seleccione"
                                    @change="
                                        handleDropdownChange(index, $event)
                                    "
                                    required
                                >
                                    <template #option="slotProps">
                                        <div
                                            class="flex align-items-center dropdown-option"
                                        >
                                            <div>
                                                {{ slotProps.option.name }}
                                            </div>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                            <div class="mb-3 flex flex-column">
                                <label for="input1" class="form-label"
                                    >Fecha a desvincular</label
                                >
                                <Calendar
                                    showIcon
                                    class="w-full"
                                    locale="es"
                                    v-model="
                                        this.formData[
                                            'fecha_desvinculacion' + index
                                        ]
                                    "
                                    dateFormat="dd/mm/yy"
                                    required
                                />
                            </div>
                            <div
                                class="mb-3 flex flex-column border p-2"
                                :id="'carta_firmada' + index"
                            >
                                <label for="input1" class="form-label"
                                    >Carta firmada o comprobante de envio por
                                    correo certificado</label
                                >
                                <input
                                    type="file"
                                    multiple
                                    @change="
                                        handleFileChange(
                                            $event,
                                            index,
                                            'carta_firmada'
                                        )
                                    "
                                    required
                                />
                            </div>
                            <div
                                class="mb-3 flex flex-column border p-2"
                                :id="'cese_dt' + index"
                            >
                                <label for="input1" class="form-label"
                                    >AVISO DT</label
                                >
                                <input
                                    type="file"
                                    multiple
                                    @change="
                                        handleFileChange(
                                            $event,
                                            index,
                                            'cese_dt'
                                        )
                                    "
                                    required
                                />
                            </div>
                            <div
                                class="mb-3 flex flex-column border p-2"
                                :id="'cese_afc' + index"
                            >
                                <label for="input1" class="form-label"
                                    >CESE AFC</label
                                >
                                <input
                                    required
                                    type="file"
                                    multiple
                                    @change="
                                        handleFileChange(
                                            $event,
                                            index,
                                            'cese_afc'
                                        )
                                    "
                                />
                            </div>
                            <!-- ocultos -->
                            <div
                                class="mb-3 flex flex-column border p-2 d-none"
                                :id="'aporte_empleador' + index"
                            >
                                <label for="input1" class="form-label"
                                    >Aporte empleador AFC</label
                                >
                                <input
                                    type="file"
                                    multiple
                                    @change="
                                        handleFileChange(
                                            $event,
                                            index,
                                            'aporte_empleador'
                                        )
                                    "
                                />
                            </div>
                            <div
                                class="mb-3 flex flex-column border p-2 d-none"
                                :id="'cert_defuncion' + index"
                            >
                                <label for="input1" class="form-label"
                                    >CERTIFICADO DE DEFUNCIÓN</label
                                >
                                <input
                                    type="file"
                                    multiple
                                    @change="
                                        handleFileChange(
                                            $event,
                                            index,
                                            'cert_defuncion'
                                        )
                                    "
                                />
                            </div>
                            <div
                                class="mb-3 flex flex-column border p-2 d-none"
                                :id="'boleta_funebre' + index"
                            >
                                <label for="input1" class="form-label"
                                    >Boleta o comprobante de gastos
                                    funebres</label
                                >
                                <input
                                    type="file"
                                    multiple
                                    @change="
                                        handleFileChange(
                                            $event,
                                            index,
                                            'boleta_funebre'
                                        )
                                    "
                                />
                            </div>
                            <div
                                class="mb-3 flex flex-column border p-2 d-none"
                                :id="'info_bancaria' + index"
                            >
                                <label for="input1" class="form-label"
                                    >Información bancaria del
                                    beneficiario</label
                                >
                                <input
                                    type="file"
                                    multiple
                                    @change="
                                        handleFileChange(
                                            $event,
                                            index,
                                            'info_bancaria'
                                        )
                                    "
                                />
                            </div>
                            <div
                                class="mb-3 flex flex-column border p-2 d-none"
                                :id="'convenio_practica' + index"
                            >
                                <label for="input1" class="form-label"
                                    >Convenio de práctica</label
                                >
                                <input
                                    type="file"
                                    multiple
                                    @change="
                                        handleFileChange(
                                            $event,
                                            index,
                                            'convenio_practica'
                                        )
                                    "
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full flex justify-content-end p-2">
                <Button
                    class="h-2rem m-1"
                    label="Cancelar"
                    severity="danger"
                    icon="pi pi-times"
                    @click="showModal"
                />
                <Button
                    class="h-2rem m-1"
                    label="Guardar"
                    severity="success"
                    icon="pi pi-check"
                    type="submit"
                />
            </div>
        </form>
    </Dialog>
</template>
<script>
import Preloader from "@/Components/Preloader.vue";
import { rutaBase, pathName } from "../../../../Utils/utils.js";
import PrimeVueComponents from "../../../../js/primevue.js";
import * as mensajes from "../../../../Utils/message.js";
export default {
    props: ["terminos", "colaboradoresDetalle", "visible"],
    emits: ["onClickClean", "showModal", "getData"],
    components: {
        Preloader,
        ...PrimeVueComponents,
    },
    data() {
        return {
            mensaje: "",
            isLoadingForm: false,
            formData: this.$inertia.form({}),
        };
    },
    mounted() {},
    watch: {
        visible: function (newValue, oldValue) {
            if (newValue == 1) {
                this.armarVModel();
            }
        },
    },
    methods: {
        armarVModel() {
            this.colaboradoresDetalle.forEach((colaborador, index) => {
                const keys = Object.keys(colaborador);
                keys.forEach((key) => {
                    this.formData["user_id" + index] = colaborador.user_id;
                    this.formData["nombre_completo" + index] =
                        colaborador.first_name + " " + colaborador.last_name;
                    this.formData["motivo" + index] = "";
                    this.formData["fecha_desvinculacion" + index] = new Date();
                    this.formData["redireccion" + index] = "";
                    this.formData["rut_empresa" + index] = colaborador.rut;
                    this.formData["centro_costo" + index] =
                        colaborador.centro_costo;
                    this.formData["full_ceco" + index] = colaborador.full_ceco;
                    this.formData["rut_empresa" + index] = colaborador.rut;
                    this.formData["razon_social" + index] =
                        colaborador.razon_social;
                    this.formData["fecha_ingreso" + index] =
                        colaborador.fecha_ingreso;
                    /* archivos */
                    this.formData["carta_firmada" + index] = null;
                    this.formData["cese_dt" + index] = null;
                    this.formData["cese_afc" + index] = null;
                    this.formData["aporte_empleador" + index] = null;
                    this.formData["cert_defuncion" + index] = null;
                    this.formData["boleta_funebre" + index] = null;
                    this.formData["info_bancaria" + index] = null;
                    this.formData["pathname" + index] = pathName;
                });
            });
            this.$inertia.form(this.formData);
        },
        onClickCleanFormUnico() {
            $("#motivoForm").val("").trigger("change");
            $("#fechaForm")
                .val(new Date().toISOString().slice(0, 10))
                .trigger("change");
            $("#redireccionForm").val("");
            $("#filesForm").val("");
        },
        submit() {
            this.isLoadingForm = true;
            this.mensaje =
                "registrando la solicitud, esto demorara según la cantidad y tamaño de los archivos";
            this.formData["obra"] = 1;
            this.$inertia.post(
                rutaBase + "/create/solicitud/multiple",
                this.formData,
                {
                    onFinish: () => {
                        this.showModal();
                        this.getData();
                        this.isLoadingForm = false;
                        this.mensaje = "";
                        this.$toast.add({
                            severity: "success",
                            position: "top-right",
                            summary: "Notificación",
                            detail: mensajes.MENSAJE_EXITO,
                            life: 6000,
                        });
                    },
                }
            );
        },
        onClickClean() {
            this.$emit("onClickClean");
        },
        getData() {
            this.$emit("getData");
        },
        showModal() {
            this.$emit("showModal");
            this.formData = {};
        },
        onClickClean() {
            this.$emit("onClickClean");
        },
        handleDropdownChange(index, value) {
            let external_code = this.formData["motivo" + index].externalcode;
            if (external_code == "03") {
                $("#cert_defuncion" + index).removeClass("d-none");
                $("#cert_defuncion" + index + " input[type='file']").attr(
                    "required",
                    true
                );
                $("#boleta_funebre" + index).removeClass("d-none");
                $("#boleta_funebre" + index + " input[type='file']").attr(
                    "required",
                    true
                );
                $("#info_bancaria" + index).removeClass("d-none");
                $("#info_bancaria" + index + " input[type='file']").attr(
                    "required",
                    true
                );
            } else {
                $("#cert_defuncion" + index).addClass("d-none");
                $("#cert_defuncion" + index + " input[type='file']").attr(
                    "required",
                    false
                );
                $("#boleta_funebre" + index).addClass("d-none");
                $("#boleta_funebre" + index + " input[type='file']").attr(
                    "required",
                    false
                );
                $("#info_bancaria" + index).addClass("d-none");
                $("#info_bancaria" + index + " input[type='file']").attr(
                    "required",
                    false
                );
            }

            if (external_code == "24") {
                $("#carta_firmada" + index).addClass("d-none");
                $("#carta_firmada" + index + " input[type='file']").attr(
                    "required",
                    false
                );

                $("#cese_dt" + index).addClass("d-none");
                $("#cese_dt" + index + " input[type='file']").attr(
                    "required",
                    false
                );

                $("#cese_afc" + index).addClass("d-none");
                $("#cese_afc" + index + " input[type='file']").attr(
                    "required",
                    false
                );

                $("#convenio_practica" + index).removeClass("d-none");
                $("#convenio_practica" + index + " input[type='file']").attr(
                    "required",
                    true
                );
            } else {
                $("#carta_firmada" + index).removeClass("d-none");
                $("#carta_firmada" + index + " input[type='file']").attr(
                    "required",
                    true
                );

                $("#cese_dt" + index).removeClass("d-none");
                $("#cese_dt" + index + " input[type='file']").attr(
                    "required",
                    true
                );

                $("#cese_afc" + index).removeClass("d-none");
                $("#cese_afc" + index + " input[type='file']").attr(
                    "required",
                    true
                );

                $("#convenio_practica" + index).addClass("d-none");
                $("#convenio_practica" + index + " input[type='file']").attr(
                    "required",
                    false
                );
            }

            if (
                external_code == "05" ||
                external_code == "18" ||
                external_code == "19"
            ) {
                $("#aporte_empleador" + index).removeClass("d-none");
                $("#aporte_empleador" + index + " input[type='file']").attr(
                    "required",
                    true
                );
            } else {
                $("#aporte_empleador" + index).addClass("d-none");
                $("#aporte_empleador" + index + " input[type='file']").attr(
                    "required",
                    false
                );
            }
        },
        handleFileChange(event, index, column) {
            console.log('handleFileChange ejecutado');
            const file = event.target.files[0];
            const maxSizeMB = 5;
            const maxSizeBytes = maxSizeMB * 1024 * 1024;  // 5 MB en bytes

            if (file) {
                if (file.size > maxSizeBytes) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Archivo demasiado grande",
                        detail: `El archivo "${file.name}" supera los ${maxSizeMB} MB permitidos.`,
                        life: 6000,
                    });

                    event.target.value = null;
                    return;
                }

                this.formData[column + index] = [file]; // Guardar si es válido
            }
        },
    },
};
</script>
<style scoped>
.input-multiple {
    font-size: 12px;
    border: none;
    background-color: transparent;
    width: 100%;
}
</style>
