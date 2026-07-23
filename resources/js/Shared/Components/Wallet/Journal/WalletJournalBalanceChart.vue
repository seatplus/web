<template>
  <CardWithHeader>
    <template #header>
      <div class="flex">
        <EntityByIdBlock
          :id="id"
          class="grow"
        />
        <div class="flex-none text-right text-sm text-gray-500">
          Balance
        </div>
      </div>
    </template>
    <div class="relative max-h-48 overflow-y-auto">
      <Line
        :data="chartData"
        :options="chartOptions"
      />
    </div>
  </CardWithHeader>
</template>

<script setup>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock.vue";
import {Line} from 'vue-chartjs'
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";
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

const page = usePage()

// Balance points arrive as a deferred page prop (WalletsController /
// CorporationWalletController): keyed per character, or per corporation+division.
// Replaces the old axios/route fetch — no axios, no Ziggy.
const balanceKey = computed(() => props.division
  ? `balance_${props.id}_${props.division.division_id}`
  : `balance_${props.id}`)

const results = computed(() => page.props[balanceKey.value] ?? [])

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


