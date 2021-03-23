<template>
  <div
    v-if="missing_characters_scopes.length >0 "
    class="bg-yellow-100 shadow sm:rounded-lg"
  >
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-yellow-900">
        Missing scopes warning
      </h3>
      <p class="text-base leading-6 font-medium text-yellow-900">
        Some characters are missing some scopes on their refresh_token for seatplus to fetch information from esi.
      </p>
      <div class="mt-8 bg-white shadow overflow-hidden rounded-md">
        <ul class="divide-y divide-yellow-400">
          <li
            v-for="character in missing_characters_scopes"
            :key="character.name"
            class=" px-6 py-4 sm:flex sm:items-start sm:justify-between bg-yellow-200"
          >
            <div class="sm:flex sm:items-start">
              <eve-image
                :object="character"
                :size="128"
              />
              <div class="mt-3 sm:mt-0 sm:ml-4">
                <div class="text-sm leading-5 font-medium text-yellow-900">
                  {{ character.name }}
                </div>
                <div class="mt-1 text-sm leading-5 text-yellow-600 sm:flex sm:items-center">
                  {{ getMissingText(character.missing_scopes) }}
                </div>
              </div>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-6 sm:flex-shrink-0">
              <span class="inline-flex rounded-md shadow-sm">
                <a
                  :href="$route('auth.eve.step_up', { character_id: character.character_id, add_scopes: getMissingScopeString(character.missing_scopes)})"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150"
                >
                  Fix
                </a>
              </span>
            </div>
          </li>

          <!-- More items... -->
        </ul>
      </div>
      <ul class="mt-5 rounded-md bg-yellow-200 divide-y divide-black" />
    </div>
  </div>
</template>

<script>
import EveImage from "../EveImage"

export default {
  name: "RequiredScopesWarning",
    components: {
      EveImage
    },
    props: {
      dispatch_transfer_object: {
        type: Object,
        required: true
      }
    },
  data() {
    return {
      requiredScopes: this.dispatch_transfer_object ? this.dispatch_transfer_object.required_scopes : [],
      characters: this.$page.props.user.data.characters
    }
  },
  computed: {
    missing_characters_scopes: function () {
      let returnValue = []
      let requiredScopes= this.requiredScopes

      _.forEach(this.characters, function (character) {

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
  },
    methods: {
      isShown() {
          return ! _.isEmpty(this.scopes)
      },
        getMissingText(missing_scopes) {

          return 'Missing the following scopes: ' + _.join(missing_scopes, ', ')
        },
        getMissingScopeString (missing_scopes) {
            return _.toString(missing_scopes)
        },
    }
}
</script>
