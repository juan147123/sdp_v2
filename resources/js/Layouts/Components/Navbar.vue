<template>
    <div class="contenedor-navbar">
        <div class="head">
            <a
                class="sidebar-toggler flex-shrink-0"
                @click="toggleEventOpenClose"
            >
                <i class="fa fa-bars"></i>
            </a>
        </div>
    </div>
</template>
<script>
export default {
    methods: {
        toggleOpenClass() {
            var content = document.getElementById("contenedor-layout");
            var sidebar = document.getElementById("sidebar-layout");
            var title = document.getElementById("title-sidebar");

            content.classList.toggle("close");
            sidebar.classList.toggle("close");
            title.classList.toggle("close");
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
<style scoped></style>
