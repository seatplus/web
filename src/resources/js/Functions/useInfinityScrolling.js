import {onBeforeMount, onMounted, onUnmounted, ref, watch} from "vue";

export function useInfinityScrolling(route) {

  const scrollComponent = ref(null)
  const result = ref([])
  const page = ref(1)
  const isInitialRequestLoading = ref(true)
  const isLoading = ref(false)
  const isComplete = ref(false)

  const fetchData = async () => {

    if(isLoading.value || isComplete.value)
      return

    isLoading.value = true

    await axios.get(route, {
      params: {
        page: page.value,
      },
    }).then(response => {

      if (response.data.data.length) {
        page.value += 1;
        result.value.push(...response.data.data);
        isLoading.value = false
      } else {
        isComplete.value = true
      }
    }).catch(error => console.log(error));
  }

  const observer = new IntersectionObserver(function(entries) {
    if(entries[0].isIntersecting === true) {
      if(isComplete.value || isLoading.value)
        return

      fetchData()
    }
  }, { threshold: [1] });


  onBeforeMount(async () => {
    await fetchData(route, page.value)
    isInitialRequestLoading.value = false
  })

  onMounted(() => {
    observer.observe(scrollComponent.value);
  })

  onUnmounted(() => {
    //observer.disconnect()
  })

  return {
    scrollComponent,
    result,
    isInitialRequestLoading,
    isComplete,
    isLoading
  }
}
