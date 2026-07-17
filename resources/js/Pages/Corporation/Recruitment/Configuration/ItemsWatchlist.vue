<template>
  <TwoColumnCardWithSubmitAction :index="1">
    <template #title>
      Items Watchlist
    </template>
    <template #description>
      Please select item types, groups or categories you want to specifically highlight during recruitment. F.e. an item of type Nyx belongs to the group of Supercarrier and to the category Ship.
    </template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <Autosuggest
          :key="uniqueId"
          route-name="autosuggestion.typesOrGroupOrCategories"
          label="Items"
          placeholder="Search for Items"
          @selectedObject="select"
        />
      </div>
      <div>
        <DismissibleButton
          v-for="item in form.items"
          :id="item.id"
          :key="item.id"
          :name="item.name"
          @remove="unselect"
        />
      </div>
    </div>
    <template #button>
      <Button
        :is-inertia-button="false"
        @click="submit"
      >
        Save
      </Button>
    </template>
  </TwoColumnCardWithSubmitAction>
</template>

<script>
import TwoColumnCardWithSubmitAction from "@/Shared/Layout/Forms/TwoColumnCardWithSubmitAction.vue";
import Autosuggest from "@/Shared/Components/Autosuggest.vue";
import {ref, watch} from "vue";
import DismissibleButton from "@/Shared/Layout/Buttons/DismissibleButton.vue";
import Button from "@/Shared/Layout/Button.vue";
import { useForm } from "@inertiajs/vue3";
import { updateWatchlist } from "@/actions/Seatplus/Web/Http/Controllers/Corporation/Recruitment/EnlistmentsController";

export default {
    name: "ItemsWatchlist",
    components: {Button, DismissibleButton, Autosuggest, TwoColumnCardWithSubmitAction},
    props: {
        items: {
            required: true,
            type: Array
        },
        corporationId: {
            required: true,
            type: Number
        }
    },
    setup(props) {
        const uniqueId = ref(+new Date())

        //const watchedItems = ref(props.items)
        const form = useForm({
            items: props.items
        })

        const select = (selection) => form.items.push(selection)
        const unselect = (id) => form.items = _.filter(form.items, (item) => item.id !== id)
        const submit = () => form.post(updateWatchlist.url(props.corporationId))

        watch(form.items, () => uniqueId.value++, {deep: true})

        return {
            uniqueId,
            select,
            unselect,
            form,
            submit
        }
    }
}
</script>

<style scoped>

</style>