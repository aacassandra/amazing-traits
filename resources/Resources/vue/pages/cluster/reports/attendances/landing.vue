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
    name: 'SecAttendancesIndex',
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
        text: 'Attendances',
        lang: 'menu.attendances',
      },
    ],
    title: 'menu.attendances',
    model: table_prefix + 't_security_attendances',
    formRoute: 'clusters-reports-attendances-form',
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
        label: 'Security Staff',
        type: 'string',
        show: true,
        field: 'security.name',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Housing Name',
        type: 'string',
        show: true,
        field: 'ihm_m_housings.name',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Cluster Name',
        type: 'string',
        show: true,
        field: 'cluster.name',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Checkin Time',
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
      {
        label: 'Checkout Time',
        show: true,
        type: 'date',
        width: '150px',
        field: 'updated_at',
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
    actions: {},
  })
  provide('data', data)
</script>
