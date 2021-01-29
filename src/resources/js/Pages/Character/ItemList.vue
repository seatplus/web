<template>
    <div class="divide-y divide-gray-200">
        <wide-list-element v-for="asset in uniqueItems" :key="asset.item_id" :url="url(asset)" >
            <template v-slot:avatar>
                        <span class="inline-block relative">
                            <EveImage :tailwind_class="'h-12 w-12 rounded-full text-white shadow-solid bg-white'" :object="asset.type" :size="128"/>
                            <span v-if="asset.quantity > 1" class="absolute bottom-0 right-0 inline-flex items-center justify-center h-3 w-3 rounded-full text-white shadow-solid bg-gray-400">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-indigo-200 text-indigo-600">{{ asset.quantity }}</span>
                            </span>
                        </span>
                <EveImage :tailwind_class="'-ml-1 inline-block h-12 w-12 rounded-full text-white shadow-solid'" :object="asset.owner" :size="128" />
            </template>

            <template slot="upper_left">{{asset.name}}</template>

            <template slot="lower_left">
                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>

                <span class="truncate">{{ asset.type.name }}</span>
            </template>

            <template slot="upper_right">{{asset.type.group.name}} <span v-if="!asset.is_singleton" class="text-info">(packaged)</span></template>

            <template slot="lower_right">
                <svg class="flex-shrink-0 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L5 10.274zm10 0l-.818 2.552c.25.112.526.174.818.174.292 0 .569-.062.818-.174L15 10.274z" clip-rule="evenodd"></path>
                </svg>
                {{getMetricPrefix(asset.quantity * asset.type.volume)}}
            </template>

            <template slot="navigation">
                <inertia-link :href="$route('character.item', asset.item_id)" >
                    <svg :class="[{'text-gray-400' : asset.content[0], 'text-transparent' : !asset.content[0]},'h-5 w-5']" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </inertia-link>

            </template>

        </wide-list-element>
    </div>

</template>

<script>
  import WideListElement from "../../Shared/WideListElement"
  import EveImage from "../../Shared/EveImage"
  import { prefix } from 'metric-prefix'

  export default {
    name: "ItemList",
        components: {EveImage, WideListElement},
        props: ['items'],
        methods: {
            getMetricPrefix(numeric_value) {

                return prefix(numeric_value, {precision: 3, unit: 'm³'})
            },
            url(asset) {
                return asset.content[0] ? this.$route('character.item', asset.item_id) : ''
            },
        },
        computed: {
            uniqueItems() {
                return _.uniqBy(this.items, 'item_id')
            }
        }
  }
</script>

<style scoped>

</style>
