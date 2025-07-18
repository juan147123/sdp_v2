<template>
    <div
        id="spinner"
        class="show position-fixed translate-middle w-100 vh-100 start-50 d-flex align-items-center justify-content-center"
    >
        <div
            class="d-flex flex-column align-items-center justify-content-center body-preloader"
        >
            <img src="../../../public/images/preloader1.svg" id="loader" />
            <img
                src="../../../public/images/logo_grupo_flesan.png"
                width="220"
                alt="imagen logo flesan"
            />
            <p>{{ this.mensaje }}</p>
        </div>
    </div>
</template>
<script>
export default {
    props: ["mensaje"],
};
</script>
<style>
#spinner {
    background-color: rgba(255, 255, 255, 0.336);
    backdrop-filter: blur(5px);
    z-index: 999999;
    top: 50%;
}
#label-spinner {
    margin-right: 10px;
    color: rgb(1, 1, 139);
}
.spinner-grow {
    width: 10px !important;
    height: 10px !important;
    margin-left: 2px;
}
#loader {
    margin-left: -20px;
}
.body-preloader {
    margin-top: -100px;
}
</style>
