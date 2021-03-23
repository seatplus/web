<template>
 <div>
   <teleport to="#head">
     <title>{{ title(object.name) }}</title>
   </teleport>
   <PageHeader :breadcrumbs="breadcrumbs">
     <div class="flex items-center">
       <div class="flex-shrink-0">
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
import Layout from "@/Shared/SidebarLayout/Layout";
import EveImage from "@/Shared/EveImage"
import PageHeader from "@/Shared/Layout/PageHeader";
import ItemLayout from "@/Shared/Components/ItemLayout";

export default {
    name: "ItemDetails",
    components : {ItemLayout, PageHeader, EveImage},
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
                    route: this.$route('character.assets')
                }
            ]
        }
    },
    created() {
        if(this.object.container)
            this.breadcrumbs.push({
                name: this.object.container.name,
                route: this.$route('character.item', this.object.container.item_id)
            })
    },
    methods: {
        getMetricPrefix(numeric_value) {

            const {prefix} = require('metric-prefix')

            return prefix(numeric_value, {precision: 3, unit: 'm³'})
        },
        url(asset) {

            return _.isEmpty(asset.content) ? '#' : this.$route('character.item', asset.item_id)
        },
        hasContent(content) {
            return !_.isEmpty(content)
        }
    }
}
</script>

<style scoped>

</style>
