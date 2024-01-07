<template>
    <div>
        <i
            class="fas fa-arrow-left mt-3 mb-4 ml-2 arrow-back"
            @click="changeViewDetailChecklist"
        ></i>
    </div>
    <div class="card-body bg-white px-5 py-3 rounded-top">
        <div class="d-flex overflow-auto">
            <div class="row flex-nowrap">
                <div
                    class="card shadow card-checklist mx-3 col"
                    v-for="area in this.checksUsuario"
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
                            v-for="checklist in area.configuracion"
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
    emits: ["changeViewDetailChecklist"],
    mounted() {},
    methods: {
        changeViewDetailChecklist() {
            this.$emit("changeViewDetailChecklist");
        },
    },
};
</script>
<style scoped>
.card-checklist {
    width: 400px;
}
</style>
