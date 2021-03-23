<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      {{ pageTitle }}
    </PageHeader>

    <Characters
      :characters="characters"
      :enlistments="characterEnlistments"
      class="mb-4"
    />

    <Enlistments
      v-if="hasCorporationEnlistments"
      :enlistments="corporationEnlistments"
      :application="user_application"
      class="mb-4"
    />

  </div>
</template>

<script>
  import Layout from "@/Shared/SidebarLayout/Layout";
  import axios from 'axios';
  import PageHeader from "@/Shared/Layout/PageHeader"
  import Enlistments from "./Enlistments"
  import Characters from "./Characters"

  export default {
      name: "Index",
      components: {Characters, Enlistments, PageHeader, Layout},
    layout: Layout,
      props: {
          characters: {
              type: Array
          },
          user_application: {
              required: true
          }
      },
      data() {
          return {
              enlistments: [],
            pageTitle: 'Home'
          }
      },
      computed: {
          characterEnlistments() {
              return _.filter(this.enlistments, (enlistment) => enlistment.type === 'character')
          },
          corporationEnlistments() {

              return _.filter(this.enlistments, (enlistment) => enlistment.type === 'user')
          },
          hasCorporationEnlistments() {
              return ! _.isEmpty(this.corporationEnlistments)
          }
      },
      created() {
          this.getEnlistments()
      },
      methods: {
          emmitEvent() {
              /*TODO this.$eventBus.$emit('notification', {
                  type: 'success',
              })*/
          },
          async getEnlistments() {
              axios.get(this.$route('list.open.enlistments'))
                  .then((result) => this.enlistments.push(...result.data))
          },

      }
  }
</script>

<style scoped>

</style>
