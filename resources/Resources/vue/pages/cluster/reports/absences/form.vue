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
            <BackButton
              :route="isApproval ? 'approval' : pageConfigs.landingRoute"
              >{{ $t('global.back') }}</BackButton
            >
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
        <Tabs :items="tabs">
          <template #tab1>
            <FormDetail
              ref="formDetailRefsTab1"
              v-model:form="schema.forms[1]"
              class="container mx-auto px-0 my-4"
            />
          </template>
        </Tabs>
        <div class="grid grid-cols-12 px-1 mt-4 pb-4">
          <div class="col-span-6 pr-3">
            <div
              class="block text-xs font-medium text-gray-900 dark:text-gray-300 mb-3"
            >
              {{ $t('field.approval_log') }}
            </div>
            <vue-good-table-custom
              :columns="detailLog.column"
              :rows="detailLog.rows"
              style-class="vgt-table"
              :theme="theme === 'dark' ? 'black-rhino' : ''"
            >
              <template #table-row="props">
                {{ props.formattedRow[props.column.field] }}
              </template>
            </vue-good-table-custom>
          </div>
          <div class="col-span-6">
            <TextAreafield
              v-model="detailLog.note"
              :disabled="
                !btnApproval.reject ||
                !btnApproval.revise ||
                !btnApproval.approve
              "
              name="note"
              label="field.reason"
              information="information.approval_reason"
            />
          </div>
        </div>
      </template>
      <template
        v-if="
          (pageConfigs.mode !== 'preview' || isApproval) && stayEnabledFooter
        "
        #card-footer
      >
        <div v-if="isApproval" class="grid grid-cols-12">
          <div class="col-span-6">
            <RejectButton
              v-if="btnApproval.reject"
              :on-click="methods.onReject"
            >
              {{ $t('global.reject') }}
            </RejectButton>
          </div>
          <div class="col-span-6 flex justify-end">
            <ReviseButton
              v-if="btnApproval.revise"
              :on-click="methods.onRevise"
              class="mr-3"
            >
              {{ $t('global.revise') }}
            </ReviseButton>
            <SubmitButton
              v-if="btnApproval.approve"
              :on-click="methods.onApprove"
            >
              {{ $t('global.approve') }}
            </SubmitButton>
          </div>
        </div>
        <div v-else class="grid grid-cols-12">
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
    provide,
    reactive,
    ref,
    inject,
  } from 'vue'
  import { useRoute } from 'vue-router'
  import { PageConfigs } from '~/types/pages/form/v1'
  // Components
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { Card } from '~/components/atoms'
  import FormHeader from '~/components/organism/FormHeader/index.vue'
  import { Schema } from '~/types'
  import SubmitButton from '~/components/atoms/buttons/SubmitButton.vue'
  import RejectButton from '~/components/atoms/buttons/RejectButton.vue'
  import ReviseButton from '~/components/atoms/buttons/ReviseButton.vue'
  import BackButton from '~/components/atoms/buttons/BackButton.vue'
  import { Axios, Notyf, Preparation, Swal, Transform, t } from '~/services'
  import { useConfigStore } from '~/store/config'
  import router from '~/router'
  import { TabsType } from '~/types/components/molecules/tabs'
  import FormDetail from '~/components/organism/FormDetail/index.vue'
  import TextAreafield from '~/components/atoms/form/header/TextAreafield.vue'
  import { Tabs } from '~/components/molecules'
  import VueGoodTableCustom from '~/components/molecules/table/Table.vue'

  const theme = inject('theme')
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'clusters-reports-absences'
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
        text: 'Attendances',
        lang: 'menu.attendances',
        route: landingRoute,
      },
    ],
    title: '',
    model: table_prefix + 't_absences',
    landingRoute,
    mode: '',
  })

  defineComponent({
    name: 'AbsencesForm',
  })

  const formHeaderRefs = ref()
  const isSaving = ref(false)
  const route = useRoute()
  const isApproval = ref(false)
  const stayEnabledFooter = ref(true)
  isApproval.value = route.query.approval && route.query.approval === 'true'
  const btnApproval = reactive({
    revise: false,
    reject: false,
    approve: false,
  })

  const tabs = ref<TabsType>([
    {
      key: 'tab1',
      label: 'tab.additional_images',
      icon: '<i class="fal fa-image mr-1"></i>',
      active: true,
    },
  ])

  const detailLog = reactive({
    rows: [],
    column: [
      {
        label: 'field.action_user',
        field: 'action_user.name',
        type: 'string',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'field.action_type',
        field: 'action',
        type: 'string',
        filter: true,
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
      {
        label: 'field.action_at',
        show: true,
        type: 'date',
        width: '150px',
        field: 'action_at',
        dateInputFormat: 'yyyy-MM-dd HH:mm:ss', // expects 2018-03-16
        dateOutputFormat: 'dd-MM-yyyy HH:mm', // outputs Mar 16th 2018
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
      {
        label: 'field.reason',
        field: 'action_note',
        type: 'string',
        filter: true,
        filterOptions: {
          enabled: true,
        },
      },
    ],
    note: '',
  })

  if (route.query.id) {
    if (route.query.preview) {
      pageConfigs.mode = 'preview'
      pageConfigs.title = isApproval.value
        ? 'global.approval_data'
        : 'global.preview_data'
      pageConfigs.breadcrumb[3].lang = isApproval.value
        ? 'global.approval_data'
        : 'global.preview_data'
    } else {
      pageConfigs.mode = 'edit'
      pageConfigs.title = 'global.edit_data'
      pageConfigs.breadcrumb[3].lang = 'global.edit_data'
    }
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
        model: table_prefix + 't_absences',
        key: 'change-data',
        ready: 0,
        docs: false,
        properties: {
          pengunjung: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Nama Pengunjung',
              information: '',
              inline: true,
              placeholder: 'Nama pengunjung',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: [],
          },
          cluster_id: {
            type: 'selectfield',
            version: 2,
            value: null,
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.cluster',
              information: 'Nama klaster',
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
            rules: [],
          },
          role_id: {
            type: 'selectfield',
            version: 2,
            value: null,
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.role',
              information: 'Nama Peran',
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
                model: table_prefix + 'm_roles',
                root: 'data',
                parameters: {
                  paginate: 25,
                },
                cache: false,
              },
            },
            rules: [],
          },
          date: {
            type: 'datefield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.date',
              information: 'Tanggal ijin',
              inline: true,
              placeholder: '',
              readonly: false,
              input: 'YYYY-MM-DD',
              output: 'DD-MM-YYYY',
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: [],
          },
          type_id: {
            type: 'selectfield',
            version: 2,
            value: null,
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.absence_type',
              information: 'Tipe ijin',
              inline: true,
              placeholder: 'Select one',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                value: 'id',
                display: 'value_1',
                type: {
                  value: 'encrypted',
                  display: 'string',
                },
              },
              api: {
                model: table_prefix + 'm_general',
                root: 'data',
                parameters: {
                  paginate: 25,
                },
                cache: false,
              },
            },
            rules: [],
          },
          code: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.code',
              information: '',
              inline: true,
              placeholder: 'field.code',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: [],
          },
          errors: null,
        },
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_t_absences_d_images',
        },
        addRow: {
          from: 'empty',
          active: pageConfigs.mode !== 'preview',
          disabled: false,
        },
        clearAllRow: {
          active: pageConfigs.mode !== 'preview',
          disabled: false,
        },
        tableConfigs: {
          fixedHeader: false,
          maxHeight: '255px',
        },
        properties: [
          {
            field: 'url',
            type: 'editor',
            label: 'URL',
            tdClass: 'custom-title',
            editor: {
              type: 'filefield',
              default: null,
              options: {
                clearable: true,
                viewer: 'image',
                placeholder: 'Upload an image',
                readonly: false,
                disabled: pageConfigs.mode === 'preview',
              },
              rules: ['required', 'mimes:jpg,png,jpeg,webp,gif'],
            },
          },
          {
            type: 'action',
            width: '80px',
            label: 'field.action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index) => {
                  if (schema.forms[1].type === 'detail') {
                    schema.forms[1].rows.splice(index, 1)
                  }
                },
              },
            },
          },
        ],
        transform: [],
        includes: [],
        rows: [],
      },
    ],
  }) as Schema
  provide('schema', schema)

  const configStore = useConfigStore()
  const methods = {
    onLoadData: () => {
      let url = `/api/v1/${pageConfigs.model}/${route.query.id}`
      if (isApproval.value) {
        url += '?is_approval=true'
      }
      Axios({ url })
        .then((res: any) => {
          if (isApproval.value) {
            detailLog.rows = res.data.data.approval.logs
            btnApproval.revise = res.data.data.approval.revise
            btnApproval.reject = res.data.data.approval.reject
            btnApproval.approve = res.data.data.approval.approve

            if (
              !btnApproval.revise ||
              !btnApproval.reject ||
              !btnApproval.approve
            ) {
              stayEnabledFooter.value = false
            }
          }

          Object.entries(res.data.data).forEach(([key, val]) => {
            Transform(schema, val, key)
          })
          configStore.backdrop.enabled = false
        })
        .catch((err) => {
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

      let html = t('alert.store_data.html')
      if (pageConfigs.mode === 'edit') {
        html = t('alert.update_data.html')
      }

      Swal.confirm({
        title: t('global.confirmation'),
        html,
        icon: 'question',
        button: {
          confirm: 'primary',
          size: 'md',
        },
        showCancelButton: true,
        confirmButtonText: t('global.yes'),
        cancelButtonText: t('global.cancel'),
      }).then((result: any) => {
        if (result.isConfirmed) {
          Preparation(schema).then((json) => {
            methods.onSend(json)
          })
        }
      })
    },
    onAction: (action: string) => {
      const send = () => {
        const url = `/api/v1/${table_prefix}e_approval/change_status`
        Axios({
          method: 'POST',
          url,
          data: {
            trx_id: route.query.id,
            trx_name: 'Surat Izin Absensi',
            trx_table: pageConfigs.model,
            action,
            action_note: detailLog.note,
          },
        })
          .then((res: any) => {
            detailLog.note = ''
            btnApproval.reject = false
            btnApproval.revise = false
            btnApproval.approve = false
            Notyf({
              type: 'success',
              message: t(res.data.message),
              duration: 3000,
              ripple: true,
              dismissible: true,
              position: {
                x: 'right',
                y: 'top',
              },
            })
          })
          .catch((err) => {
            //
          })
      }
      Swal.confirm({
        title: t('global.confirmation'),
        html: t(`alert.${action.toLowerCase()}_data.html`),
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
          send()
        }
      })
    },
    onReject: () => {
      methods.onAction('REJECTED')
    },
    onRevise: () => {
      methods.onAction('REVISED')
    },
    onApprove: () => {
      methods.onAction('APPROVED')
    },
  }

  onMounted(() => {
    if (route.query.id) {
      configStore.backdrop.enabled = true
      methods.onLoadData()
    }
  })
</script>
