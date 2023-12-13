<template>
  <div class="w-full">
    <div class="grid grid-cols-12">
      <div class="col-span-6 my-auto">
        <Breadcrumb :data="pageConfigs.breadcrumb" />
      </div>
    </div>

    <Card>
      <template #card-header>
        <div class="flex">
          <div class="flex-grow">
            <div
              class="text-lg text-gray-900 dark:text-white ft-dmsans-700 px-1 pt-1"
            >
              {{ $t(pageConfigs.title) }}
            </div>
          </div>
        </div>
      </template>

      <template #card-body>
        <vue-good-table-custom
          :line-numbers="true"
          :columns="data.column"
          :rows="data.rows"
          :pagination-options="{
            enabled: true,
            pagination: true,
          }"
          style-class="vgt-table"
          :theme="theme === 'dark' ? 'black-rhino' : ''"
        >
          <template #table-row="props">
            {{ props.formattedRow[props.column.field] }}
          </template>
        </vue-good-table-custom>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
  /* eslint-disable @typescript-eslint/no-unused-vars,no-console */
  import {
    defineComponent,
    inject,
    onMounted,
    provide,
    reactive,
    Ref,
    ref,
    UnwrapRef,
  } from 'vue'
  import { PageConfigs } from '~/types/pages/form/v1'
  // Components
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { Card } from '~/components/atoms'
  import CreateButton from '~/components/atoms/buttons/CreateButton.vue'
  import Landing from '~/components/organism/Landing.vue'
  import { DataLanding } from '~/types/pages'
  import { Axios, CheckPermissions, Notyf, Swal } from '~/services'
  import router from '~/router'
  import { useI18n } from 'vue-i18n'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'
  const theme = inject('theme')
  import { useConfigStore } from '~/store/config'

  defineComponent({
    name: 'HistorySubsIndex',
  })

  const { t } = useI18n()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRef = ref()
  const fileImport = ref()
  const configStore = useConfigStore()
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
        text: 'Berlangganan',
        lang: 'menu.subscriptions',
      },
      {
        type: 'text',
        text: 'Riwayat Berlangganan',
        lang: 'menu.history_subscription',
      },
    ],
    title: 'menu.reports',
    model: table_prefix + 't_subscriptions',
  })
  const permissions = inject('permissions') as Ref<UnwrapRef<Array<any>>>

  const data = reactive({
    rows: [],
    column: [
      {
        label: 'Order Waktu',
        type: 'string',
        field: 'created_at',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Order ID',
        type: 'string',
        field: 'invoice_code',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Paket',
        type: 'string',
        field: 'package.name',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Transfer (Rp)',
        type: 'rupiah',
        field: 'total',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Metode',
        type: 'string',
        field: 'payment_method.value_1',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Status',
        type: 'string',
        field: 'status',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
    ],
  })
  provide('data', data)

  onMounted(() => {
    Axios({
      method: 'GET',
      url: `/api/v1/${pageConfigs.model}`,
    })
      .then((res: any) => {
        console.log(res.data.data)
        data.rows = res.data.data

        data.rows.forEach((row: any) => {
          if (row.status == 'PROGRESS') {
            row.status = 'Masih Menunggu'
          } else {
            row.status = 'Berhasil'
          }

          row.total = 'Rp. ' + Intl.NumberFormat().format(row.total)
        })
      })
      .catch((err) => {
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
  })
</script>
