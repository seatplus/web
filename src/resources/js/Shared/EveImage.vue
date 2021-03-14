<template>
    <img v-if="ready" :class="img_class" :src="image_url" :alt="object.name" />
    <svg v-else :class="img_class" class="text-indigo-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
    </svg>
</template>

<script>
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
        data() {
            return {
                img_url: '',
                img_class: this.tailwind_class,
                resource_variant: null,
                ready: false
            }
        },
        computed: {
            resource_size() {
                let size = this.size < 32 ? 32 : this.size

                return this.isRetina ? size*2 : size;
            },
            isRetina() {
                return (window.devicePixelRatio > 1 ||	(window.matchMedia && window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches));
            },
            resource_id() {

                return _.chain(['type_id', 'character_id', 'corporation_id', 'alliance_id'])
                    .map(resource => _.get(this.object, resource))
                    .filter()
                    .head()
                    .value()
            },
            resource_type() {

                let array = {
                    'character_id': 'characters',
                    'corporation_id': 'corporations',
                    'alliance_id': 'alliances',
                    'type_id': 'types',
                }

                return _.chain(array)
                    .filter( (type, id) => id in this.object )
                    .map((type) => type)
                    .head()
                    .value();
            },
            image_url() {
                return `https://images.evetech.net/${this.resource_type}/${this.resource_id}/${this.resource_variant}?size=${this.resource_size}&tenant=tranquility`
            }
        },
        created() {
            this.getImageVariant();
        },
        methods: {
            async getImageVariant() {

                if ('character_id' in this.object)
                    return this.resource_variant = 'portrait';

                if ('corporation_id' in this.object || 'alliance_id' in this.object)
                    return this.resource_variant = 'logo';

                await axios.get(this.$route('get.resource.variants', {
                    resource_type: this.resource_type,
                    resource_id: this.resource_id
                })).then(result => {

                    if(this.bpo && _.has(_.invert(result.data), 'bp'))
                        return this.resource_variant = 'bp'

                    this.resource_variant = result.data[0]
                })
            },
        },
        watch: {
            resource_variant(newValue) {
                if(newValue)
                    this.ready = true
            }
        }
    }
</script>

<style scoped>

</style>
