export function setDateNow() {
    const fechaActual = new Date();
    const opcionesFecha = { day: "numeric", month: "long", year: "numeric" };
    return fechaActual.toLocaleDateString(undefined, opcionesFecha);
}

import dayjs from "dayjs";
export function dateFormatChange(data){
    return dayjs(data).format("DD/MM/YYYY");
}
export var rutaBase = window.location.origin;
export var pathName = window.location.pathname;
