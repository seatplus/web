<template>
        <!--<b-img-lazy
            v-bind="mainProps"
            :src="getImageUrl()"
            class="img-circle"
            v-b-tooltip.hover :title="this.object.name"
            :alt="this.object.name"
        ></b-img-lazy>-->
    <img class="h-12 w-12 rounded-full" :src="getImageUrl()" :alt="this.object.name" />
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
      }
    },
    data() {
      return {
        mainProps: {
          blank: true,
          blankColor: '#bbb',
          size: this.getSize(),
          left: false
        }
      }
    },
    methods: {
      getSize() {
        let size = this.size < 32 ? 32 : this.size

        return this.isRetina() ? size*2 : size;

      },
      isRetina() {
        return (window.devicePixelRatio > 1 ||	(window.matchMedia && window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches));
      },
      getImageUrl() {
        const {size} = this.mainProps

        return 'https://images.evetech.net/' + this.getImageType() + '/' + this.getId() +'/'+ this.getImageVariant() +'?size=' + size + '&tenant=tranquility'
      },
      getImageType() {

        if('type_id' in this.object)
          return 'types';

        if ('character_id' in this.object)
          return 'characters';

        if ('corporation_id' in this.object)
            return 'corporations';

        if ('alliance_id' in this.object)
            return 'alliances';

        return 'types';
      },
      getImageVariant() {

        if('type_id' in this.object)
          return 'icon';

        if ('character_id' in this.object)
          return 'portrait';

        if ('corporation_id' in this.object || 'alliance_id' in this.object)
          return 'logo';

        return 'icon';
      },
      getId(){

        if('type_id' in this.object)
          return this.object.type_id;

        if('character_id' in this.object)
          return this.object.character_id;

        if('corporation_id' in this.object)
          return this.object.corporation_id;

        if('alliance_id' in this.object)
          return this.object.alliance_id;
      }
    }
  }
</script>

<style scoped>

</style>
