<template>
    <div>
        <b-alert :show="isShown()" variant="info" dismissible>
            <h4 class="alert-heading">Missing Scopes</h4>
            <span>Some characters are missing some scopes on their refresh_token for seatplus to fetch information from esi.</span>

                    <span class="d-flex flex-row" v-for="character in characters">
                        <div class="p-2"><EveImage :object="character" :size="32" /></div>
                        <div class="p-2 align-self-center">{{character.name}}</div>
                        <div class="p-2 align-self-center">{{ getMissingText(character.missing_scopes) }}</div>
                    </span>

        </b-alert>
    </div>
</template>

<script>
import EveImage from "./EveImage"

export default {
  name: "RequiredScopesWarning",
    components: {
      EveImage
    },
    props: {
      scopes: {
          type: Array,
          required: true,
          default: function () {
              return []
          }
      }
    },
    methods: {
      isShown() {
          return ! _.isEmpty(this.scopes)
      },
        getMissingText(missing_scopes) {

          return 'Missing the following scopes: ' + _.join(missing_scopes, ', ')
        }
    },
    computed: {
      characters: function () {
          let returnValue = []
          let requiredScopes= this.scopes

          _.forEach(this.$page.user.data.characters, function (character) {

              let missing_scopes = _.difference(requiredScopes, character.scopes)

              if(_.isEmpty(missing_scopes))
                  return

              returnValue.push({
                  character_id: character.character_id,
                  name: character.name,
                  missing_scopes: missing_scopes
              })
          })

          return returnValue;
      }
    }
}
</script>
