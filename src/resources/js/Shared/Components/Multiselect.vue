<template>
  <div>
    <Autosuggest
      :key="uniqueID"
      :route="route"
      :label="label"
      :placeholder="placeholder"
      @selectedObject="onSelected"
    />
    <DismissibleButton
      v-for="selection in selections"
      :id="selection.id"
      :key="selection.id"
      :name="selection.name"
      @remove="removeEntry"
    />
  </div>
</template>

<script>
import Autosuggest from "@/Shared/Components/Autosuggest";
import DismissibleButton from "@/Shared/Layout/Buttons/DismissibleButton";
export default {
  name: "Multiselect",
  components: {
      DismissibleButton,
    Autosuggest
    },
  props: {
    modelValue: {
      required: true
    },
    route: {
      required: true,
      type: String
    },
    label: {
      required: true,
      type: String
    },
    placeholder: {
      required: true,
      type: String
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      uniqueID: +new Date(),
      selections: this.modelValue,
      suggestions: [],
    }
  },
  methods: {
    onSelected(selection) {

      this.selections.push(selection)

      this.update()
    },
    removeEntry(id) {

      this.selections = this.selections.filter(suggestion => suggestion.id !== id)

      this.update()
    },
    update() {

      this.$emit('update:modelValue', this.selections)
      this.uniqueID++
    }
  }
}
</script>
