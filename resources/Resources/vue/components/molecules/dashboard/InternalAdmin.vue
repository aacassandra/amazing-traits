<script setup lang="ts">
  import { BarChart, PieChart } from '~/components/atoms/charts'
  import { onMounted, reactive } from 'vue'
  import { Axios } from '~/services'

  const data = reactive({
    clusters: {
      unverified: 0,
      verified: 0,
    },
    subscriptions: {
      subscribe: 0,
      unsubscribe: 0,
      toBeExpired: 0,
      trial: 0,
      basic: 0,
      advantage: 0,
    },
    usersKnowFrom: {
      total: 0,
      data: [],
      values: [],
    },
  })

  onMounted(() => {
    Axios({
      method: 'GET',
      url: '/api/v1/dashboard/get_statistics?role=INTERNAL-ADMIN',
    }).then((response: any) => {
      Object.entries(response.data).forEach(([key, value]) => {
        data[key] = value
      })
    })
  })
</script>
<template>
  <div class="grid grid-cols-12 mt-7">
    <div
      class="col-span-12 md:col-span-6 lg:col-span-3 flex flex-col justify-center my-auto"
    >
      <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="text-sm font-bold">Jumlah Cluster Unverified:</div>
          <div
            class="text-gray-400 text-xs font-bold opacity-0"
            style="line-height: 0"
          >
            -
          </div>
          <div class="grid content-center grid-cols-12 mt-3">
            <div class="col-span-12 flex justify-center items-center">
              <span class="text-6xl">{{ data.clusters.unverified }}</span>
              <i class="fa-light fa-house-circle-xmark text-4xl ml-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="col-span-12 md:col-span-6 lg:col-span-3 flex flex-col justify-center my-auto"
    >
      <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="text-sm font-bold">Jumlah Cluster Verified:</div>
          <div class="grid content-center grid-cols-12 mt-3">
            <div class="col-span-12 flex items-center justify-center">
              <span class="text-6xl">{{ data.clusters.verified }}</span>
              <i class="fa-light fa-house-circle-check text-4xl ml-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="col-span-12 md:col-span-6 lg:col-span-3 flex flex-col justify-center my-auto"
    >
      <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="text-sm font-bold">Jumlah Cluster Subscribe:</div>
          <div class="grid content-center grid-cols-12 mt-3">
            <div class="col-span-12 flex items-center justify-center">
              <span class="text-6xl">{{ data.subscriptions.subscribe }}</span>
              <i class="fa-light fa-house-signal text-4xl ml-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="col-span-12 md:col-span-6 lg:col-span-3 flex flex-col justify-center my-auto"
    >
      <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="text-sm font-bold">Jumlah Cluster Unsubscribe:</div>
          <div class="grid content-center grid-cols-12 mt-3">
            <div class="col-span-12 flex items-center justify-center">
              <span class="text-6xl">{{ data.subscriptions.unsubscribe }}</span>
              <i class="fa-light fa-house-window text-4xl ml-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="col-span-12 md:col-span-6 lg:col-span-3 flex flex-col justify-center my-auto"
    >
      <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="text-sm font-bold">Jumlah Cluster Subscribe:</div>
          <div class="text-gray-400 text-xs font-bold" style="line-height: 0">
            Expired Dalam 1 Bulan
          </div>
          <div class="grid content-center grid-cols-12 mt-3">
            <div class="col-span-12 flex justify-center items-center">
              <span class="text-6xl">{{ data.subscriptions.toBeExpired }}</span>
              <i class="fa-light fa-house-circle-exclamation text-4xl ml-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="col-span-12 md:col-span-6 lg:col-span-3 flex flex-col justify-center my-auto"
    >
      <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="text-sm font-bold">Jumlah Subsriber Trial:</div>
          <div class="grid content-center grid-cols-12 mt-3">
            <div class="col-span-12 flex justify-center items-center">
              <span class="text-6xl">{{ data.subscriptions.trial }}</span>
              <i class="fa-light fa-building-lock text-4xl ml-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="col-span-12 md:col-span-6 lg:col-span-3 flex flex-col justify-center my-auto"
    >
      <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="text-sm font-bold">Jumlah Subsriber Basic:</div>
          <div class="grid content-center grid-cols-12 mt-3">
            <div class="col-span-12 flex justify-center items-center">
              <span class="text-6xl">{{ data.subscriptions.basic }}</span>
              <i class="fa-light fa-building text-4xl ml-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="col-span-12 md:col-span-6 lg:col-span-3 flex flex-col justify-center my-auto"
    >
      <div class="card mr-4 mb-4 bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="text-sm font-bold">Jumlah Subsriber Advantage:</div>
          <div class="grid content-center grid-cols-12 mt-3">
            <div class="col-span-12 flex justify-center items-center">
              <span class="text-6xl">{{ data.subscriptions.advantage }}</span>
              <i class="fa-light fa-house-building text-4xl ml-3"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-span-12 lg:col-span-6 mt-4">
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <PieChart
            title="Jumlah Pendaftar Cluster Berdasarkan Sumber Informasi yang Didapat"
            :series="[
              {
                name: 'Pendaftar',
                colorByPoint: true,
                data: data.usersKnowFrom.values,
              },
            ]"
            suffix="global.peoples"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
  .highcharts-figure,
  .highcharts-data-table table {
    margin: 1em auto;
  }

  #container {
    height: 400px;
  }

  .highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
  }

  .highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
  }

  .highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
  }

  .highcharts-data-table td,
  .highcharts-data-table th,
  .highcharts-data-table caption {
    padding: 0.5em;
  }

  .highcharts-data-table thead tr,
  .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
  }

  .highcharts-data-table tr:hover {
    background: #f1f7ff;
  }
</style>
