<template>
    <ul class="divide-y divide-gray-200" >
        <CharacterComplianceElement v-for="character of characters" :key="character.character.character_id" :character="character" />
    </ul>
</template>

<script>
import EveImage from "@/Shared/EveImage";
import axios from "axios";
import CharacterComplianceElement from "./CharacterComplianceElement";
export default {
    name: "CharacterCompliance",
    components: {CharacterComplianceElement, EveImage},
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
        }
    },
    methods: {
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
            this.getCharacters(this.getRoute)
        }
    },
    created() {
        this.getCharacters(this.getRoute);
    }
}
</script>

<style scoped>

</style>
