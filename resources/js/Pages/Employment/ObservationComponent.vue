<template>
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
    <div class="flex items-center gap-3 p-4 border-b border-gray-100">
      <EveImage
        :object="{ corporation_id: corporation.corporation_id }"
        :size="64"
        tailwind_class="h-10 w-10 rounded"
      />
      <div class="flex-1 min-w-0">
        <h3 class="text-base font-semibold text-gray-900 truncate">
          [{{ corporation.ticker }}] {{ corporation.name }}
        </h3>
      </div>
      <input
        v-model="search"
        type="text"
        placeholder="Search member…"
        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
      >
    </div>

    <div
      v-if="loading"
      class="p-4 text-sm text-gray-400"
    >
      Loading members…
    </div>

    <div
      v-else-if="members.length === 0"
      class="p-4 text-sm text-gray-500"
    >
      No members found.
    </div>

    <ul
      v-else
      role="list"
      class="divide-y divide-gray-100"
    >
      <li
        v-for="member in members"
        :key="member.id"
        class="flex items-center gap-4 p-4"
      >
        <EveImage
          v-if="member.main_character"
          :object="{ character_id: member.main_character.character_id }"
          :size="64"
          tailwind_class="h-10 w-10 rounded-full"
        />
        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold text-gray-900 truncate">
            {{ member.main_character?.name ?? 'Unknown' }}
          </p>
          <p class="text-xs text-gray-500">
            {{ member.count_complete }}/{{ member.count_total }} characters compliant
            <span
              v-for="character in member.characters"
              :key="character.character_id"
              class="ml-2 text-gray-400"
            >
              · {{ character.name }} {{ lastSeen(character.last_logon) }}
            </span>
          </p>
        </div>
        <span
          v-if="member.employment_status"
          class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
          :class="statusClass(member.employment_status)"
        >
          {{ member.employment_status }}
        </span>
        <span
          class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
          :class="member.count_missing === 0 ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700'"
        >
          {{ member.count_missing === 0 ? 'Compliant' : member.count_missing + ' missing scopes' }}
        </span>
        <Link
          :href="member.inspect_url"
          class="text-sm font-medium text-indigo-600 hover:text-indigo-500 whitespace-nowrap"
        >
          Inspect
        </Link>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import { Link } from "@inertiajs/vue3";
import { getJson } from "@/Functions/http";
import EveImage from "@/Shared/EveImage.vue";

const props = defineProps({
  corporation: {
    required: true,
    type: Object,
  },
});

const members = ref([]);
const loading = ref(true);
const search = ref("");

async function fetchMembers() {
  loading.value = true;
  const url = search.value
    ? `${props.corporation.observe_url}?search=${encodeURIComponent(search.value)}`
    : props.corporation.observe_url;
  const response = await getJson(url);
  members.value = response?.data ?? [];
  loading.value = false;
}

function lastSeen(lastLogon) {
  if (!lastLogon) {
    return "(no activity)";
  }

  const days = Math.floor((Date.now() - new Date(lastLogon).getTime()) / 86400000);
  return days <= 0 ? "(today)" : `(${days}d ago)`;
}

function statusClass(status) {
  return {
    active: "bg-green-50 text-green-700",
    suspended: "bg-amber-50 text-amber-700",
    alumni: "bg-gray-100 text-gray-600",
  }[status] ?? "bg-gray-100 text-gray-600";
}

let debounce;
watch(search, () => {
  clearTimeout(debounce);
  debounce = setTimeout(fetchMembers, 300);
});

onMounted(fetchMembers);
</script>
