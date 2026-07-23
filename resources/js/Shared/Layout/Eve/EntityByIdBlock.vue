<template>
  <div 
    ref="entityByIdBlockComponent"
    class="sm:flex"
  >
    <div
      v-if="isReady"
      class="mb-4 shrink-0 sm:mb-0 sm:mr-4 self-center"
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

<script setup>
import {computed, onMounted, onUnmounted, ref} from "vue";
import EveImage from "@/Shared/EveImage.vue"
import { getJson } from "@/Functions/http";
import { getEntityFromId } from "@/actions/Seatplus/Web/Http/Controllers/Shared/HelperController";

const props = defineProps({
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
});

const entityByIdBlockComponent = ref(null)
const isReady = ref(false)
const entity = ref(null)

const getEntity = async () => {
    try {
        const data = await getJson(getEntityFromId.url(props.id))

        // resolve.id returns an empty payload ("") for ids it cannot resolve;
        // only render once we actually have an entity object.
        if (data && typeof data === 'object' && Object.keys(data).length > 0) {
            entity.value = data
            isReady.value = true
        }
    } catch (error) {
        console.log(error)
    }
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

const subText = computed(() => [_.get(entity.value, 'corporation.name'), _.get(entity.value, 'alliance.name')].filter( Boolean ).join(' | '))

const name = computed(() => _.get(entity.value, 'name', 'missing name'))

const image_class = computed(() => `h-${props.imageSize} w-${props.imageSize} rounded-full`)

const name_class = computed(() => `text-${props.nameFontSize} leading-6 font-medium text-gray-900`)

const hasSubtext = computed(() => {
    if(!props.withSubText)
        return false;

    return !!(subText.value);
})

const sub_text_class = computed(() => {
    let size
    switch (props.nameFontSize) {
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
})
</script>

<style scoped>

</style>
