<template>
  <div class="space-y-3">
    <PageHeader :page-title="pageTitle" />

    <CompleteInertiaLoaderHelper v-slot="{data}">
      <Card>
        <Line :data="getDate(data)" :options="options" />
      </Card>

    </CompleteInertiaLoaderHelper>


  </div>
</template>

<script setup>

import PageHeader from "@/Shared/Layout/PageHeader.vue";
import CompleteInertiaLoaderHelper from "@/Shared/CompleteInertiaLoaderHelper.vue";
import {groupBy, map} from 'lodash'
import {Line} from 'vue-chartjs'
import {
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  LineElement,
  PointElement,
  TimeScale,
  Tooltip,
  Colors
} from "chart.js";
import Card from "@/Shared/Layout/Cards/Card.vue";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
import relativeTime from "dayjs/plugin/relativeTime";

const pageTitle = 'Server Performance'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Legend,
    TimeScale,
    Tooltip,
    Colors
);

dayjs.extend(duration)
dayjs.extend(relativeTime)

function getDate(data) {

  // sort data by finished_at
  data.sort((a, b) => {
    return new Date(a.finished_at) - new Date(b.finished_at)
  })

  const datasets = []

  // group data by queue_balancing_configuration
  const grouped = groupBy(data, 'queue_balancing_configuration')

  // loop through each group
  for (const [key, value] of Object.entries(grouped)) {

    // map each data item with a new day property
    const mapped = map(value, function (data) {

      return{
        x: data.finished_at,
        y: data.duration
      }
    })

    datasets.push({
      label: key,
      data: mapped,
    })
  }

  const labels = map(data, function (data) {
    return data.finished_at
  })

  return {
    datasets: datasets,
    labels: labels
  }
}

const options = {
  responsive: true,
  maintainAspectRatio: true,

  // Turn off animations and data parsing for performance
  animation: false,

  plugins: {
    decimation: {
      enabled: true,
      algorithm: 'lttb',
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          return context.dataset.label.substring(0,42) + '...: ' + dayjs.duration(context.parsed.y, 's').humanize();
        }
      }
    },
  },
  scales: {
    x: {
      ticks: {
        source: 'auto',
        // Disabled rotation for performance
        maxRotation: 0,
        autoSkip: true,
        display: false,
      }
    },
    y: {
      ticks: {
        callback: function(value) {
          return dayjs.duration(value, 's').humanize()
        }
      }
    }
  }
}



</script>