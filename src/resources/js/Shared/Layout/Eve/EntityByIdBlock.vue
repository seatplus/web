<template>
    <div class="flex items-center">
        <div v-if="ready" class="flex-shrink-0">
            <EveImage :object="entity" :size="256" tailwind_class="h-12 w-12 rounded-full"/>
        </div>
        <div v-if="ready" class="ml-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{  name }}
            </h3>
            <p v-if="entity.corporation || entity.alliance" class="text-sm text-gray-500 truncate">
                {{ corporationName }}  {{ hasAlliance() ? '| ' + allianceName : '' }}
            </p>
        </div>
    </div>
</template>

<script>
import EveImage from "@/Shared/EveImage";
import axios from "axios";
export default {
    name: "EntityByIdBlock",
    components: {EveImage},
    props: {
        id: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            entity: null,
            ready: false
        }
    },
    methods: {
        hasAlliance() {
            return _.has(this.entity, 'alliance')
        },
        getEntity() {
            axios.get(this.$route('resolve.id', this.id))
                .then((response) => {

                    this.entity = response.data

                    this.ready = true
                })
                .catch(error => console.log(error))
        }
    },
    computed: {
        corporationName() {
            return _.get(this.entity, 'corporation.name', '')
        },
        allianceName() {
            return _.get(this.entity, 'alliance.name', '')
        },
        name() {
            return _.get(this.entity, 'name', 'missing name')
        },

    },
    created() {

        this.getEntity()
    }
}
</script>

<style scoped>

</style>
