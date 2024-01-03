import * as mensajes from "./message.js";

export function setAlertify(options) {
    const value = options.value;

    switch (value) {
        case "success":
            alertify.success(mensajes.MENSAJE_EXITO);
            break;
        case "error":
            alertify.error(mensajes.MENSAJE_ERROR);
            break;
        case "exist":
            alertify.error(mensajes.MENSAJE_SOLICITUD_EXISTE);
            break;
        default:
            console.log("no message");
            break;
    }
}
