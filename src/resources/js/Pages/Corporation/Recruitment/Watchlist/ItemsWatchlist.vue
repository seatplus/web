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
          route="autosuggestion.typesOrGroupOrCategories"
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
      <button
        :disabled="form.processing"
        type="submit"
        class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        @click="submit"
      >
        Save
      </button>
    </template>
  </TwoColumnCardWithSubmitAction>
</template>

<script>
import TwoColumnCardWithSubmitAction from "@/Shared/Layout/Forms/TwoColumnCardWithSubmitAction";
import Autosuggest from "@/Shared/Components/Autosuggest";
import {ref, watch} from "vue";
import DismissibleButton from "@/Shared/Layout/Buttons/DismissibleButton";
import {useForm} from "@inertiajs/inertia-vue3";
import route from 'ziggy'

export default {
    name: "ItemsWatchlist",
    components: {DismissibleButton, Autosuggest, TwoColumnCardWithSubmitAction},
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
        const unselect = (watchable_id) => form.items = _.filter(form.items, (item) => item.watchable_id !== watchable_id)
        const submit = () => form.post(route('update.watchlist', props.corporationId))

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