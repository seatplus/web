<template>
    <div class="overflow-y-auto">
        <ul class="divide-y divide-gray-200 max-h-96" >
            <CharacterComplianceElement v-for="character of characters" :key="character.character.character_id" :character="character" />
            <infinite-loading :identifier="infiniteId" @infinite="loadEntries" spinner="waveDots" >
                <div slot="no-more">all loaded</div>
            </infinite-loading>
        </ul>
    </div>
</template>

<script>
import EveImage from "@/Shared/EveImage";
import axios from "axios";
import CharacterComplianceElement from "./CharacterComplianceElement";
import InfiniteLoading from "vue-infinite-loading";

export default {
    name: "CharacterCompliance",
    components: {CharacterComplianceElement, EveImage, InfiniteLoading},
    props: {
        corporation_id: {
            required: true,
            type: Number
        },
        queryParam: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            characters: [],
            infiniteId: +new Date(),
            page: 1,
        }
    },
    methods: {
        loadEntries($state) {
            const self = this;

            axios.get(this.getRoute, {
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
        async getCharacters(route) {
            return await axios.get(route)
                .then((response) => {

                    let character_data = _.filter([...response.data.data], (data) => !_.isEmpty(data))

                    this.characters.push(...character_data)

                    if(response.data.links.next)
                        this.getInfo(response.data.links.next)
                })
                .catch(error => console.log(error))
        },
    },
    computed: {
        getRoute() {

            let parameter = this.queryParam === '' ? null : this.queryParam;

            return this.$route('character.compliance', {corporation_id: this.corporation_id, filter: parameter})
        }
    },
    watch: {
        queryParam() {
            this.characters = [];
            this.page = 1;
            this.infiniteId += 1
        }
    },
}
</script>

<style scoped>

</style>
