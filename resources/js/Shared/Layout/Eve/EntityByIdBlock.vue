<template>
  <div 
    ref="entityByIdBlockComponent"
    class="sm:flex"
  >
    <div
      v-if="isReady"
      class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4 self-center"
    >
      <EveImage
        :object="entity"
        :size="256"
        :tailwind_class="image_class"
      />
    </div>
    <div v-if="isReady">
      <h3 :class="name_class">
        {{ name }}
      </h3>
      <p
        v-if="hasSubtext"
        :class="sub_text_class"
      >
        {{ subText }}
      </p>
    </div>
  </div>
</template>

<script>
import EveImage from "@/Shared/EveImage"
import axios from "axios";
import {onMounted, onUnmounted, ref} from "vue";
import route from 'ziggy'

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
    setup(props) {
        const entityByIdBlockComponent = ref(null)
        const isReady = ref(false)
        const entity = ref(null)

        const getEntity = async () => {
            await axios.get(route('resolve.id', props.id))
                .then((response) => {

                    entity.value = response.data

                   isReady.value = true
                })
                .catch(error => console.log(error))
        }

        const observer = new IntersectionObserver(function(entries) {
            if(entries[0].isIntersecting === true) {
                if(isReady.value)
                    return

                if(props.id >0) {
                    getEntity()
                }
            }
        }, { threshold: [1] });

        onMounted(() => {

            observer.observe(entityByIdBlockComponent.value);

        })

        onUnmounted(() => {
            observer.disconnect()
        })
        
        return {
            entityByIdBlockComponent,
            entity,
            isReady
        }

    },
    computed: {
        subText() {
            return [_.get(this.entity, 'corporation.name'), _.get(this.entity, 'alliance.name')].filter( Boolean ).join(' | ')
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

            return !!(this.subText);
        },
        sub_text_class() {

            let size = ''
            switch (this.nameFontSize) {
                case 'xs':
                case 'sm':
                case 'text-base':
                    size = 'xs';
                    break;
                case 'lg':
                    size = 'sm';
                    break;
                default:
                    size = 'sm';
            }

            return `text-${size} text-gray-500 truncate`
        }
    }
}
</script>

<style scoped>

</style>
