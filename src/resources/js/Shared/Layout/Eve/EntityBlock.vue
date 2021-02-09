<template>
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <EveImage :object="entity" :size="256" :tailwind_class="tailwind_class"/>
        </div>
        <div class="ml-4">
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
export default {
    name: "EntityBlock",
    components: {EveImage},
    props: {
        entity: {
            required: true,
            type: Object
        },
        tailwind_class: {
            required: false,
            type: String,
            default: 'h-12 w-12 rounded-full'
        }
    },
    methods: {
        hasAlliance() {
            return _.has(this.entity, 'alliance.name')
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
        }
    }
}
</script>

<style scoped>

</style>
