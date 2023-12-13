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
    name: 'SecPatrolIndex',
  })

  const { t } = useI18n()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRef = ref()
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
        text: 'Patrols',
        lang: 'menu.patrols',
      },
    ],
    title: 'menu.patrols',
    model: table_prefix + 't_log_patrols',
    formRoute: 'clusters-reports-patrols-form',
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
        label: 'Security',
        type: 'string',
        show: true,
        field: 'created.name',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Cluster',
        type: 'number',
        show: true,
        field: 'cluster.name',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Start At',
        show: true,
        type: 'date',
        field: 'start_patrol_at',
        dateInputFormat: 'yyyy-MM-dd HH:mm:ss',
        dateOutputFormat: 'dd-MM-yyyy HH:mm',
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
      {
        label: 'Finish At',
        show: true,
        type: 'date',
        field: 'finish_patrol_at',
        dateInputFormat: 'yyyy-MM-dd HH:mm:ss',
        dateOutputFormat: 'dd-MM-yyyy HH:mm',
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
      {
        label: 'Duration',
        show: true,
        type: 'date',
        field: 'duration',
        dateInputFormat: 'HH:mm:ss',
        dateOutputFormat: 'HH:mm:ss',
        filter: false,
        filterOptions: {
          enabled: false,
        },
      },
      {
        label: 'Finished',
        type: 'boolean',
        templates: [
          'true|Finished|<div class="p-1"><span class="bg-green-500 px-2 py-1 rounded-md text-white">:value</span></div>',
          'false|On going|<div class="p-1"><span class="bg-yellow-500 px-2 py-1 rounded-md text-white">:value</span></div>',
        ],
        show: true,
        field: 'finished',
        filter: true,
        filterOptions: {
          enabled: true,
          filterDropdownItems: [
            { value: true, text: 'Finished' },
            { value: false, text: 'On going' },
          ],
        },
      },
    ],
    rows: [],
    tableConfigs: {
      fixedHeader: true,
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
            ['security-transactions-patrols-preview'],
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
