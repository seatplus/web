<template>
  <div ref="eveImageComponent">
    <img
      v-if="isReady"
      :class="tailwind_class"
      :src="imageUrl"
      :alt="object.name"
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

<script>
import {computed, onMounted, onUnmounted, ref, watch} from "vue";
import route from 'ziggy'

    export default {
        name: "EveImage",
        props: {
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
        },
        setup(props) {
            const isReady = ref(false)
            const resourceVariant = ref(null)
            const eveImageComponent = ref(null)
            const CancelToken = axios.CancelToken;
            let cancel;

            const getImageVariant = async () => {
                if ('character_id' in props.object)
                    return resourceVariant.value = 'portrait';

                if ('corporation_id' in props.object || 'alliance_id' in props.object)
                    return resourceVariant.value = 'logo';

                await axios.get(route('get.resource.variants', {
                    resource_type: resourceType.value,
                    resource_id: resourceId.value
                }), {
                    cancelToken: new CancelToken(function executor(c) {
                        // An executor function receives a cancel function as a parameter
                        cancel = c;
                    })
                }).then(result => {

                    function getVariant() {
                        if(props.bpo && _.has(_.invert(result.data), 'bp'))
                            return 'bp'

                        return result.data[0]
                    }

                    resourceVariant.value = getVariant()
                })
            }

            const resourceSize = computed(() => {

                function isRetina() {
                    return (window.devicePixelRatio > 1 ||	(window.matchMedia && window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches));
                }

                let size = props.size < 32 ? 32 : props.size

                return isRetina() ? size*2 : size;
            })
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
            const imageUrl = computed(() => {
                return `https://images.evetech.net/${resourceType.value}/${resourceId.value}/${resourceVariant.value}?size=${resourceSize.value}&tenant=tranquility`
            })

            watch(resourceVariant, () => {
                if(resourceVariant.value)
                    isReady.value = true
            })

            const observer = new IntersectionObserver(function(entries) {
                if(entries[0].isIntersecting === true) {
                    if(isReady.value || resourceVariant.value)
                        return

                    getImageVariant()
                }
            }, { threshold: [1] });

            onMounted(() => {
                observer.observe(eveImageComponent.value);
            })

            onUnmounted(() => {
                if(_.isFunction(cancel)) {
                    cancel('image unmounted')
                }
                observer.disconnect()

            })

            return {
                imageUrl,
                isReady,
                eveImageComponent
            }

        },
    }
</script>

<style scoped>

</style>
