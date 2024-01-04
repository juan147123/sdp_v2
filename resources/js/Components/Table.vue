<template>
    <div class="table-container">
        <DataTable
            :data="data"
            :options="dataTableOptions"
            id="table"
            ref="table"
        >
            <thead>
                <tr>
                    <th
                        class="text-center"
                        v-for="header in headers"
                        :key="header"
                    >
                        {{ header }}
                    </th>
                </tr>
            </thead>
        </DataTable>
    </div>
</template>

<script>
import DataTable from "datatables.net-vue3";
import DataTablesCore from "datatables.net-bs5";
import "datatables.net-select";
import "datatables.net-responsive-dt"


DataTable.use(DataTablesCore);

export default {
    props: ["data", "headers", "dataTableOptions"],
    components: {
        DataTable,
    },
    data() {
        return {};
    },
    mounted() {},
    methods: {},
};
</script>

<style></style>
