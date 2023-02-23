<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      {{ pageTitle }}
    </PageHeader>

    <CompleteInertiaLoaderHelper v-slot="{data}">
      {{ getDate(data) }}
    </CompleteInertiaLoaderHelper>


  </div>
</template>

<script setup>

import PageHeader from "@/Shared/Layout/PageHeader.vue";
import CompleteInertiaLoaderHelper from "@/Shared/CompleteInertiaLoaderHelper.vue";
import { map } from 'lodash'
import dayjs from "dayjs";

const pageTitle = 'Server Performance'

function getDate(data) {

  // mpa each data item with a new day property

  return  map(data, function (data) {
    data.day = dayjs(data.finished_at).format('YYYY-MM-DD')
    return data
  })
}

</script>