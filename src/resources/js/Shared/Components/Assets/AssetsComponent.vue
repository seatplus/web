<template>
    <div>
        <div class="space-y-2 sm:space-y-6">
            <LocationComponent
                v-for="location in groupedAssets"
                :location="location"
                :key="location.location_id"
                :context="context"
            />
        </div>
      <div ref="scrollComponent"></div>
    </div>
</template>

<script>
import LocationComponent from "./LocationComponent";
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader";
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import {useInfinityScrolling} from "@/Functions/useInfinityScrolling";
export default {
    name: "AssetsComponent",
    components: {EntityByIdBlock, CardWithHeader, LocationComponent,
        //InfiniteLoading
    },
    props: {
        parameters: {
            type: Object,
            required: true
        },
        context: {
            required: false,
            type: String,
            default: 'character'
        }
    },
  setup(props) {

    return useInfinityScrolling('load.character.assets', props.parameters)
  },
    data() {
        return {
            infiniteId: +new Date(),
            assets_data: [],
            loading_page: 1,
            openModal: false,
            modal_location_id: 0
        }
    },
    methods: {
    },
    computed: {
        groupedAssets() {

            return  _.map(_.groupBy(this.result, 'location_id'), (value, prop) => (
                {
                    location_id: _.toInteger(prop),
                    location: _.get(_.head(value), 'location.locatable.name'), //value[0].location ? value[0].location.locatable.name : 'Unknown Structure (' + _.toInteger(prop) +')' ,
                    assets: _.map(value, function(asset) {

                        asset.type =  asset.type ?? { type_id: asset.type_id, name: '', group: { name: '' }}
                        asset.type.group = asset.type.group ?? { name: '' }

                        return asset
                    }),
                }
            ))
        },
        selectedCharacterIds() {

            let character_ids = _.get(this.$route().params, 'character_ids')

            if(!character_ids)
                return []

            return  _.map(character_ids, (id) => parseInt(id))
        },
    },
    watch: {
        params: {
            deep: true,
            handler(params) {

                this.assets_data = [];
                this.loading_page = 1;
                this.infiniteId += 1;
            }
        }
    }
}
</script>

<style scoped>

</style>
