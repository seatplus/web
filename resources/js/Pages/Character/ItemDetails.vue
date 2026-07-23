<template>
  <div>
    <AppHead :app-title="object.name" />
    <PageHeader :breadcrumbs="breadcrumbs">
      <div class="flex items-center">
        <div class="shrink-0">
          <EveImage
            :object="object"
            :size="256"
            tailwind_class="h-12 w-12 rounded-full"
          />
        </div>
        <div class="ml-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ object.name }}
          </h3>
        </div>
      </div>
    </PageHeader>

    <ItemLayout :items="object.content" />
  </div>
</template>

<script setup>
import { ref } from "vue";
import EveImage from "@/Shared/EveImage.vue"
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import ItemLayout from "@/Shared/Components/ItemLayout.vue";
import AppHead from "@/Shared/AppHead.vue";
import { index as assetsRoute, item as itemRoute } from "@/actions/Seatplus/Web/Http/Controllers/Character/AssetsController";

const props = defineProps({
    item: {
        type    : Object,
        required: true
    },
});

const object = _.first(props.item.data)
const breadcrumbs = ref([
    {
        name: 'Character Assets',
        route: assetsRoute.url()
    }
])

if(object.container)
    breadcrumbs.value.push({
        name: object.container.name,
        route: itemRoute.url({character_id: object.owner_id, item_id: object.container.item_id})
    })
</script>

<style scoped>

</style>
