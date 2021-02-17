<template>
    <ul class="divide-y divide-gray-200 overflow-y-auto border-t">
        <SelectionEntity v-model="selected_character_ids" :entity="character" v-for="character in characters" :key="character.character_id" />
        <infinite-loading :identifier="infiniteId" @infinite="loadEntries" spinner="waveDots" >
            <div slot="no-more">all loaded</div>
        </infinite-loading>
    </ul>
</template>

<script>
import InfiniteLoading from "vue-infinite-loading";
import SelectionEntity from "./SelectionEntity";

export default {
    name: "CharacterSelection",
    components: {SelectionEntity, InfiniteLoading},
    props: {
        dispatch_transfer_object: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            infiniteId: +new Date(),
            characters: [],
            page: 1,
            initial_ids: [],
            selected_character_ids: []
        }
    },
    methods: {
        loadEntries($state) {
            const self = this;

            axios.get(this.$route('get.affiliated.characters', this.dispatch_transfer_object.permission), {
                params: {
                    page: this.page,
                },
            }).then(response => {

                if(response.data.data.length) {
                    self.page += 1;
                    self.characters.push(...response.data.data);
                    $state.loaded();
                } else {
                    $state.complete();
                }
            });
        },
    },
    computed: {
       changed() {
            return !_.isEqual(this.initial_ids, this.selected_character_ids)
        }
    },
    beforeMount() {

        let character_ids = _.get(this.$route().params, 'character_ids')

        if(!character_ids)
            return

        this.selected_character_ids = _.map(character_ids, (id) => parseInt(id))
        this.initial_ids = _.map(character_ids, (id) => parseInt(id))

    },
    beforeDestroy() {

        if(!this.changed)
            return

        let route = this.$route().current()

        if(_.isEmpty(this.selected_character_ids))
            return this.$inertia.get(this.$route(route))

        this.$inertia.get(this.$route(route, {_query: {character_ids: this.selected_character_ids}}))
    }
}
</script>

<style scoped>

</style>
