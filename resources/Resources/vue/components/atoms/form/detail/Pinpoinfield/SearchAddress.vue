<script setup lang="ts">
  import { defineProps } from 'vue'
  import GetFixLocationName from '~/components/atoms/form/detail/Pinpoinfield/GetFixLocationName'
  import axios from 'axios'
  import { ref, watch } from 'vue'

  const props = defineProps({
    setCenter: {
      type: Function,
      required: true,
    },
  })

  const searchTerm = ref('')
  const address = ref([])
  const searchProgress = ref(false)
  const addressListOpen = ref(false)

  const methods = {
    searchAddress: () => {
      axios({
        method: 'GET',
        url: `https://nominatim.openstreetmap.org/search?format=jsonv2&q=${searchTerm.value}&addressdetails=1&accept-language=ID`,
      })
        .then((res) => {
          addressListOpen.value = true
          searchProgress.value = false
          address.value = res.data
        })
        .catch((err) => {
          searchProgress.value = false
        })
    },
    selectAddress: (index) => {
      addressListOpen.value = false
      const { lat, lon } = address.value[index]
      props.setCenter(lat, lon)
    },
    focusSearchAddress: () => {
      if (address.value.length) {
        addressListOpen.value = true
      }
    },
    clearAllAddress: () => {
      address.value.length = 0
      addressListOpen.value = false
      searchTerm.value = ''
    },
  }

  let calcMover = 0
  let calcInterver
  watch(searchTerm, () => {
    if (searchTerm.value) {
      calcMover += 1
      if (!calcInterver) {
        searchProgress.value = true
        let match = 0
        calcInterver = setInterval(() => {
          const currentMover = calcMover
          setTimeout(() => {
            if (currentMover === calcMover) {
              match += 1
              if (match >= 2) {
                clearInterval(calcInterver)
                calcInterver = null
                methods.searchAddress()
              }
            } else {
              match = 0
            }
          }, 250)
        }, 1000)
      }
    } else {
      searchProgress.value = false
      address.value.length = 0
    }
  })
</script>

<template>
  <div class="absolute z-20" style="top: 15px; right: 15px; width: 390px">
    <div class="w-full flex items-center">
      <input
        v-model="searchTerm"
        type="text"
        class="input input-md w-full drop-shadow-md shadow-gray-400 focus:outline-none focus:ring-0 pr-10 z-10"
        placeholder="Telusuri Maps"
        @focus="methods.focusSearchAddress"
      />
      <div v-if="searchProgress" class="z-20" style="margin-left: -30px">
        <i class="fad fa-spin fa-spinner-third"></i>
      </div>
      <div
        v-if="address.length && !searchProgress"
        class="z-20"
        style="margin-left: -30px"
      >
        <i
          class="fa-solid fa-xmark cursor-pointer"
          @click="methods.clearAllAddress"
        ></i>
      </div>
    </div>
    <div
      v-if="address.length && addressListOpen"
      class="w-full bg-white drop-shadow-md shadow-gray-400 py-2 rounded-lg mt-2 scroll-search-address"
      style="max-height: 300px; overflow-y: auto"
    >
      <ul>
        <li
          v-for="(item, index) in address"
          :key="index"
          class="py-2 px-2 hover:bg-gray-100 cursor-pointer"
          @click="methods.selectAddress(index)"
        >
          {{ GetFixLocationName(item) }}
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
  /* width */
  .scroll-search-address::-webkit-scrollbar {
    width: 10px;
  }

  /* Track */
  .scroll-search-address::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  /* Handle */
  .scroll-search-address::-webkit-scrollbar-thumb {
    background: #888;
  }

  /* Handle on hover */
  .scroll-search-address::-webkit-scrollbar-thumb:hover {
    background: #555;
  }
</style>
