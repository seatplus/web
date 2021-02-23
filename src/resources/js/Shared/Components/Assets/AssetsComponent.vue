<template>
    <div>
        <div class="space-y-2 sm:space-y-6">
            <LocationComponent
                v-for="location in groupedAssets"
                :location="location"
                :key="location.location_id"
                @open_modal="openManualLocationModal"
                :context="context"
            />
        </div>

        <infinite-loading :identifier="infiniteId" @infinite="loadAssets" spinner="waveDots" force-use-infinite-wrapper=".main.flex-1">
            <div slot="no-more">all assets loaded</div>
        </infinite-loading>
    </div>
</template>

<script>
import LocationComponent from "./LocationComponent";
import InfiniteLoading from "vue-infinite-loading"
export default {
    name: "AssetsComponent",
    components: {LocationComponent, InfiniteLoading},
    props: {
        params: {
            required: true,
            type: Object
        },
        context: {
            required: false,
            type: String,
            default: 'character'
        }
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
        openManualLocationModal(event) {
            this.$emit('openManualLocationModal', event)
        },
        loadAssets($state) {
            const self = this;

            function getParams(params, loading_page) {
                return {
                    character_ids: params.character_ids,
                    page: loading_page,
                    regions: params.regions,
                    search: params.search === "" ? null : params.search,
                    systems: params.systems,
                    withUnknownLocations: params.withUnknownLocations
                };
            }

            let params = getParams(this.params, this.loading_page)

            axios.get(this.$route('load.character.assets'), { params: params })
                .then(response => {
                    if(response.data.data.length) {
                        self.loading_page += 1;
                        self.assets_data.push(...response.data.data);
                        $state.loaded();
                    } else {
                        $state.complete();
                    }
                });
        },
    },
    computed: {
        groupedAssets() {

            return  _.map(_.groupBy(this.assets_data, 'location_id'), (value, prop) => (
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
