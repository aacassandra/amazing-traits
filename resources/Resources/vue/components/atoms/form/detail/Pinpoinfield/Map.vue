<template>
  <div class="grid grid-cols-12 mt-6 z-20 relative">
    <div class="col-span-12">
      <div class="marker-position">
        click on the map, move the marker, click on the marker
      </div>
      <div id="map" style="height: 550px" class="z-10"></div>
      <SearchAddress :set-center="methods.setCenter" />
    </div>
    <div class="col-span-12 flex justify-end mt-4">
      <div
        v-if="currSelectedMarkerPosition"
        class="btn btn-sm btn-access mr-2 normal-case"
        @click="methods.copyMarker"
      >
        <i class="fa-duotone fa-copy"></i> Copy
      </div>
      <button
        :disabled="!markers.length"
        class="btn btn-sm btn-accent normal-case text-white"
        @click="methods.clearAll"
      >
        <i class="fa-regular fa-clock-rotate-left"></i>
        {{ props.isMultiple ? 'Clear All' : 'Clear' }}
      </button>
      <button
        class="btn btn-sm btn-primary ml-2 normal-case text-white"
        @click="methods.save"
      >
        <i
          :class="{
            'fa-duotone': true,
            'fa-check': !isMultiple,
            'fa-check-double': isMultiple,
          }"
        ></i>
        Verify
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
  /* eslint-disable no-console */
  import { defineProps, inject, onMounted, reactive, ref } from 'vue'
  import { Element } from '~/types/components/atoms/forms/header'
  import 'leaflet/dist/leaflet.css'
  import L from 'leaflet'
  import './leaflet_numbered_markers'
  import { GetRandomString, Notyf } from '~/services'
  import SearchAddress from './SearchAddress.vue'

  const props = defineProps({
    isMultiple: {
      type: Boolean,
      default: false,
    },
    properties: {
      type: Object as () => Element,
      default: null,
    },
  })

  const clientWidth = inject('clientWidth')

  const temp = inject('tempVal') as {
    value_: any
    displayValue: string
    showModal: boolean
    value: any
  }

  // Helper for multiple select only
  const fix = reactive({
    displayValue: '',
    value: [],
  })
  const theme = inject('theme')
  const nativeEdit = inject<{ value: boolean }>('nativeEdit')

  const map = ref()
  const markers = ref([])
  const markerCounter = ref(0)
  const buttonRemove = (id: string) => {
    return (
      '<div style="width: 145px" class="flex">' +
      `<div class="btn btn-xs cp-lnglat-${id} mr-2 normal-case">` +
      '<i class="fa-duotone fa-copy"></i> Copy' +
      '</div>' +
      `<div class="btn btn-accent text-white btn-xs remove-${id} normal-case">` +
      '<i class="fa-sharp fa-solid fa-trash"></i> Delete' +
      '</div>' +
      '</div>'
    )
  }
  const currpos = ref()
  const currSelectedMarkerPosition = ref('')

  const methods = {
    getLocation: () => {
      return new Promise((resolve) => {
        const showPosition = (position) => {
          currpos.value = position
          resolve(true)
        }
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(showPosition)
        } else {
          alert('Geolocation is not supported by this browser.')
          resolve(true)
        }
      })
    },
    recalc: async () => {
      markerCounter.value = 0
      markers.value.forEach((mar, marI) => {
        markerCounter.value++
        let customIcon = new L.NumberedDivIcon({
          number: markerCounter.value,
        })
        mar.marker.setIcon(customIcon)
      })
    },
    copyMarker: () => {
      if (currSelectedMarkerPosition.value) {
        navigator.clipboard.writeText(currSelectedMarkerPosition.value)
        Notyf({
          type: 'success',
          message: 'The coordinates have been copied to the clipboard!',
          duration: 3000,
          ripple: true,
          dismissible: true,
          position: {
            x: 'right',
            y: 'top',
          },
        })
      }
    },
    addMarker: (e, setCenter = false) => {
      if (!props.isMultiple && markers.value.length) {
        return
      }

      markerCounter.value++
      let id = GetRandomString(5)

      // Add marker to map at click location
      const markerPlace = document.querySelector('.marker-position')
      markerPlace.textContent = `new marker: ${e.latlng.lat}, ${e.latlng.lng}`
      currSelectedMarkerPosition.value = `${e.latlng.lat}, ${e.latlng.lng}`

      const latlng = L.latLng(e.latlng.lat, e.latlng.lng)

      const marker = L.marker(latlng, {
        draggable: true,
        icon: new L.NumberedDivIcon({
          number: props.isMultiple ? markerCounter.value : null,
        }),
      })
        .addTo(map.value)
        .bindPopup(buttonRemove(id))

      if (setCenter) {
        map.value.setView(latlng)
      }

      markers.value.push({
        id,
        marker,
        latlng,
      })

      // event remove marker
      marker.on('popupopen', function () {
        methods.removeMarker(marker, id)
        methods.copyLngLat(marker, id)
      })

      // event draged marker
      marker.on('dragend', function (e) {
        let lat = marker.getLatLng().lat
        let lng = marker.getLatLng().lng
        methods.dragedMaker(lat, lng, id)
      })
    },
    removeMarker: (marker, id) => {
      const btn = document.querySelector(`.remove-${id}`)
      btn.addEventListener('click', function () {
        markers.value.forEach((mar, marI) => {
          if (mar.id === id) {
            markers.value.splice(marI, 1)
          }
        })

        map.value.removeLayer(marker)
        methods.recalc()
      })
    },
    copyLngLat: (marker, id) => {
      const btn = document.querySelector(`.cp-lnglat-${id}`)
      btn.addEventListener('click', function () {
        markers.value.forEach((mar, marI) => {
          if (mar.id === id) {
            navigator.clipboard.writeText(`${mar.latlng.lat}, ${mar.latlng.lng}`)
            Notyf({
              type: 'success',
              message: 'The coordinates have been copied to the clipboard!',
              duration: 3000,
              ripple: true,
              dismissible: true,
              position: {
                x: 'right',
                y: 'top',
              },
            })
          }
        })
      })
    },
    dragedMaker: (lat, lng, id) => {
      const latlng = L.latLng(lat, lng)
      markers.value.forEach((marker, markerI) => {
        if (marker.id === id) {
          markers.value[markerI].latlng = latlng
        }
      })

      const markerPlace = document.querySelector('.marker-position')
      markerPlace.textContent = `change position: ${lat}, ${lng}`
      currSelectedMarkerPosition.value = `${lat}, ${lng}`
    },
    clearAll: () => {
      markers.value.forEach((mar, marI) => {
        map.value.removeLayer(mar.marker)
      })
      markers.value = []
      markerCounter.value = 0
    },
    save: () => {
      nativeEdit.value = true
      if (props.isMultiple) {
        temp.value = []
        temp.value_ = []
        // temp.value_
        markers.value.forEach((mar) => {
          temp.value.push({
            type: 'Point',
            coordinates: {
              lat: mar.latlng.lat,
              lng: mar.latlng.lng,
            },
          })
          temp.value_.push({
            type: 'Point',
            coordinates: {
              lat: mar.latlng.lat,
              lng: mar.latlng.lng,
            },
          })
        })
        temp.showModal = false
        temp.displayValue = markers.value.length
          ? `${markers.value.length} Location`
          : ''
      } else {
        if (markers.value.length) {
          temp.value = {
            type: 'Point',
            coordinates: {
              lat: markers.value[0].latlng.lat,
              lng: markers.value[0].latlng.lng,
            },
          }
          temp.value_ = {
            type: 'Point',
            coordinates: {
              lat: markers.value[0].latlng.lat,
              lng: markers.value[0].latlng.lng,
            },
          }
        } else {
          temp.value = null
          temp.value_ = null
        }
        temp.showModal = false
        temp.displayValue = markers.value.length ? `1 Location` : ''
      }
      setTimeout(() => {
        nativeEdit.value = false
      }, 300)
    },
    setCenter: (lat, lon) => {
      map.value.setView([lat, lon], 17)
    },
  }

  onMounted(async () => {
    await methods.getLocation()

    // config map
    let config = {
      minZoom: 7,
      maxZomm: 18,
    }
    // magnification with which the map will start
    const zoom = 15
    // co-ordinates
    const lat = currpos.value.coords.latitude || 52.22977
    const lon = currpos.value.coords.longitude || 21.01178

    // calling map
    map.value = L.map('map', config).setView([lat, lon], zoom)

    // Used to load and display tile layers on the map
    // Most tile servers require attribution, which you can set under `Layer`
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map.value)

    // add marker on click
    map.value.on('click', methods.addMarker)

    if (props.isMultiple) {
      if (temp.value_ && temp.value_.length) {
        temp.value_.forEach((mar, marI) => {
          if (mar.type === 'Point') {
            if (Array.isArray(mar.coordinates)) {
              const latlng = L.latLng(mar.coordinates[1], mar.coordinates[0])
              methods.addMarker({ latlng }, marI === 0)
            } else {
              const latlng = L.latLng(mar.coordinates.lat, mar.coordinates.lng)
              methods.addMarker({ latlng }, marI === 0)
            }
          } else {
            //
          }
        })
      }
    } else {
      if (temp.value_ && temp.value_.type === 'Point') {
        if (Array.isArray(temp.value_.coordinates)) {
          const latlng = L.latLng(
            temp.value_.coordinates[1],
            temp.value_.coordinates[0],
          )
          methods.addMarker({ latlng }, true)
        } else {
          const latlng = L.latLng(
            temp.value_.coordinates.lat,
            temp.value_.coordinates.lng,
          )
          methods.addMarker({ latlng }, true)
        }
      }
    }
  })
</script>
<style>
  .marker-position {
    position: absolute;
    bottom: 0;
    left: 0;
    z-index: 999;
    padding: 10px;
    font-weight: 700;
    background-color: #fff;
  }

  .leaflet-div-icon {
    background: transparent;
    border: none;
  }

  .leaflet-marker-icon {
    cursor: default;
  }

  .leaflet-marker-icon img {
    position: relative;
    height: inherit;
    width: inherit;
    cursor: pointer;
  }

  .leaflet-marker-icon .number {
    position: relative;
    top: -53px;
    left: 8px;
    font-size: 12px;
    font-weight: 600;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
  }
</style>
