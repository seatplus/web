<template>
  <div class="sm:flex">
    <div
      v-if="ready"
      class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4 self-center"
    >
      <EveImage
        :object="entity"
        :size="256"
        :tailwind_class="image_class"
      />
    </div>
    <div v-if="ready">
      <h3 :class="name_class">
        {{ name }}
      </h3>
      <p
        v-if="hasSubtext"
        class="text-sm text-gray-500 truncate"
      >
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
        },
        withSubText: {
            required: false,
            type: Boolean,
            default: true
        },
        imageSize: {
            required: false,
            default: 12,
            type: Number
        },
        nameFontSize: {
            required: false,
            default: 'lg',
            type: String
        }
    },
    data() {
        return {
            entity: null,
            ready: false
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
        image_class() {
            return `h-${this.imageSize} w-${this.imageSize} rounded-full`
        },
        name_class() {
            return `text-${this.nameFontSize} leading-6 font-medium text-gray-900`
        },
        hasSubtext() {

            if(!this.withSubText)
                return false;

            if(this.entity.corporation || this.entity.alliance)
                return true

            return false
        }
    },
    created() {

        this.getEntity()
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
    }
}
</script>

<style scoped>

</style>
