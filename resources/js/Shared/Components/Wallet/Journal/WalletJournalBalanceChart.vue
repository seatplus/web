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
      <Line :data="chartData" :options="chartOptions" />
    </div>
  </CardWithHeader>
</template>

<script setup>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import {useLoadCompleteResource} from "@/Functions/useLoadCompleteResource";
import {Line} from 'vue-chartjs'
import {computed} from "vue";
import {CategoryScale, Chart, Colors, LinearScale, LineElement, PointElement, Tooltip} from 'chart.js';

Chart.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Colors
);

const props = defineProps({
  id: {
    required: true,
    type: Number
  },
  division: {
    required: false,
    type: Object,
    default: () => {}
  }
})

let routeName = props.division? 'corporation.balance' : 'character.balance'
let routeParameters = props.division ? {
  corporation_id: props.id,
  division_id: props.division.division_id
} : {
  character_id: props.id
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  animation: false,
  legend: {
    display: false
  },
  scales: {
    x: {
      ticks: {
        source: 'auto',
        autoSkip: true,
      }
    },
    y: {
      ticks: {
        beginAtZero: true,
        callback: function(value) {
          return 'ISK ' + value.toLocaleString();
        }
      }
    },
  }
}

const {results} = useLoadCompleteResource(routeName, routeParameters)

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

</script>


