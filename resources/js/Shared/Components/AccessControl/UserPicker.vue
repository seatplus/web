<template>
  <div class="relative">
    <input
      v-model="query"
      type="search"
      autocomplete="off"
      :placeholder="trans('web::access_control.moderate.search_users')"
      class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-xs focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-hidden"
      @input="search"
    >
    <ul
      v-if="results.length"
      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5"
    >
      <li
        v-for="user in results"
        :key="user.id"
        class="flex cursor-pointer items-center gap-3 px-3 py-2 text-sm hover:bg-indigo-50"
        @click="pick(user)"
      >
        <EveImage
          v-if="user.mainCharacter"
          :object="user.mainCharacter"
          :size="64"
          tailwind_class="h-6 w-6 rounded-full"
        />
        <span
          v-else
          class="h-6 w-6 shrink-0 rounded-full bg-gray-200"
        />
        <span class="truncate">{{ user.mainCharacter?.name ?? `#${user.id}` }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from "vue";
import EveImage from "@/Shared/EveImage.vue";
import { getJson } from "@/Functions/http";
import { useTranslations } from "@/composables/useTranslations";
import ListUserController from "@/actions/Seatplus/Web/Http/Controllers/AccessControl/ListUserController";

const emit = defineEmits(["select"]);
const { trans } = useTranslations();

const query = ref("");
const results = ref([]);
let timer = null;

const search = () => {
    clearTimeout(timer);
    timer = setTimeout(async () => {
        if (query.value.trim().length < 3) {
            results.value = [];
            return;
        }
        const response = await getJson(ListUserController.url({ query: { name: query.value.trim() } }));
        results.value = response?.data ?? [];
    }, 300);
};

const pick = (user) => {
    emit("select", user.id);
    query.value = "";
    results.value = [];
};
</script>
