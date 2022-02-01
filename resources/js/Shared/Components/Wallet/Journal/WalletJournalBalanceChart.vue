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
        :chart-data="chartData"
        :chart-options="chartOptions"
        :style="{height: '12rem', position: 'relative'}"
      />
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "../../../Layout/Cards/CardWithHeader";
import EntityByIdBlock from "../../../Layout/Eve/EntityByIdBlock";
import {useLoadCompleteResource} from "@/Functions/useLoadCompleteResource";
import LineChart from "../../../Charts/LineChart";

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

        return useLoadCompleteResource(route, routeParameters)
    },
    data() {
        return {
            chartOptions: {
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
                            callback: function(value, index, values) {
                                return 'ISK ' + value.toLocaleString();
                            }
                        }
                    }],
                }
            }
        }
    },
    computed: {
        chartData() {

            return {
                labels: _.map(this.results, (result) => result.x),
                datasets: [{
                    label: 'ISK',
                    data: _.map(this.results, (result) => result.y),
                    borderWidth: 3,
                    fill: false,
                    borderColor: '#4f46e5'
                }]
            }
        }
    },
    created() {
        this.infiniteId += 1;
    }
}
</script>

<style scoped>

</style>
