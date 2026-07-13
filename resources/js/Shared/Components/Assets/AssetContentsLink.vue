<template>
  <!-- "Both": clicking opens the contents in a modal (fetched on demand); the href is the
       shareable character.item deep link that renders the full ItemDetails page on direct visit. -->
  <a
    :href="itemUrl"
    @click.stop.prevent="open"
  >
    <slot />
  </a>
  <teleport to="#destination">
    <WithDismissButtonModal
      v-model="openModal"
      width="4xl"
    >
      <DialogTitle
        as="h3"
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Contents
      </DialogTitle>

      <ItemList
        v-if="contents.length"
        :items="contents"
        :compact="false"
        class="mt-4"
      />
      <p
        v-else
        class="mt-4 text-sm text-gray-500"
      >
        Loading…
      </p>
    </WithDismissButtonModal>
  </teleport>
</template>

<script>
import { ref, defineAsyncComponent } from "vue";
import { DialogTitle } from "@headlessui/vue";
import WithDismissButtonModal from "@/Shared/Modals/WithDismissButtonModal.vue";
import { getJson } from "@/Functions/http";
import { item as itemAction } from "@/actions/Seatplus/Web/Http/Controllers/Character/AssetsController";

// Lazily resolved to break the ItemList ↔ AssetContentsLink import cycle (a container's
// contents render another ItemList, which can drill again). A static import deadlocks at
// module init ("can't access 'ItemList' before initialization").
const ItemList = defineAsyncComponent(() => import("./ItemList.vue"));

export default {
    name: "AssetContentsLink",
    components: { WithDismissButtonModal, DialogTitle, ItemList },
    props: {
        characterId: {
            type: Number,
            required: true,
        },
        itemId: {
            type: Number,
            required: true,
        },
    },
    setup(props) {
        const openModal = ref(false)
        const contents = ref([])

        const itemUrl = itemAction.url({ character_id: props.characterId, item_id: props.itemId })

        const open = async () => {
            if (! contents.value.length) {
                // X-Modal returns [the container]; its `content` is the one level of contents.
                const data = await getJson(itemUrl, { 'X-Modal': true })
                contents.value = data[0]?.content ?? []
            }

            openModal.value = true
        }

        return { openModal, contents, itemUrl, open }
    }
}
</script>
