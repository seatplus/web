

<script>
import {Line} from "vue3-chart-v2";
import {defineComponent} from "@vue/runtime-core";

export default defineComponent({
    name: "LineChart",
    extends: Line,
    props: {
        chartData: {
            type: Object,
            required: true
        },
        chartOptions: {
            type: Object,
            required: false
        },
    },
    mounted () {
        this.renderChart(this.chartData, this.chartOptions)
    }
})
</script>