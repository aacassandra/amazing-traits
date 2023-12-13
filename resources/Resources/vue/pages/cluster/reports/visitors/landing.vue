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
        <Landing ref="landingRef" :fixed-header="false" />
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
  /* eslint-disable @typescript-eslint/no-unused-vars,no-console */
  import {
    defineComponent,
    inject,
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

  defineComponent({
    name: 'SecVisitorsIndex',
  })

  const { t } = useI18n()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRef = ref()
  const fileImport = ref()
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
        text: 'Cluster',
        lang: 'menu.cluster',
      },
      {
        type: 'text',
        text: 'Reports',
        lang: 'menu.reports',
      },
      {
        type: 'text',
        text: 'Visitors',
        lang: 'menu.visitors',
      },
    ],
    title: 'menu.visitors',
    model: table_prefix + 't_visitors',
    formRoute: 'clusters-reports-visitors-form',
  })
  const permissions = inject('permissions') as Ref<UnwrapRef<Array<any>>>
  const data = reactive<DataLanding>({
    isRefresh: false,
    isLoading: false,
    api: {
      url: `/api/v1/${pageConfigs.model}`,
      root: '',
    },
    columns: [
      {
        label: 'Visitor',
        type: 'string',
        show: true,
        field: 'pengunjung',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Needs',
        type: 'string',
        show: true,
        field: 'keperluan.value_1',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Destination House',
        type: 'string',
        show: true,
        field: 'rumah_tujuan',
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
        dateInputFormat: 'dd/MM/yyyy HH:mm', // expects 2018-03-16
        dateOutputFormat: 'dd-MM-yyyy HH:mm', // outputs Mar 16th 2018
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
    ],
    rows: [],
    tableConfigs: {
      fixedHeader: false,
      lineNumbers: true,
      maxHeight: '400px',
    },
    totalRecords: 0,
    serverParams: {
      columnFilters: {},
      sort: [],
      page: 1,
      perPage: 10,
    },
    actions: {
      preview: () => {
        if (
          !CheckPermissions(
            ['security-transactions-visitors-preview'],
            permissions.value,
          )
        ) {
          Notyf({
            type: 'warning',
            message: "You don't have permission",
            duration: 3000,
            ripple: true,
            dismissible: true,
            position: {
              x: 'right',
              y: 'top',
            },
          })
          return false
        }

        if (!data.selectedRow) {
          Notyf({
            type: 'info',
            message: 'Select row first',
            duration: 3000,
            ripple: true,
            dismissible: true,
            position: {
              x: 'right',
              y: 'top',
            },
          })
          return false
        }
        router.push({
          name: pageConfigs.formRoute,
          query: {
            id: data.selectedRow.row.id,
            preview: 'true',
          },
        })
      },
    },
  })
  provide('data', data)
</script>
