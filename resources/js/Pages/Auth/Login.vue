<template>
    <Toast position="top-left" />
    <div class="w-screen h-screen grid p-0 relative container-login-app">
        <div
            class="col-12 md:col-5 flex p-0 flex-column justify-content-between align-items-center relative"
            id="login-panel"
        >
            <div class="flex justify-content-center z-2 pt-6">
                <div class="w-15rem" id="login-slot-group-image">
                    <img src="/images/logo_grupo_flesan.png" class="w-full" />
                </div>
            </div>
            <div
                class="py-4 border-round w-full sm:w-25rem z-3"
                id="login-slot"
            >
                <div class="row">
                    <img
                        src="/images/sdp_logo.png"
                        alt="grupo_flesan"
                        id="logo-sdp"
                        class="logo"
                    />
                </div>
                <div class="login-container">
                    <div class="row">
                        <Button
                            label="Secondary"
                            severity="secondary"
                            size="small"
                            :disabled="isLoading == true"
                            outlined
                            @click="submit"
                        >
                            <div
                                class="flex justify-content-center align-items-center w-full"
                            >
                                <div>
                                    <img
                                        src="/images/logo-google.svg"
                                        width="30"
                                    />
                                </div>
                                <span class="text-center ml-2 btn-span-login"
                                    >Sign in with Google</span
                                >
                                <div
                                    v-if="isLoading == true"
                                    class="spinner-border text-primary spinner-border-sm spinner-login ml-2"
                                    role="status"
                                ></div>
                            </div>
                        </Button>

                        <div class="btn btn-danger mt-1 btn-recomended btn-sm">
                            <i class="fab fa-chrome"></i> Sistema optimizado
                            para navegador Google Chrome.
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full flex bg-white z-2">
                <img src="/images/cinta_logos.png" class="w-full" />
            </div>

            <div
                class="absolute left-0 right-0 bottom-0 top-0 z-1"
                id="images-gray-squares"
            ></div>
        </div>
        <div class="md:col-7 p-0" id="image-panel">
            <Carousel
                :value="images"
                :numVisible="1"
                :numScroll="1"
                class="h-full"
                circular
                :autoplayInterval="7000"
            >
                <template #item="slotProps">
                    <div class="w-full h-full relative">
                        <div
                            class="w-full h-full bg-cover bg-center"
                            :style="{
                                backgroundImage: `url(${slotProps.data.path})`,
                            }"
                        ></div>

                        <div
                            id="background-image-title"
                            class="absolute right-0 font-opensans-italic flex flex-column pl-6 py-1 pr-4 color-title-background justify-content-center align-items-center"
                            style="bottom: 3rem"
                        >
                            <div
                                class="font-semibold text-2xl"
                                id="image-title-panel"
                            >
                                {{ slotProps.data.title }}
                            </div>
                        </div>
                    </div>
                </template>
            </Carousel>
        </div>
    </div>
</template>

<script>
import PrimeVueComponents from "../../../js/primevue.js";
import { setSwal } from "../../../Utils/swal.js";
import { ImageService } from "../../Services/Login/LoginService";

export default {
    components: {
        ...PrimeVueComponents,
    },
    data() {
        return {
            isLoading: false,
            loadingImages: true,
            images: [],
            autorizado: 0,
        };
    },
    mounted() {
        if (this.$page.props.autorizado) {
            this.autorizado = this.$page.props.autorizado;
        }
        if (this.autorizado == 1) {
            setSwal({ value: "unauthorized" });
            this.autorizado = 0;
        }
        ImageService.getImages().then((response) => {
            this.loadingImages = false;
            this.images = response.data;
        });
    },
    methods: {
        submit() {
            window.location.href = "login/google";
            this.isLoading = true;
        },
    },
};
</script>

<style scoped>
#background-image-title {
    background: url("/images/fondo_texto_foto.png");
    background-repeat: repeat-x;
    background-size: cover;
    background-position-x: left;
}

#images-gray-squares {
    background: url("/images/background.png");
    background-repeat: repeat-x;
    background-size: cover;
    background-position-x: center;
}

.background-text-color {
    background-color: #f41b35;
}

.background-text {
    background: linear-gradient(
        to right,
        #0c0c0cb5,
        #0c0c0c05,
        rgb(0 0 0 / 0%)
    );
}

.color-title-background {
    color: #7b7873;
}

.form-container {
    max-width: 60rem;
}
</style>
