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
            <BackButton :route="pageConfigs.landingRoute">{{
              $t('global.back')
            }}</BackButton>
          </div>
          <div class="fle-grow-0">
            <div class="flex relative items-center">
              <div
                class="text-lg text-gray-900 dark:text-white ft-dmsans-700 px-1"
              >
                {{ $t(pageConfigs.title) }}
              </div>
            </div>
          </div>
        </div>
      </template>

      <template #card-body>
        <FormHeader
          ref="formHeaderRefs"
          v-model:form="schema.forms[0]"
          class="mt-3 mb-5"
          mode="create"
        />
        <Tabs :items="pageConfigs.tabs">
          <template #tab1>
            <FormDetail
              ref="formDetailRefsTab1"
              v-model:form="schema.forms[1]"
              class="container mx-auto px-0 my-4"
            />
          </template>
        </Tabs>
      </template>
      <template v-if="pageConfigs.mode !== 'preview'" #card-footer>
        <div class="grid grid-cols-12">
          <div class="col-span-12 flex justify-end">
            <SubmitButton :is-loading="isSaving" :on-click="methods.onSubmit">
              {{ pageConfigs.mode === 'create' ? 'Save' : 'Update' }}
            </SubmitButton>
          </div>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
  import {
    defineComponent,
    onMounted,
    onUnmounted,
    provide,
    reactive,
    ref,
  } from 'vue'
  import { useRoute } from 'vue-router'
  import { PageConfigs } from '~/types/pages/form/v1'
  // Components
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { Card } from '~/components/atoms'
  import FormHeader from '~/components/organism/FormHeader/index.vue'
  import FormDetail from '~/components/organism/FormDetail/index.vue'
  import { Schema } from '~/types'
  import SubmitButton from '~/components/atoms/buttons/SubmitButton.vue'
  import BackButton from '~/components/atoms/buttons/BackButton.vue'
  import {
    Axios,
    Cryptor,
    Notyf,
    Preparation,
    Swal,
    Transform,
  } from '~/services'
  import { useConfigStore } from '~/store/config'
  import router from '~/router'
  import { Tabs } from '~/components/molecules'
  import Pusher from 'pusher-js'
  import Echo from 'laravel-echo'
  import { useWebsocketStore } from '~/store/websocket'

  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'clusters-reports-patrols'
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
        type: 'text-link',
        text: 'Patrols',
        lang: 'menu.patrols',
        route: landingRoute,
      },
    ],
    title: '',
    model: table_prefix + 't_log_patrols',
    landingRoute,
    mode: '',
    tabs: [
      {
        key: 'tab1',
        label: 'Detail',
        icon: '<i class="fal fa-user-group"></i>',
        active: true,
      },
    ],
  })

  defineComponent({
    name: 'SecPatrolsForm',
  })

  const formHeaderRefs = ref()
  const formDetailRefsTab1 = ref()
  const isSaving = ref(false)
  const route = useRoute()
  const headerId = ref('')

  if (route.query.id) {
    if (route.query.preview) {
      pageConfigs.mode = 'preview'
      pageConfigs.title = 'global.preview_data'
      pageConfigs.breadcrumb[3].lang = 'global.preview_data'
    } else {
      pageConfigs.mode = 'edit'
      pageConfigs.title = 'global.edit_data'
      pageConfigs.breadcrumb[3].lang = 'global.edit_data'
    }
    headerId.value = Cryptor.decrypt(route.query.id)
  } else {
    pageConfigs.title = 'global.create_data'
    pageConfigs.mode = 'create'
    pageConfigs.breadcrumb[3].lang = 'global.create_data'
  }

  const schema = reactive({
    version: 1,
    mode: pageConfigs.mode,
    forms: [
      {
        type: 'header',
        model: pageConfigs.model,
        key: 'form',
        ready: 0,
        docs: false,
        properties: {
          cluster_id: {
            type: 'selectfield',
            version: 2,
            value: '',
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.cluster',
              information: '',
              inline: true,
              placeholder: 'Select one',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                value: 'id',
                display: 'name',
                type: {
                  value: 'encrypted',
                  display: 'string',
                },
              },
              api: {
                model: table_prefix + 'm_clusters',
                root: 'data',
                parameters: {
                  paginate: 25,
                },
                cache: false,
              },
            },
            listener: () => {
              //
            },
            rules: [],
          },
          duration: {
            type: 'timefield',
            value: '',
            default: 'now',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.duration',
              information: 'Start patroli',
              inline: true,
              placeholder: 'HH:mm',
              input: 'HH:mm:ss',
              output: 'HH:mm',
              in24h: true,
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: ['required'],
          },
          start_patrol_at: {
            type: 'datetimefield',
            value: '',
            default: 'now',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.start_at',
              information: 'Start patroli',
              inline: true,
              placeholder: 'DD-MM-YYYY HH:mm',
              input: 'YYYY-MM-DD HH:mm:ss',
              output: 'DD-MM-YYYY HH:mm',
              in24h: true,
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: ['required'],
          },
          finish_patrol_at: {
            type: 'datetimefield',
            value: '',
            default: 'now',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.finish_at',
              information: 'Start patroli',
              inline: true,
              placeholder: 'DD-MM-YYYY HH:mm',
              input: 'YYYY-MM-DD HH:mm:ss',
              output: 'DD-MM-YYYY HH:mm',
              in24h: true,
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: ['required'],
          },
          errors: null,
        },
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_t_log_patrols_d',
        },
        addRow: {
          from: 'popup',
          active: pageConfigs.mode !== 'preview',
          disabled: true,
          options: {
            isMultiple: true,
            globalSearch: false,
            className: {
              addNewList: 'text-white',
              verivy: 'text-white',
            },
            tableConfigs: {
              maxHeight: '300px',
              fixedHeader: true,
              selectAll: false,
            },
            uniqueColumn: 'id',
            includes: ['id:ihm_t_log_patrols_id'],
            columns: [
              {
                label: 'Name',
                field: 'name',
                model: 'ihm_m_users',
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
              {
                label: 'Phone',
                field: 'phone',
                model: 'ihm_m_users',
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
              {
                label: 'Email',
                field: 'email',
                model: 'ihm_m_users',
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
              {
                type: 'image',
                style: { width: '30px', height: '30px', borderRadius: '50%' },
                label: 'Photo',
                field: 'avatar',
              },
            ],
            api: {
              model: 'ihm_t_log_patrols_d',
              root: 'data',
              parameters: {
                paginate: 25,
              },
              cache: false,
            },
          },
        },
        clearAllRow: {
          active: pageConfigs.mode !== 'preview',
          disabled: false,
        },
        tableConfigs: {
          fixedHeader: false,
          maxHeight: '255px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'name',
            type: 'string',
            label: 'field.security_name',
          },
          {
            field: 'radius_patrol',
            type: 'string',
            label: 'field.patrol_radius',
          },
          {
            field: 'distance',
            type: 'string',
            label: 'field.distance',
          },
          {
            field: 'in_radius',
            type: 'boolean',
            templates: [
              'true|value.yes|<div class="p-1"><span class="bg-green-500 px-2 py-1 rounded-md text-white"><i class="fa-duotone fa-check mr-2"></i> :value</span></div>',
              'false|value.no|<div class="p-1"><span class="bg-red-500 px-2 py-1 rounded-md text-white"><i class="fa-sharp fa-solid fa-circle-exclamation mr-2"></i>:value</span></div>',
              'null|-|:value',
            ],
            label: 'field.inside_radius',
          },
          {
            field: 'security.avatar',
            fieldText: 'security.name',
            type: 'image',
            label: 'Avatar',
            style: { width: '30px', height: '30px', borderRadius: '50%' },
          },
          {
            label: 'field.checkpoint_at',
            type: 'date',
            width: '150px',
            field: 'created_at',
            dateInputFormat: 'dd/MM/yyyy HH:mm',
            dateOutputFormat: 'dd-MM-yyyy HH:mm',
          },
        ],
        transform: ['name:security.name'],
        includes: [],
        rows: [],
      },
    ],
  }) as Schema
  provide('schema', schema)

  const configStore = useConfigStore()
  const methods = {
    onLoadData: () => {
      Axios({
        url: `/api/v1/${pageConfigs.model}/${route.query.id}`,
      })
        .then((res: any) => {
          Object.entries(res.data.data).forEach(([key, val]) => {
            Transform(schema, val, key)
          })
          configStore.backdrop.enabled = false
        })
        .catch((err) => {
          console.log(err)
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
        })
    },
    onSend: (json) => {
      for (let i = 0; i < schema.forms.length; i++) {
        const form = schema.forms[i]
        if (form.type === 'header') {
          isSaving.value = true

          let url = `/api/v1/${form.model}`
          let method = 'POST'
          if (pageConfigs.mode === 'edit') {
            url += `/${route.query.id}`
            method = 'PUT'
          }

          Axios({
            url,
            data: json[form.key][form.model],
            method,
          })
            .then((res: any) => {
              isSaving.value = false
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

              router.push({ name: pageConfigs.landingRoute })
            })
            .catch((err) => {
              isSaving.value = false
              if ([400, 401, 402, 404, 422].includes(err.response.status)) {
                if (err.response.data.data && err.response.data.data.length) {
                  err.response.data.data.forEach((dt) => {
                    Notyf({
                      type: 'error',
                      message: dt,
                      duration: 3000,
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
                    message: err.response.statusText,
                    duration: 3000,
                    ripple: true,
                    dismissible: true,
                    position: {
                      x: 'right',
                      y: 'top',
                    },
                  })
                }
              }
            })
        }
      }
    },
    onSubmit: () => {
      let errors = 0
      errors += formHeaderRefs.value.getValid()
      errors += formDetailRefsTab1.value.getValidRows()

      if (errors) {
        Notyf({
          type: 'error',
          message: `Please correct your form`,
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

      let html = 'Are you sure to saving this data, click Yes to continue'
      if (pageConfigs.mode === 'edit') {
        html = 'Are you sure? save this change, click Yes to continue'
      }
      Swal.confirm({
        title: 'Confirmation',
        html,
        icon: 'question',
        button: {
          confirm: 'primary',
          size: 'md',
        },
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
      }).then((result: any) => {
        if (result.isConfirmed) {
          Preparation(schema).then((json) => {
            const debug = false
            if (!debug) {
              methods.onSend(json)
            } else {
              console.log(json)
            }
          })
        }
      })
    },
  }

  const webscoketStore = useWebsocketStore()
  onMounted(() => {
    if (route.query.id) {
      configStore.backdrop.enabled = true
      methods.onLoadData()

      webscoketStore.echo
        .private(`private_channel.${headerId.value}`)
        .listen('.ihm_t_log_patrols', (e) => {
          if (e.action === 'updated') {
            Object.entries(e.data).forEach(([key, val]) => {
              Transform(schema, val, key)
            })
          }
        })
    }
  })

  onUnmounted(() => {
    if (pageConfigs.mode === 'preview') {
      webscoketStore.echo.leave(`private_channel.${headerId.value}`)
    }
  })
</script>
