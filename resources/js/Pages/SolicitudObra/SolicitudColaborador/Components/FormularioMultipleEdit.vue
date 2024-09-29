<template>
    <Preloader v-if="isLoadingForm == true" :mensaje="mensaje" />
    <Dialog
        header="Editar solicitud"
        :visible="visiblemultiple"
        :closable="false"
        :draggable="false"
        pt:mask:class="backdrop-blur-sm"
        maximizable
        modal
        :style="{
            width: colaboradoresDetalle.length === 1 ? '50rem' : '70rem',
        }"
    >
        <form class="grid" @submit.prevent="submit">
            <div
                :class="colaboradoresDetalle.length == 1 ? 'col-12' : 'col-6'"
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
                                {{ colaborador.nombre_completo }}
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
                                option-value="externalcode"
                                v-model="this.formData['motivo' + index]"
                                placeholder="Seleccione"
                                @change="handleDropdownChange(index, $event)"
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
                        <div>
                            <div class="flex flex-column mb-3">
                                <label for="input1" class="form-label"
                                    >Archivos adjuntos
                                </label>
                                <span
                                    class="text-danger"
                                    style="font-size: 13px"
                                    >Si es necesario corregir un documento,
                                    elimínalo y adjúntalo nuevamente.</span
                                >
                            </div>
                            <div
                                v-for="(
                                    archivo, indexarchivo
                                ) in colaborador.archivos"
                                class="m-1 p-1 border"
                                style="font-size: 12px"
                                :id="index + 'archivo' + indexarchivo"
                            >
                                <div class="">
                                    <div class="flex text-align-center">
                                        <i class="pi pi-file-o"></i>
                                        <strong>{{
                                            this.setOrigen(archivo.origen)
                                        }}</strong>
                                    </div>
                                </div>

                                <div class="ml-2">
                                    <div class="text-left">
                                        <span>{{ archivo.name }}</span>
                                    </div>
                                    <div>
                                        <a
                                            :href="this.ruta + archivo.path"
                                            target="_blank"
                                        >
                                            <i
                                                class="pi pi-download text-success mr-1"
                                            >
                                            </i>
                                        </a>
                                        <i
                                            class="pi pi-trash text-danger"
                                            style="cursor: pointer"
                                            @click="
                                                deleteData(
                                                    index +
                                                        'archivo' +
                                                        indexarchivo,
                                                    archivo.id
                                                )
                                            "
                                        ></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mb-3 flex flex-column border p-2"
                            :id="'carta_firmada' + index"
                        >
                            <label for="input1" class="form-label"
                                >Carta firmada o comprobante de envio por correo
                                certificado</label
                            >
                            <input
                                type="file"
                                @change="
                                    handleFileChange(
                                        $event,
                                        index,
                                        'carta_firmada'
                                    )
                                "
                            />
                        </div>
                        <div
                            class="mb-3 flex flex-column border p-2"
                            :id="'cese_dt' + index"
                        >
                            <label for="input1" class="form-label"
                                >CESE DT</label
                            >
                            <input
                                type="file"
                                @change="
                                    handleFileChange($event, index, 'cese_dt')
                                "
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
                                type="file"
                                @change="
                                    handleFileChange($event, index, 'cese_afc')
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
                                >Boleta o comprobante de gastos funebres</label
                            >
                            <input
                                type="file"
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
                                >Información bancaria del beneficiario</label
                            >
                            <input
                                type="file"
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
import { rutaBase, pathName } from "../../../../../Utils/utils.js";
import PrimeVueComponents from "../../../../primevue.js";
import * as mensajes from "../../../../../Utils/message.js";
import { setSwal } from "../../../../../Utils/swal.js";
export default {
    props: ["terminos", "colaboradoresDetalle", "visiblemultiple"],
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
            ruta: rutaBase,
        };
    },
    mounted() {},
    watch: {
        visiblemultiple: function (newValue, oldValue) {
            if (newValue == 1) {
                console.log(1);
                this.armarVModel();
            }
        },
    },
    methods: {
        armarVModel() {
            this.colaboradoresDetalle.forEach((colaborador, index) => {
                const keys = Object.keys(colaborador);
                keys.forEach((key) => {
                    this.formData["id" + index] = colaborador.id;
                    this.formData["id_solicitud" + index] =
                        colaborador.id_solicitud;
                    this.formData["user_id" + index] = colaborador.user_id;
                    this.formData["nombre_completo" + index] =
                        colaborador.nombre_completo;
                    this.formData["motivo" + index] = colaborador.motivo;
                    this.formData["fecha_desvinculacion" + index] = new Date(
                        colaborador.fecha_desvinculacion
                    );
                    this.formData["redireccion" + index] = "";
                    this.formData["rut_empresa" + index] =
                        colaborador.rut_empresa;
                    this.formData["centro_costo" + index] =
                        colaborador.centro_costo;
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

                setTimeout(() => {
                    this.handleDropdownChange(index, null);
                }, 500);
            });
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
            this.$inertia.post(
                rutaBase + "/update/solicitud/multiple",
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
                            life: 3000,
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
            let external_code = this.formData["motivo" + index];

            if (external_code == "03") {
                $("#cert_defuncion" + index).removeClass("d-none");

                $("#boleta_funebre" + index).removeClass("d-none");

                $("#info_bancaria" + index).removeClass("d-none");
            } else {
                $("#cert_defuncion" + index).addClass("d-none");

                $("#boleta_funebre" + index).addClass("d-none");

                $("#info_bancaria" + index).addClass("d-none");
            }

            if (external_code == "24") {
                $("#carta_firmada" + index).addClass("d-none");

                $("#cese_dt" + index).addClass("d-none");

                $("#cese_afc" + index).addClass("d-none");

                $("#convenio_practica" + index).removeClass("d-none");
            } else {
                $("#carta_firmada" + index).removeClass("d-none");

                $("#cese_dt" + index).removeClass("d-none");

                $("#cese_afc" + index).removeClass("d-none");

                $("#convenio_practica" + index).addClass("d-none");
            }

            if (
                external_code == "05" ||
                external_code == "18" ||
                external_code == "19"
            ) {
                $("#aporte_empleador" + index).removeClass("d-none");
            } else {
                $("#aporte_empleador" + index).addClass("d-none");
            }
        },
        handleFileChange(event, index, column) {
            const file = event.target.files[0];
            if (file) {
                this.formData[column + index] = [file];
            }
        },
        setOrigen(origen) {
            var origen_detail = "";
            switch (origen) {
                case "carta_firmada":
                    origen_detail = "Carta firmada";
                    break;
                case "cese_dt":
                    origen_detail = "CESE DT";
                    break;
                case "cese_afc":
                    origen_detail = "CESE AFC";
                    break;
                case "convenio_practica":
                    origen_detail =
                        "Carta firmada o comprobante de envio por correo certificado";
                    break;
                case "aporte_empleador":
                    origen_detail = "Aporte empleador AFC";
                    break;
                case "cert_defuncion":
                    origen_detail = "CERTIFICADO DE DEFUNCIÓN";
                    break;
                case "boleta_funebre":
                    origen_detail = "Boleta o comprobante de gastos funebres";
                    break;
                case "info_bancaria":
                    origen_detail = "Información bancaria del beneficiario";
                    break;
                default:
                    origen_detail = "Descripción no disponible";
                    break;
            }
            return origen_detail;
        },
        deleteData(id, idFile) {
            setSwal({
                value: "deleteForm",
                callback: () => {
                    this.deleteFile(id, idFile);
                    this.getData();
                },
            });
        },
        async deleteFile(id, idFile) {
            let file = document.getElementById(id);
            if (file) {
                try {
                    await axios.put(`${this.ruta}/archivos/delete/${idFile}`);
                    file.remove();
                } catch (error) {
                    console.error("Error al eliminar el archivo:", error);
                }
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
