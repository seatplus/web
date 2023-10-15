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

<script>
import EveImage from "@/Shared/EveImage.vue"
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import ItemLayout from "@/Shared/Components/ItemLayout.vue";
import { prefix } from "metric-prefix";
import AppHead from "@/Shared/AppHead.vue";

export default {
    name: "ItemDetails",
    components : {AppHead, ItemLayout, PageHeader, EveImage},
    props: {
        item: {
            type    : Object,
            required: true
        },
    },
    data() {
        return {
            object: _.first(this.item.data),
            breadcrumbs: [
                {
                    name: 'Character Assets',
                    route: route('character.assets')
                }
            ]
        }
    },
    created() {
        if(this.object.container)
            this.breadcrumbs.push({
                name: this.object.container.name,
                route: route('character.item', this.object.container.item_id)
            })
    },
    methods: {
        getMetricPrefix(numeric_value) {

            return prefix(numeric_value, {precision: 3, unit: 'm³'})
        },
        url(asset) {

            return _.isEmpty(asset.content) ? '' : route('character.item', {'item_id': asset.item_id, 'character_id': asset.owner.character_id})
        },
        hasContent(content) {
            return !_.isEmpty(content)
        }
    }
}
</script>

<style scoped>

</style>
