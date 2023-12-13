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
        text: 'Absence Permit',
        lang: 'menu.absence_permit',
      },
    ],
    title: 'menu.absence_permit',
    model: table_prefix + 't_absences',
    formRoute: 'clusters-reports-absences-form',
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
        label: 'code',
        type: 'string',
        show: true,
        field: 'code',
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
        label: 'Role',
        type: 'string',
        show: true,
        field: 'role.name',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Date',
        show: true,
        type: 'date',
        width: '150px',
        field: 'date',
        dateInputFormat: 'yyyy-MM-dd', // expects 2018-03-16
        dateOutputFormat: 'dd-MM-yyyy', // outputs Mar 16th 2018
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Type',
        type: 'string',
        show: true,
        field: 'type.value_1',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Contact Number',
        type: 'string',
        show: true,
        field: 'contact_number',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Updated At',
        show: true,
        type: 'date',
        width: '150px',
        field: 'updated_at',
        dateInputFormat: 'dd/MM/yyyy HH:mm', // expects 2018-03-16
        dateOutputFormat: 'dd-MM-yyyy HH:mm', // outputs Mar 16th 2018
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Status',
        model: 'ihm_t_absences',
        width: '170px',
        type: 'string',
        show: true,
        field: 'status',
        filter: true,
        templates: [
          'PROGRESS|<div class="p-1"><span class="bg-orange-500 px-2 py-1 rounded-md text-white">:value</span></div>',
          'REVISED|<div class="p-1"><span class="bg-orange-500 px-2 py-1 rounded-md text-white">:value</span></div>',
          'REJECTED|<div class="p-1"><span class="bg-red-500 px-2 py-1 rounded-md text-white">:value</span></div>',
          'APPROVED|<div class="p-1"><span class="bg-green-500 px-2 py-1 rounded-md text-white">:value</span></div>',
        ],
        filterOptions: {
          enabled: true,
          filterDropdownItems: [
            { value: 'PROGRESS', text: 'PROGRESS' },
            { value: 'REVISED', text: 'REVISED' },
            { value: 'REJECTED', text: 'REJECTED' },
            { value: 'APPROVED', text: 'APPROVED' },
          ],
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
            ['cluster-reports-absences-preview'],
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
