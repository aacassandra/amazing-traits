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
    name: 'TransReportsIndex',
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
        text: 'App Reports',
        lang: 'menu.app_reports',
      },
    ],
    title: 'menu.reports',
    model: table_prefix + 't_app_reports',
    formRoute: 'cluster-transaction-app_reports-form',
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
        label: 'Title',
        show: true,
        model: pageConfigs.model,
        type: 'string',
        field: 'title',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Description',
        type: 'string',
        show: true,
        model: pageConfigs.model,
        field: 'description',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Ticket Number',
        type: 'string',
        show: true,
        model: pageConfigs.model,
        field: 'ticket_number',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Status',
        type: 'string',
        show: true,
        model: pageConfigs.model,
        field: 'status',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Reported By',
        type: 'string',
        show: true,
        model: pageConfigs.model,
        field: 'created.name',
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
        if (!CheckPermissions([], permissions.value)) {
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
      delete: () => {
        if (!CheckPermissions([], permissions.value)) {
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

        const runDelete = () => {
          Axios({
            url: `${data.api.url}/${data.selectedRow.row.id}`,
            method: 'DELETE',
          })
            .then((res: any) => {
              Notyf({
                type: 'success',
                message: res.data.message,
                duration: 3000,
                ripple: true,
                dismissible: true,
                position: {
                  x: 'right',
                  y: 'top',
                },
              })

              landingRef.value.methods.loadItems()
            })
            .catch((err) => {
              console.log(err.response)
            })
        }

        Swal.confirm({
          title: t('alert.delete_row.title'),
          html: t('alert.delete_row.html'),
          icon: 'warning',
          button: {
            confirm: 'primary',
            size: 'md',
          },
          showCancelButton: true,
          confirmButtonText: t('global.yes'),
          cancelButtonText: t('global.cancel'),
        }).then((result: any) => {
          if (result.isConfirmed) {
            runDelete()
          }
        })
      },
    },
  })
  provide('data', data)
</script>
