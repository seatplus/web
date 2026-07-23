<template>
  <div>
    <img
      v-if="isReady"
      :class="tailwind_class"
      :src="imageUrl"
      :alt="object.name"
      loading="lazy"
    >
    <svg
      v-else
      :class="tailwind_class"
      class="text-indigo-600"
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path
        fill-rule="evenodd"
        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
        clip-rule="evenodd"
      />
    </svg>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    object: {
        type: Object,
        required: true
    },
    size: {
        type: Number,
        default: 32
    },
    tailwind_class: {
        required: false,
        default: "h-12 w-12 rounded-full"
    },
    showName: {
        type: Boolean,
        required: false,
        default: false
    },
    bpo: {
        type: Boolean,
        required: false,
        default: false
    }
});

const resourceId = computed(() => {
    return _.chain(['type_id', 'character_id', 'corporation_id', 'alliance_id'])
        .map(resource => _.get(props.object, resource))
        .filter()
        .head()
        .value()
})

const resourceType = computed(() => {
    let array = {
        'character_id': 'characters',
        'corporation_id': 'corporations',
        'alliance_id': 'alliances',
        'type_id': 'types',
    }

    return _.chain(array)
        .filter( (type, id) => id in props.object )
        .map((type) => type)
        .head()
        .value();
})

// Resolved synchronously — no per-image HTTP roundtrip. characters/corps/alliances are
// deterministic; types carry their variation (render/bp/icon) from the backend
// (TypeResource.image_variant, derived from the inventory category), defaulting to icon.
const resourceVariant = computed(() => {
    if (!props.object || typeof props.object !== 'object')
        return null

    if ('character_id' in props.object)
        return 'portrait'

    if ('corporation_id' in props.object || 'alliance_id' in props.object)
        return 'logo'

    if (resourceType.value !== 'types')
        return null

    return props.bpo ? 'bp' : (props.object.image_variant ?? 'icon')
})

const resourceSize = computed(() => {

    function isRetina() {
        return (window.devicePixelRatio > 1 ||	(window.matchMedia && window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches));
    }

    let size = props.size < 32 ? 32 : props.size

    return isRetina() ? size*2 : size;
})

const imageUrl = computed(() => {
    return `https://images.evetech.net/${resourceType.value}/${resourceId.value}/${resourceVariant.value}?size=${resourceSize.value}&tenant=tranquility`
})

const isReady = computed(() => Boolean(resourceType.value && resourceId.value && resourceVariant.value))
</script>

<style scoped>

</style>
