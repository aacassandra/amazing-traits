<script setup lang="ts">
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { inject, provide, reactive, ref } from 'vue'
  import { PageConfigs } from '~/types/pages/form/v1'
  import DateRangefield from '~/components/atoms/form/header/DateRangefield.vue'
  import SelectfieldV2 from '~/components/atoms/form/header/SelectfieldV2.vue'
  import { Card } from '~/components/atoms'
  import { Axios, Notyf, IntToRupiah } from '~/services'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'
  import { useConfigStore } from '~/store/config'

  const configStore = useConfigStore()
  const theme = inject('theme')
  const pageConfigs = reactive<PageConfigs>({
    breadcrumb: [
      {
        type: 'text-link',
        route: '/dashboard',
        icon: '<i class="fal fa-home-alt"></i>',
        text: 'Dashboard',
      },
      {
        type: 'text',
        text: 'Calculation',
        lang: 'menu.calculation',
      },
      {
        type: 'text',
        text: 'Point Rewards',
        lang: 'menu.point_rewards',
      },
    ],
    title: 'Calculate Point Rewards',
  })

  const isReady = ref(false)
  provide('parentReady', isReady)
  const data = reactive({
    rows: [],
    column: [
      {
        label: 'field.security',
        field: 'employee',
        type: 'string',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'field.report',
        field: 'report',
        type: 'number',
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
      {
        label: 'field.visitor',
        field: 'visitor',
        type: 'number',
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
      {
        label: 'field.attendance',
        field: 'attendance',
        type: 'number',
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
      {
        label: 'field.patrol',
        field: 'patrol',
        type: 'number',
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
      {
        label: 'field.score',
        field: 'score',
        type: 'number',
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
      {
        label: 'field.incentive',
        field: 'incentive_rupiah',
        type: 'rupiah',
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
    ],
    employee: {
      value: null,
      field: {
        value: 'id',
        display: 'name',
        type: {
          value: 'encrypted',
          display: 'string',
        },
      },
      api: {
        model: 'ihm_m_users',
        root: 'data',
        parameters: {
          paginate: 25,
        },
      },
    },
    total_incentives: 0,
    incentive: 0,
    total_securities: 0,
    daterange: {
      start: '',
      end: '',
    },
  })

  const methods = {
    onCalculate: () => {
      configStore.backdrop.enabled = true
      Axios({
        method: 'POST',
        url: '/api/v1/ihm_t_point_rewards/calculate',
        data: {
          start_date: data.daterange.start,
          end_date: data.daterange.end,
        },
      })
        .then((res: any) => {
          configStore.backdrop.enabled = false
          data.rows = res.data.data.items
          data.total_incentives = res.data.data.total_incentives
          data.incentive = res.data.data.incentive
          data.total_securities = res.data.data.total_securities
        })
        .catch((err) => {
          configStore.backdrop.enabled = false
          if (
            err.response.data.errors &&
            Array.isArray(err.response.data.errors)
          ) {
            err.response.data.errors.forEach((err_message) => {
              Notyf({
                type: 'error',
                message: err_message,
                duration: 5000,
                ripple: true,
                dismissible: true,
                position: {
                  x: 'right',
                  y: 'top',
                },
              })
            })
          } else {
            Notyf({
              type: 'error',
              message: err.response.data.message,
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
    },
    onClear: () => {
      data.rows.length = 0
      data.total_incentives = 0
      data.incentive = 0
      data.total_securities = 0
    },
  }
</script>

<template>
  <div class="w-full">
    <div class="grid grid-cols-12">
      <div class="col-span-6 my-auto">
        <Breadcrumb :data="pageConfigs.breadcrumb" />
      </div>
    </div>
    <div class="grid grid-cols-12 mt-10 gap-3">
      <div class="col-span-3">
        <DateRangefield
          v-model="data.daterange"
          name="daterange"
          input="YYYY-MM-DD"
          output="DD-MM-YYYY"
          label="Daterange"
          placeholder="DD-MM-YYYY to DD-MM-YYYY"
          :from-generator="false"
        />
      </div>
      <div class="col-span-3">
        <button
          type="button"
          class="rounded-lg bg-primary hover:bg-primary-focus normal-case text-white mt-[23px] px-3 text-sm font-medium"
          style="height: 42px"
          @click="methods.onCalculate"
        >
          <i class="fa-light fa-calculator"></i> {{ $t('global.calculate') }}
        </button>
        <button
          v-if="data.rows.length"
          type="button"
          class="ml-3 rounded-lg bg-accent hover:bg-accent-focus normal-case text-white mt-[23px] px-3 text-sm font-medium"
          style="height: 42px"
          @click="methods.onClear"
        >
          <i class="fa-light fa-clock-rotate-left"></i> {{ $t('global.clear') }}
        </button>
      </div>

      <div class="col-span-12 mt-10">
        <div v-if="data.rows.length" class="mb-5">
          <div class="text-sm">
            <b>Total Incentives:</b>
            {{ IntToRupiah(data.incentive, 'Rp. ') }} x
            {{ data.total_securities }} =
            {{ IntToRupiah(data.total_incentives, 'Rp. ') }}
          </div>
        </div>
        <vue-good-table-custom
          :line-numbers="true"
          :columns="data.column"
          :rows="data.rows"
          style-class="vgt-table"
          :theme="theme === 'dark' ? 'black-rhino' : ''"
        >
          <template #table-row="props">
            {{ props.formattedRow[props.column.field] }}
          </template>
        </vue-good-table-custom>
      </div>
    </div>
  </div>
</template>
