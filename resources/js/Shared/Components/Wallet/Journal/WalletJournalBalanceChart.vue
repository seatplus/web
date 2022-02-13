<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityByIdBlock
          :id="id"
          class="flex-grow"
        />
        <div class="flex-none text-right text-sm text-gray-500">
          Balance
        </div>
      </div>
    </template>
    <div class="relative max-h-48 overflow-y-auto">
      <LineChart
        v-if="results.length > 0"
        class="p-2"
        v-bind="lineChartProps"
      />
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import {useLoadCompleteResource} from "@/Functions/useLoadCompleteResource";
import {LineChart, useLineChart } from "vue-chart-3";
import {computed} from "vue";
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

export default {
    name: "WalletJournalBalanceChart",
    components: {
        LineChart,
        EntityByIdBlock,
        CardWithHeader
    },
    props: {
        id: {
            required: true,
            type: Number
        },
        division: {
            required: false,
            type: Object,
            default: () => {}
        }
    },
    setup(props) {

        let route = props.division? 'corporation.balance' : 'character.balance'
        let routeParameters = props.division ? {
            corporation_id: props.id,
            division_id: props.division.division_id
        } : {
            character_id: props.id
        }

        const chartOptions = {
            responsive: true,
                maintainAspectRatio: false,
                legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    type: 'time',
                    distribution: 'linear'
                }],
                    yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return 'ISK ' + value.toLocaleString();
                        }
                    }
                }],
            }
        }

        const {results} = useLoadCompleteResource(route, routeParameters)

        const chartData = computed(() => {
            return {
                labels: _.map(results.value, (result) => result.x),
                datasets: [{
                    label: 'ISK',
                    data: _.map(results.value, (result) => result.y),
                    borderWidth: 3,
                    fill: false,
                    borderColor: '#4f46e5'
                }]
            }
        })

        const {lineChartProps, lineChartRef} = useLineChart({
            chartData, chartOptions, style: {height: '12rem', position: 'relative'}
        })


        return {
            results,
            lineChartProps,
            lineChartRef
        }
    },
}
</script>

<style scoped>

</style>
