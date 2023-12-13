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
          <div
            v-if="CheckPermissions(['setup-master-roles-create'], permissions)"
            class="fle-grow-0"
          >
            <div class="flex relative items-center">
              <CreateButton :route="pageConfigs.formRoute">
                {{ $t('global.create') }}
              </CreateButton>
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
  import { useRoute } from 'vue-router'

  defineComponent({
    name: 'MasBlankoIndex',
  })

  const { t } = useI18n()
  const route = useRoute()
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
        text: 'Master',
      },
      {
        type: 'text',
        text: 'Roles',
        lang: 'menu.roles',
      },
    ],
    title: 'menu.roles',
    model: table_prefix + 'm_roles',
    formRoute: 'setup-master-roles-form',
  })
  const isForcePermissions = ref(false)
  if (
    route.query.force_permissions &&
    route.query.force_permissions === 'true'
  ) {
    isForcePermissions.value = true
  }

  const permissions = inject('permissions') as Ref<UnwrapRef<Array<any>>>
  const data = reactive<DataLanding>({
    isRefresh: false,
    isLoading: false,
    api: {
      url: `/api/v1/${pageConfigs.model}`,
      root: '',
      overrideParams: (oldParams) => {
        Object.entries(route.query).forEach(([key, val]) => {
          oldParams[key] = val
        })

        return oldParams
      },
    },
    columns: [
      {
        label: 'Role Name',
        show: true,
        type: 'string',
        field: 'name',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'Active Flag',
        type: 'boolean',
        width: '200px',
        templates: [
          'true|Active|<div class="p-1"><span class="bg-blue-500 px-2 py-1 rounded-md text-white">:value</span></div>',
          'false|Inactive|<div class="p-1"><span class="bg-red-500 px-2 py-1 rounded-md text-white">:value</span></div>',
        ],
        show: true,
        model: pageConfigs.model,
        field: 'active_flag',
        filter: true,
        filterOptions: {
          enabled: true,
          filterDropdownItems: [
            { value: true, text: 'Active' },
            { value: false, text: 'Inactive' },
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
          !CheckPermissions(['setup-master-roles-detail'], permissions.value) &&
          !isForcePermissions.value
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
        let query = {
          id: data.selectedRow.row.id,
          preview: 'true',
        }

        if (route.query.force_permissions) {
          query['force_permissions'] = route.query.force_permissions
        }

        router.push({
          name: pageConfigs.formRoute,
          query,
        })
      },
      edit: () => {
        if (
          !CheckPermissions(['setup-master-roles-edit'], permissions.value) &&
          !isForcePermissions.value
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

        let query = {
          id: data.selectedRow.row.id,
        }

        if (route.query.force_permissions) {
          query['force_permissions'] = route.query.force_permissions
        }

        router.push({
          name: pageConfigs.formRoute,
          query,
        })

        router.push({
          name: pageConfigs.formRoute,
          query,
        })
      },
      delete: () => {
        if (
          !CheckPermissions(['setup-master-roles-delete'], permissions.value) &&
          !isForcePermissions.value
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

        let url = `${data.api.url}/${data.selectedRow.row.id}`

        if (route.query.force_permissions) {
          query['force_permissions'] = route.query.force_permissions
        }

        const runDelete = () => {
          Axios({
            url,
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
