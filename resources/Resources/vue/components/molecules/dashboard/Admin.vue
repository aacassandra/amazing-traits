<script setup lang="ts">
  import { BarChart, PieChart } from '~/components/atoms/charts'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'
  import { onMounted, reactive } from 'vue'
  import { Axios } from '~/services'

  const data = reactive({
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

  onMounted(() => {
    Axios({
      method: 'GET',
      url: '/api/v1/dashboard/get_statistics?role=ADMIN',
    }).then((response: any) => {
      console.log(response)
      Object.entries(response.data).forEach(([key, value]) => {
        data[key] = value
      })
    })
  })
</script>
<template>
  <div class="grid grid-cols-12 mt-7">
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
    <div class="col-span-12 mt-4 mb-4">
      <vue-good-table-custom
        :pagination-options="{
          enabled: true,
          pagination: true,
        }"
        :columns="[
          {
            label: 'Pengunjung',
            field: 'pengunjung',
            type: 'string',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            label: 'Keperluan',
            field: 'keperluan',
            type: 'string',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            label: 'Rumah Tujuan',
            field: 'rumah_tujuan',
            type: 'string',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            label: 'Petugas',
            field: 'petugas',
            type: 'string',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            label: 'Created At',
            show: true,
            type: 'date',
            width: '150px',
            field: 'created_at',
            dateInputFormat: 'dd/MM/yyyy HH:mm',
            dateOutputFormat: 'dd-MM-yyyy HH:mm',
            filter: false,
            filterOptions: {
              enabled: false,
            },
          },
        ]"
        :rows="data.visitorsByCategories.data"
        style-class="vgt-table"
        :theme="theme === 'dark' ? 'black-rhino' : ''"
      >
        <template #table-row="props">
          {{ props.formattedRow[props.column.field] }}
        </template>
      </vue-good-table-custom>
    </div>
    <!-- <div
      class="lg:col-span-6 md:col-span-12 sm:col-span-12 col-span-12 flex flex-col justify-center my-auto"
    >
      <figure class="highcharts-figure">
        <div id="container"></div>
      </figure>
    </div> -->
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
