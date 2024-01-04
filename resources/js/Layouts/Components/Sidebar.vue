<template>
    <Preloader v-if="isLoading == true" />
    <div class="contenedor-sidebar d-flex flex-column" id="sidebar-layout">
        <a class="sidebar-toggler flex-shrink-0" @click="toggleEventOpenClose">
            <i class="fa-solid fa-chevron-left" id="icon-toogle"></i>
        </a>
        <div class="head title-sidebar" id="title-sidebar">
            <img
                src="/images/sdp_logo.png"
                id="title1"
                width="180"
                height="40"
            />
            <img
                src="/images/sdp_logo_cabecera.png"
                id="title2"
                class="text-end col-md-4 d-none"
                width="70"
            />
        </div>

        <div class="mx-2 mt-3 user-section">
            <span class="d-flex image-url">
                <img
                    class="rounded-circle"
                    :src="this.$page.props.auth.user.avatar"
                    alt=""
                    style="width: 60px; height: 60px"
                />
                <div class="d-flex flex-column user-props text-center">
                    <!-- <span class="mx-2 user-data">{{
                        this.$page.props.rol.nombre
                    }}</span> -->
                    <span class="mx-2 user-data">{{
                        this.$page.props.auth.user.name
                    }}</span>
                    <span
                        class="mx-2 user-data"
                        v-if="this.$page.props.area != null"
                        >{{ this.$page.props.area[0].descripcion }}</span
                    >
                </div>
            </span>
        </div>
        <hr />
        <nav class="mb-auto">
         <ul class="mcd-menu">
                <div>
                  <!--   <li
                        class="toogle-li"
                        v-if="
                            this.$page.props.rol.id_rol == 82 ||
                            this.$page.props.rol.id_rol == 80
                        "
                    >
                        <a
                            :href="
                                this.$page.props.dwh === 'PE'
                                    ? this.route('colaboradores.peru')
                                    : this.route('colaboradores')
                            "
                            :class="
                                (this.route().current('colaboradores')
                                    ? 'active'
                                    : '') ||
                                (this.route().current('colaboradores.peru')
                                    ? 'active'
                                    : '')
                            "
                            @click="setPreloader()"
                        >
                            <i class="fas fa-users icon-menu"></i>
                            <strong>Colaboradores</strong>
                        </a>
                    </li> -->
                    <li class="toogle-li">
                        <a
                            :href="this.route('redirect.solicitud')"
                            :class="
                                this.route().current('redirect.solicitud')
                                    ? 'active'
                                    : ''
                            "
                            @click="setPreloader()"
                        >
                            <i class="fas fa-file icon-menu"></i>
                            <strong>Solicitudes</strong>
                        </a>
                    </li>

               <!--      <li
                        class="toogle-li"
                        v-if="this.$page.props.rol.id_rol == 79"
                    >
                        <a class="no-href">
                            <i class="fas fa-wrench icon-menu"></i>
                            <strong>Configuraciones</strong>
                        </a>
                        <ul>
                            <li>
                                <a
                                    :href="this.route('configuraciones')"
                                    :class="
                                        this.route().current('configuraciones')
                                            ? 'active'
                                            : ''
                                    "
                                    @click="setPreloader()"
                                    ><i class="fa fa-globe"></i>areas /
                                    checklist</a
                                >
                            </li>
                        </ul>
                    </li> -->
                </div>
               <!--  <div v-else>
                    <li class="toogle-li">
                        <a
                            :href="this.route('solicitud.area')"
                            :class="
                                this.route().current('solicitud.area')
                                    ? 'active'
                                    : ''
                            "
                            @click="setPreloader()" n
                        >
                            <i class="fas fa-file icon-menu"></i>
                            <strong>Solicitud / Checklist</strong>
                        </a>
                    </li>
                </div> -->
            </ul> 
        </nav>
        <div
            class="text-center mt-auto p-5"
            id="button-logout-block"
            @click="logout"
        >
            <a class="btn btn-outline-secondary" role="button">Cerrar sesión</a>
        </div>
        <div
            class="head title-sidebar mt-auto pb-5 d-none"
            id="button-logout"
            @click="logout"
        >
            <h5 class="col-md-7 text-white">Button logout</h5>
            <strong class="text-end col-md-3 button-logout-icon"
                ><i class="fas fa-sign-out-alt"></i
            ></strong>
        </div>
    </div>
</template>
<script>
import { setSwal } from "../../../Utils/swal.js";
import Preloader from "@/Components/Preloader.vue";
export default {
    components: {
        Preloader,
    },
    data() {
        return {
            isLoading: false,
        };
    },
    methods: {
        logout() {
            setSwal({
                value: "logout",
                callback: this.closeSession,
            });
        },
        closeSession() {
            this.$inertia.post(route("logout"));
        },
        setPreloader() {
            this.isLoading = true;
        },
        toggleOpenClass() {
            var content = document.getElementById("contenedor-layout");
            var sidebar = document.getElementById("sidebar-layout");
            var title = document.getElementById("title-sidebar");
            var toggler = document.getElementById("icon-toogle");

            content.classList.toggle("close");
            sidebar.classList.toggle("close");
            title.classList.toggle("close");
            toggler.classList.toggle("icon-toogle-close");
        },
        alignIconMenus() {
            var content = document.getElementById("contenedor-layout");
            var sidebar = document.getElementById("sidebar-layout");

            var iconMenus = Array.from(document.querySelectorAll(".icon-menu"));

            iconMenus.forEach(function (iconMenu) {
                if (
                    content.classList.contains("close") ||
                    sidebar.classList.contains("close")
                ) {
                    iconMenu.style.float = "right";
                } else {
                    iconMenu.style.float = "left";
                }
            });
        },
        changeIconOrder() {
            var content = document.getElementById("contenedor-layout");
            var sidebar = document.getElementById("sidebar-layout");

            var container = document.querySelector(".image-url");
            var image = container.querySelector("img");
            var userProps = container.querySelector(".user-props");

            if (
                content.classList.contains("close") ||
                sidebar.classList.contains("close")
            ) {
                container.appendChild(userProps);
                container.appendChild(image);
                image.style.marginLeft = "auto";
            } else {
                container.appendChild(image);
                container.appendChild(userProps);
                image.style.marginLeft = "";
            }
        },
        toggleEventOpenClose() {
            this.toggleOpenClass();
            this.alignIconMenus();
            this.changeIconOrder();
            this.alignTitleOnClose();
        },
        alignTitleOnClose() {
            var title1 = document.getElementById("title1");
            var title2 = document.getElementById("title2");
            var buttonlogout = document.getElementById("button-logout");
            var buttonLogoutBlock = document.getElementById("button-logout-block");
            title1.classList.toggle("d-none");
            title2.classList.toggle("d-none");
            buttonlogout.classList.toggle("d-none");
            buttonLogoutBlock.classList.toggle("d-none");
        },
    },
};
</script>
<style scoped>
.user-data {
    font-size: 10px;
}
</style>
