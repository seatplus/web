<template>
  <div aria-hidden="true">
      <span v-show="!this.src" class="inline-flex items-center justify-center h-32 w-32 flex-shrink-0 mx-auto rounded-full bg-gray-500">
          <span class="text-5xl font-medium leading-none text-white">{{ getInitials(this.name) }}</span>
      </span>
      <img v-if="this.src" class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=4&amp;w=256&amp;h=256&amp;q=60" alt="">
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
