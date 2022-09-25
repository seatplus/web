<template>
  <div ref="unknownIdRef">
    <div 
      v-if="isComplete" 
      :class="tailwindClass"
    >
      {{ name }}
    </div>
  </div>
</template>

<script>
import {onMounted, onUnmounted, ref} from "vue";

export default {
    name: "ResolveIdToName",
    props: {
        id: {
            type: Number,
            required: true
        },
        tailwindClass: {
            type: String,
            required: false,
            default: 'text-sm whitespace-nowrap text-gray-500'
        }
    },
    setup(props) {
        const isLoading = ref(false)
        const isComplete = ref(false)
        const name = ref('')
        const unknownIdRef = ref(null)


        const fetch = async () => {

            if(isLoading.value || isComplete.value)
                return

            isLoading.value = true

            await axios.get(route('resolve.id', props.id))
                .then(result => {
                    name.value = result.data.name
                    isComplete.value = true
                })
                .catch(error => console.log(error));

            isLoading.value = false
        }

        const observer = new IntersectionObserver(function(entries) {
            if(entries[0].isIntersecting === true) {
                if(isComplete.value)
                    return

                fetch()
            }
        }, { threshold: [1] });

        onMounted(() => {
            observer.observe(unknownIdRef.value);
        })

        onUnmounted(() => {
            observer.disconnect()
        })

        return {
            name,
            isComplete,
            unknownIdRef
        }
    }
}
</script>

<style scoped>

</style>