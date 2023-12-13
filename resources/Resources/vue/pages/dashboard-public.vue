<script setup lang="ts">
  import { Axios, Cryptor, Notyf } from '~/services'
  import { useRoute } from 'vue-router'
  import { reactive, ref } from 'vue'
  import { BarChart, PieChart } from '~/components/atoms/charts'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'

  const route = useRoute()
  const clusterId = ref()
  const data = reactive({
    cluster: {
      name: '',
      description: '',
      phone: '',
      picture: '',
      logo: '',
    },
    users: 0,
    patrols: 0,
    visitors: {
      total: 0,
      days: [],
      values: [],
    },
    visitorsByCategories: {
      total: 0,
      data: [],
      values: [],
    },
    patrolCounter: {
      get_from: '',
      data: {
        labels: [],
        values: [],
      },
    },
    absences: 0,
    reports: 0,
  })

  if (route.query.cluster_id) {
    clusterId.value = route.query.cluster_id

    Axios({
      method: 'GET',
      url: '/api/v1/dashboard/get_public_statistics',
      params: {
        cluster_id: clusterId.value,
      },
    }).then((response: any) => {
      Object.entries(response.data).forEach(([key, value]) => {
        data[key] = value
      })
    })
  } else {
    Notyf({
      type: 'error',
      message: 'Cluster ID is not found!',
      duration: 3000,
      ripple: true,
      dismissible: true,
      position: {
        x: 'right',
        y: 'top',
      },
    })
  }
</script>

<template>
  <div class="container mx-auto px-3 pb-3">
    <div class="grid grid-cols-12 mt-7">
      <div class="col-span-12 mb-3">
        <div class="flex">
          <div v-if="data.cluster.logo" class="flex-grow-0">
            <img :src="data.cluster.logo" alt="" style="height: 70px" />
          </div>
          <div class="flex-grow ml-3">
            <h1 class="font-bold text-xl">{{ data.cluster.name }}</h1>
            <p class="font-medium text-sm">{{ data.cluster.description }}</p>
            <p class="font-medium text-sm">{{ data.cluster.phone }}</p>
          </div>
        </div>
      </div>
      <div
        class="col-span-6 lg:col-span-4 xl:col-span-2 flex flex-col justify-center my-auto"
      >
        <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
          <div class="card-body">
            <div class="text-sm font-bold">Jumlah User:</div>
            <div
              class="text-gray-400 text-xs font-bold opacity-0"
              style="line-height: 0"
            >
              -
            </div>
            <div class="grid content-center grid-cols-12 mt-3">
              <div class="col-span-12 flex justify-center items-center">
                <span class="text-3xl xl:text-6xl">{{ data.users }}</span>
                <i class="fa-light fa-users text-2xl xl:text-4xl ml-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="col-span-6 lg:col-span-4 xl:col-span-2 flex flex-col justify-center my-auto"
      >
        <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
          <div class="card-body">
            <div class="text-sm font-bold">Jumlah Patroli:</div>
            <div class="text-gray-400 text-xs font-bold" style="line-height: 0">
              7 Hari Terakhir
            </div>
            <div class="grid content-center grid-cols-12 mt-3">
              <div class="col-span-12 flex items-center justify-center">
                <span class="text-3xl xl:text-6xl">{{ data.patrols }}</span>
                <i class="fa-light fa-route text-2xl xl:text-4xl ml-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="col-span-6 lg:col-span-4 xl:col-span-2 flex flex-col justify-center my-auto"
      >
        <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
          <div class="card-body">
            <div class="text-sm font-bold">Jumlah Tamu:</div>
            <div class="text-gray-400 text-xs font-bold" style="line-height: 0">
              7 Hari Terakhir
            </div>
            <div class="grid content-center grid-cols-12 mt-3">
              <div class="col-span-12 flex items-center justify-center">
                <span class="text-3xl xl:text-6xl">{{
                  data.visitors.total
                }}</span>
                <i class="fa-light fa-users text-2xl xl:text-4xl ml-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="col-span-6 lg:col-span-4 xl:col-span-2 flex flex-col justify-center my-auto"
      >
        <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
          <div class="card-body">
            <div class="text-sm font-bold">Jumlah Keterlambatan:</div>
            <div class="text-gray-400 text-xs font-bold" style="line-height: 0">
              7 Hari Terakhir
            </div>
            <div class="grid content-center grid-cols-12 mt-3">
              <div class="col-span-12 flex items-center justify-center">
                <span class="text-3xl xl:text-6xl">0</span>
                <i class="fa-light fa-user-clock text-2xl xl:text-4xl ml-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="col-span-6 lg:col-span-4 xl:col-span-2 flex flex-col justify-center my-auto"
      >
        <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
          <div class="card-body">
            <div class="text-sm font-bold">Jumlah Ijin:</div>
            <div class="text-gray-400 text-xs font-bold" style="line-height: 0">
              7 Hari Terakhir
            </div>
            <div class="grid content-center grid-cols-12 mt-3">
              <div class="col-span-12 flex justify-center items-center">
                <span class="text-3xl xl:text-6xl">{{ data.absences }}</span>
                <i
                  class="fa-light fa-clipboard-check text-2xl xl:text-4xl ml-3"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="col-span-6 lg:col-span-4 xl:col-span-2 flex flex-col justify-center my-auto"
      >
        <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
          <div class="card-body">
            <div class="text-sm font-bold">Jumlah Laporan:</div>
            <div class="text-gray-400 text-xs font-bold" style="line-height: 0">
              7 Hari Terakhir
            </div>
            <div class="grid content-center grid-cols-12 mt-3">
              <div class="col-span-12 flex justify-center items-center">
                <span class="text-3xl xl:text-6xl">{{ data.reports }}</span>
                <i
                  class="fa-light fa-image-polaroid text-2xl xl:text-4xl ml-3"
                ></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-span-12 xl:col-span-6 mt-4">
        <div class="card bg-base-100 shadow-xl mr-4">
          <div class="card-body">
            <BarChart
              title="Jumlah Tamu 7 Hari Terakhir"
              :labels="data.visitors.days"
              :series="[
                {
                  name: 'Tamu',
                  // data: [10, 7, 12, 15, 11, 28, 19],
                  data: data.visitors.values,
                },
              ]"
              unit="global.peoples"
            />
          </div>
        </div>
      </div>
      <div class="col-span-12 xl:col-span-6 mt-4">
        <div class="card bg-base-100 shadow-xl">
          <div class="card-body">
            <PieChart
              title="Jumlah Tamu Per Kategori 7 Hari Terakhir"
              :series="[
                {
                  name: 'Tamu',
                  colorByPoint: true,
                  data: data.visitorsByCategories.values,
                },
              ]"
              suffix="global.peoples"
            />
          </div>
        </div>
      </div>
      <div class="col-span-12 mt-4">
        <div class="card bg-base-100 shadow-xl">
          <div class="card-body">
            <BarChart
              :title="`Patrol Counter By Range Hour (${data.patrolCounter.get_from})`"
              :labels="data.patrolCounter.data.labels"
              :series="[
                {
                  name: 'Jumlah Patroli',
                  data: data.patrolCounter.data.values,
                },
              ]"
              unit="global.peoples"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
