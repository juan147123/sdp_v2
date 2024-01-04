<template>
    <div class="card-body bg-white px-5 py-3 rounded-top">
        <div class="d-flex overflow-auto">
            <div class="row flex-nowrap">
                <div
                    class="card shadow card-checklist mx-3 col"
                    v-for="area in this.$page.props.data.checklist"
                >
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            {{ area.descripcion }}
                            <span
                                class="badge text-white"
                                :id="'status-' + area.id"
                            ></span>
                        </div>
                    </div>
                    <div class="card-body body_riesgo">
                        <div
                            class="form-check"
                            v-for="checklist in area.checklist"
                        >
                            <img
                                src=""
                                width="20"
                                class="m-1"
                                :id="'img-' + checklist.id"
                            />
                            <label
                                class="form-check-label m-1"
                                for="flexCheckDefault"
                                :id="'label-' + checklist.id"
                            >
                                {{ checklist.descripcion }}
                            </label>
                        </div>
                        <div style="margin-left: 20px; margin-top: 20px">
                            <label for="">Comentarios:</label>
                            <p
                                :id="'comentarios-' + area.id"
                                style="
                                    border: 1px solid #3342;
                                    padding: 10px;
                                    border-radius: 10px;
                                "
                            ></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["checksUsuario"],
    mounted() {
         this.asignChecks();
    },
    methods: {
        asignChecks() {
            this.checksUsuario.forEach((area) => {
                const checkList = JSON.parse(area.checklist);
                var desc = "";
                var color = "";
                if (area.status == 2) {
                    desc = "pendiente";
                    color = "bg-info";
                }
                if (area.status == 3) {
                    desc = "no aplica";
                    color = "bg-secondary";
                }
                if (area.status == 4) {
                    desc = "completo";
                    color = "bg-success";
                }

                $("#status-" + area.area_id).addClass(color);
                $("#status-" + area.area_id).html(desc);
                checkList.forEach((check) => {
                    if (check.checked == true) {
                        $("#img-" + check.id).attr("src", "/images/check.png");
                    } else {
                        $("#img-" + check.id).attr(
                            "src",
                            "/images/uncheck.png"
                        );
                    }
                });
                $("#comentarios-" + area.area_id).html(area.comentarios);
            });
        },
    },
};
</script>
<style scoped>
.card-checklist {
    width: 400px;
}
</style>
