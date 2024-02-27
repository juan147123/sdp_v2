import * as mensajes from "./message.js";
import Swal from "sweetalert2";

export function setSwal(options) {
    const value = options.value;
    const cantidad = options.cantidad;
    var url = options.url;
    var data = options.data;

    switch (value) {
        case "unauthorized":
            Swal.fire({
                icon: "warning",
                title: mensajes.MENSAJE_ERROR,
                text: mensajes.MENSAJE_NO_AUTORIZADO,
                didClose: () => {
                    const nuevaUrl = window.location.origin;
                    window.location.href = nuevaUrl;
                },
            });
            break;
        case "create":
            Swal.fire({
                icon: "success",
                title: mensajes.MENSAJE_REGISTRADO,
                text: mensajes.MENSAJE_EXITO,
            });
            break;
        case "update":
            Swal.fire({
                icon: "success",
                title: mensajes.MENSAJE_ACTUALIZADO,
                text: mensajes.MENSAJE_EXITO,
            });
            break;
        case "delete":
            Swal.fire({
                icon: "success",
                title: mensajes.MENSAJE_ELIMINADO,
                text: mensajes.MENSAJE_EXITO,
            });
            break;
        case "error":
            Swal.fire({
                icon: "warning",
                title: mensajes.MENSAJE_ERROR,
                text: mensajes.MENSAJE_NO_AUTORIZADO,
                didClose: () => {
                    const nuevaUrl = window.location.origin;
                    window.location.href = nuevaUrl;
                },
            });
            break;

        case "logout":
            Swal.fire({
                text: mensajes.MENSAJE_LOGOUT,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "submitFormSolicitud":
            Swal.fire({
                text: "¿Desea genera la solicitud?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, generar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "submitMultiForm":
            Swal.fire({
                text:
                    "Solo " +
                    cantidad +
                    " registro(s) cuentan con datos seleccionados. ¿Desea generar las solicitudes?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, generar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "submitMultiFormAll":
            Swal.fire({
                text: "¿Desea generar las solicitudes para los " + cantidad + " colaboradores?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, generar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;

        case "multioption":
            Swal.fire({
                text: "¿Desea ejecutar la acción?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, ejecutar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "createForm":
            Swal.fire({
                text: "¿Desea generar el registro?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, generar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "updateForm":
            Swal.fire({
                text: "¿Desea actualizar el registro?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, actualizar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "deleteForm":
            Swal.fire({
                text: "¿Desea eliminar el registro?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "checkPendienteEntregado":
            Swal.fire({
                title: "¿Desea registrar?",
                text: "El checklist no esta completo",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Completo",
                cancelButtonText: "Cancelar",
                showDenyButton: true,
                denyButtonText: "Pendiente",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                } else if (result.isDenied) {
                    await options.callbackDenied();
                }
            });
            break;
        case "checkNoAplica":
            Swal.fire({
                title: "¿Desea registrar?",
                text: "El checklist no aplicará al usuario",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, registrar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "checkAplicaLleno":
            Swal.fire({
                title: "¿Desea registrar?",
                text: "No se podrá editar despues de esta acción",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, registrar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "updateStatus":
            Swal.fire({
                text: "¿Desea ejecutar la acción?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, ejecutar",
                cancelButtonText: "Cancelar",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await options.callback();
                }
            });
            break;
        case "updateStatusInput":
            Swal.fire({
                title: "¿Desea ejecutar la acción?",
                text: "Debe ingresar el motivo de la desaprobación",
                icon: "warning",
                input: "textarea",
                inputAttributes: {
                    autocapitalize: "off",
                },
                showCancelButton: true,
                confirmButtonText: "Sí, ejecutar",
                cancelButtonText: "Cancelar",
                showLoaderOnConfirm: true,
                preConfirm: (comentario) => {
                  /*   if (!comentario) {
                        Swal.showValidationMessage("El campo es obligatorio");
                        return false;
                    } */
                    return options.callback(comentario);
                },
                allowOutsideClick: () => !Swal.isLoading(),
            });
            break;

        default:
            console.log("no message");
            break;
    }
}
