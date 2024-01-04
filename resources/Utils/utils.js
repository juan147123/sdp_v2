export function setDateNow() {
    const fechaActual = new Date();
    const opcionesFecha = { day: "numeric", month: "long", year: "numeric" };
    return fechaActual.toLocaleDateString(undefined, opcionesFecha);
}

export var rutaBase = window.location.origin;
