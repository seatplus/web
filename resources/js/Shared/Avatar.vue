<template>
  <div aria-hidden="true">
    <span
      v-show="!src"
      class="inline-flex items-center justify-center h-32 w-32 shrink-0 mx-auto rounded-full bg-gray-500"
    >
      <span class="text-5xl font-medium leading-none text-white">{{ getInitials(name) }}</span>
    </span>
    <img
      v-if="src"
      class="w-32 h-32 shrink-0 mx-auto bg-black rounded-full"
      :src="src"
      alt=""
    >
  </div>
</template>

<script>
    export default {
        name: "Avatar",
        props: {
            name: {
                type: String,
                required: true
            },
            src: {
                type: String
            }
        },
        methods: {
            getInitials(str) {
                const FirstName = str.split(' ')[0];
                const LastName = str.split(' ').reduceRight(a => a);

                // Extract the first two characters of a string
                if (FirstName === LastName) {
                    return FirstName.trim()
                        .substring(0, 2)
                        .toUpperCase();
                }

                // Abbreviate FirstName and LastName
                return [FirstName, LastName]
                    .map(name => name[0])
                    .join('').toUpperCase();
            }
        }

    }
</script>

<style scoped>

</style>
