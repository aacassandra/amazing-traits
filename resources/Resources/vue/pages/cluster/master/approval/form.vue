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
        <Tabs :items="tabs">
          <template #tab1>
            <FormDetail
              ref="formDetailRefsTab1"
              v-model:form="schema.forms[1]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab2>
            <FormDetail
              ref="formDetailRefsTab2"
              v-model:form="schema.forms[2]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab3>
            <FormDetail
              ref="formDetailRefsTab3"
              v-model:form="schema.forms[3]"
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
  import { defineComponent, onMounted, provide, reactive, ref } from 'vue'
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
    Notyf,
    Preparation,
    SendError,
    Swal,
    Transform,
  } from '~/services'
  import { useConfigStore } from '~/store/config'
  import router from '~/router'
  import { useI18n } from 'vue-i18n'
  import { Tabs } from '~/components/molecules'
  import { TabsType } from '~/types/components/molecules/tabs'
  import table from '~/components/molecules/table/Table.vue'

  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'cluster-master-approval'
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
        text: 'Setup',
      },
      {
        type: 'text',
        text: 'Master',
      },
      {
        type: 'text-link',
        text: 'Approval',
        lang: 'menu.approval',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 'm_approval',
    landingRoute,
    mode: '',
  })

  defineComponent({
    name: 'MasApprovalForm',
  })

  const { t } = useI18n()
  const formHeaderRefs = ref()
  const formDetailRefsTab1 = ref()
  const formDetailRefsTab2 = ref()
  const formDetailRefsTab3 = ref()
  const isSaving = ref(false)
  const initEdit = ref(false)
  const route = useRoute()

  const tabs = ref<TabsType>([
    {
      key: 'tab1',
      label: 'Detail Rules',
      icon: '<i class="fal fa-sitemap"></i>',
      active: true,
    },
    {
      key: 'tab2',
      label: 'Detail Configs',
      icon: '<i class="fa-light fa-sliders"></i>',
      active: false,
    },
    {
      key: 'tab3',
      label: 'Detail Excludes',
      icon: '<i class="fa-light fa-object-exclude"></i>',
      active: false,
    },
  ])

  if (route.query.id) {
    if (route.query.preview) {
      pageConfigs.mode = 'preview'
      pageConfigs.title = 'global.preview_data'
      pageConfigs.breadcrumb[4].lang = 'global.preview_data'
    } else {
      pageConfigs.mode = 'edit'
      pageConfigs.title = 'global.edit_data'
      pageConfigs.breadcrumb[4].lang = 'global.edit_data'
    }
  } else {
    pageConfigs.title = 'global.create_data'
    pageConfigs.mode = 'create'
    pageConfigs.breadcrumb[4].lang = 'global.create_data'
  }

  const schema = reactive({
    version: 1,
    mode: pageConfigs.mode,
    forms: [
      {
        type: 'header',
        model: table_prefix + 'm_approval',
        key: 'form',
        ready: 0,
        docs: false,
        properties: {
          company_id: {
            type: 'selectfield',
            version: 2,
            value: null,
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.cluster',
              information: '',
              inline: true,
              placeholder: 'Select one',
              readonly: false,
              disabled: true,
              hidden: true,
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
            rules: ['required'],
          },
          name: {
            type: 'selectfield',
            version: 2,
            value: null,
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.name',
              information: '',
              inline: true,
              placeholder: 'Select one',
              readonly: false,
              disabled: true,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                value: 'value_1',
                display: 'value_1',
                type: {
                  value: 'string',
                  display: 'string',
                },
              },
              api: {
                model: table_prefix + 'm_general',
                root: 'data',
                parameters: {
                  paginate: 25,
                  where: "`group` = 'APPROVAL KEY'",
                },
                cache: false,
              },
            },
            rules: ['required'],
          },
          menu_id: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Menu',
              information: '',
              inline: true,
              placeholder: 'Select one',
              readonly: false,
              disabled: true,
              hidden: false,
              multiple: false,
              clearable: true,
              tableConfigs: {
                fixedHeader: false,
                maxHeight: '455px',
                lineNumbers: true,
              },
              field: {
                value: 'id',
                display: 'name',
                columns: [
                  {
                    label: 'Menu',
                    type: 'string',
                    model: table_prefix + 'm_menu',
                    field: 'name',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Module',
                    type: 'string',
                    model: table_prefix + 'm_menu',
                    field: 'module',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Submodule',
                    type: 'string',
                    model: table_prefix + 'm_menu',
                    field: 'submodule',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Path URL',
                    type: 'string',
                    model: table_prefix + 'm_menu',
                    field: 'path_url',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                ],
                type: {
                  value: 'encrypted',
                  display: 'string',
                },
              },
              api: {
                model: table_prefix + 'm_menu',
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
            rules: ['required'],
          },
          information: {
            type: 'textareafield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'field.information',
              information: '',
              inline: true,
              placeholder: 'field.information',
              readonly: false,
              disabled: true,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: [],
          },
          active_flag: {
            type: 'switchfield',
            value: false,
            default: true,
            col: 'col-span-12 lg:col-span-1',
            options: {
              label: 'Active Flag',
              information:
                'Jika dimatikan maka data tidak akan bisa digunakan sementara',
              inline: true,
              disabled: pageConfigs.mode === 'preview',
              hidden: true,
            },
          },
          errors: null,
        },
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: table_prefix + 'm_approval_d_rules',
        },
        addRow: {
          from: 'empty',
          active: true,
          disabled: false,
          className: 'text-white',
        },
        clearAllRow: {
          active: pageConfigs.mode !== 'preview',
          disabled: false,
          exceptRowIndex: 0,
        },
        tableConfigs: {
          fixedHeader: true,
          maxHeight: '255px',
        },
        properties: [
          {
            field: 'level',
            type: 'editor',
            width: '180px',
            label: 'field.level',
            editor: {
              type: 'numberfield',
              default: 0,
              options: {
                placeholder: 'Level',
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) => {
                if (type === 'init') {
                  const { rowIndex, getValues, setValue } = element
                  if (rowIndex > 0) {
                    if (route.query.id && !initEdit.value) {
                      return
                    }

                    const prevRow = getValues(rowIndex - 1)
                    setValue({
                      field: 'level',
                      value: prevRow.level + 1,
                    })
                  } else {
                    element.setDisabled({ field: 'level', value: true })
                    element.setDisabled({ field: 'level_order', value: true })
                    element.setDisabled({ field: 'type', value: true })
                  }
                } else if (type === 'change') {
                  const { rowIndex } = element
                  setTimeout(() => {
                    if (schema.forms[2].type === 'detail') {
                      schema.forms[2].rows[rowIndex - 1].level = value
                    }
                  }, 100)
                }
              },
              rules: ['required'],
            },
          },
          {
            field: 'level_order',
            type: 'editor',
            width: '180px',
            label: 'field.levelOrder',
            editor: {
              type: 'numberfield',
              default: 0,
              options: {
                placeholder: 'Level Order',
                readonly: false,
                disabled: false,
              },
              rules: ['required'],
              listener: (element, type, value) => {
                if (type === 'init') {
                  const { rowIndex, getValues, setValue } = element
                  if (rowIndex > 0) {
                    if (route.query.id && !initEdit.value) {
                      return
                    }

                    const prevRow = getValues(rowIndex - 1)
                    setValue({
                      field: 'level_order',
                      value: prevRow.level_order + 1,
                    })
                  }
                }
              },
            },
          },
          {
            field: 'type',
            type: 'editor',
            width: '230px',
            label: 'field.type',
            editor: {
              type: 'selectfield',
              version: 2,
              default: '',
              options: {
                placeholder: 'Select one',
                items: ['MENGAJUKAN', 'MENGETAHUI', 'MENYETUJUI'],
                disabledItems: ['MENGAJUKAN'],
                readonly: false,
                disabled: false,
                multiple: false,
                clearable: true,
              },
              listener: () => {
                //
              },
              rules: ['required'],
            },
          },
          {
            field: 'role_id',
            type: 'editor',
            width: '230px',
            label: 'field.role',
            editor: {
              type: 'selectfield',
              version: 2,
              default: '',
              options: {
                placeholder: 'Select one',
                readonly: false,
                disabled: false,
                multiple: false,
                clearable: true,
                field: {
                  value: 'id',
                  display: 'name',
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
              listener: () => {
                //
              },
              rules: ['required'],
            },
          },
          {
            field: 'user_id',
            type: 'editor',
            width: '230px',
            label: 'field.user',
            editor: {
              type: 'selectfield',
              version: 2,
              default: '',
              options: {
                placeholder: 'Select one',
                readonly: false,
                disabled: false,
                multiple: false,
                clearable: true,
                field: {
                  value: 'id',
                  display: 'name',
                },
                api: {
                  model: table_prefix + 'm_users',
                  root: 'data',
                  parameters: {
                    paginate: 25,
                    scopes: 'filter_by_cluster',
                  },
                  cache: false,
                },
              },
              listener: () => {
                //
              },
              rules: [],
            },
          },
          {
            field: 'action',
            type: 'action',
            width: '80px',
            label: 'field.action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index) => {
                  if (schema.forms[1].type === 'detail') {
                    schema.forms[1].rows.splice(index, 1)
                  }
                  if (schema.forms[2].type === 'detail') {
                    schema.forms[2].rows.splice(index - 1, 1)
                  }
                },
              },
            },
          },
        ],
        transform: [],
        includes: [],
        rows: [],
        listener: (type, item, index) => {
          if (type === 'add-new-row') {
            if (schema.forms[2].type === 'detail') {
              schema.forms[2].rows.push({
                level: 0,
                is_skippable: false,
                is_able_to_skip: false,
                is_full_approve: false,
                min_value: null,
                max_value: null,
              })
            }
          } else if (type === 'clear-all-row') {
            if (schema.forms[2].type === 'detail') {
              schema.forms[2].rows.length = 0
            }
          }
        },
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: table_prefix + 'm_approval_d_configs',
        },
        addRow: {
          from: 'empty',
          active: false,
          disabled: false,
          className: 'text-white',
        },
        clearAllRow: {
          active: false,
          disabled: false,
          exceptRowIndex: 0,
        },
        tableConfigs: {
          fixedHeader: true,
          maxHeight: '255px',
        },
        properties: [
          {
            field: 'level',
            type: 'editor',
            width: '180px',
            label: 'field.level',
            editor: {
              type: 'numberfield',
              default: 0,
              options: {
                placeholder: 'Level',
                readonly: false,
                disabled: true,
              },
              rules: ['required'],
            },
          },
          {
            field: 'is_skippable',
            type: 'editor',
            width: '180px',
            label: 'Skippable',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: false,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: () => {
                //
              },
              rules: [],
            },
          },
          {
            field: 'is_able_to_skip',
            type: 'editor',
            width: '180px',
            label: 'Able To Skip',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: false,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: () => {
                //
              },
              rules: [],
            },
          },
          {
            field: 'is_full_approve',
            type: 'editor',
            width: '180px',
            label: 'Full Approve',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: false,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: () => {
                //
              },
              rules: [],
            },
          },
          {
            field: 'send_wa_notif',
            type: 'editor',
            width: '180px',
            label: 'WA Notification',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: false,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: () => {
                //
              },
              rules: [],
            },
          },
          {
            field: 'send_email_notif',
            type: 'editor',
            width: '180px',
            label: 'Email Notification',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: false,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: () => {
                //
              },
              rules: [],
            },
          },
          {
            field: 'min_value',
            type: 'editor',
            width: '180px',
            label: 'Min Value',
            editor: {
              type: 'numberfield',
              default: null,
              options: {
                placeholder: 'Min Value',
                readonly: false,
                disabled: false,
              },
              rules: [],
            },
          },
          {
            field: 'max_value',
            type: 'editor',
            width: '180px',
            label: 'Max Value',
            editor: {
              type: 'numberfield',
              default: null,
              options: {
                placeholder: 'Max Value',
                readonly: false,
                disabled: false,
              },
              rules: [],
            },
          },
        ],
        transform: [],
        includes: [],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: table_prefix + 'm_approval_d_excludes',
        },
        addRow: {
          from: 'empty',
          active: true,
          disabled: false,
          className: 'text-white',
        },
        clearAllRow: {
          active: true,
          disabled: false,
          exceptRowIndex: 0,
        },
        tableConfigs: {
          fixedHeader: true,
          maxHeight: '255px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'level',
            type: 'editor',
            label: 'field.level',
            editor: {
              type: 'numberfield',
              default: 0,
              options: {
                placeholder: 'Level',
                readonly: false,
                disabled: false,
              },
              rules: ['required'],
            },
          },
          {
            field: 'user_id',
            type: 'editor',
            label: 'User',
            editor: {
              type: 'selectfield',
              version: 2,
              default: '',
              options: {
                placeholder: 'Select one',
                readonly: false,
                disabled: pageConfigs.mode === 'preview',
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
                  model: table_prefix + 'm_users',
                  root: 'data',
                  parameters: {
                    paginate: 25,
                    scopes: 'filter_by_cluster',
                  },
                  cache: false,
                },
              },
              listener: () => {
                //
              },
              rules: ['required'],
            },
          },
          {
            field: 'action',
            type: 'action',
            width: '80px',
            label: 'field.action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index) => {
                  if (schema.forms[3].type === 'detail') {
                    schema.forms[3].rows.splice(index, 1)
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
  if (pageConfigs.mode === 'create') {
    setTimeout(() => {
      if (schema.forms[1].type === 'detail') {
        schema.forms[1].rows.push({
          level: 0,
          level_order: 0,
          type: 'MENGAJUKAN',
          role_id: null,
          user_id: null,
          disabled: {
            level: true,
            level_order: true,
            type: true,
            action: true,
          },
        })
        // schema.forms[1].rows.push({
        //   level: 1,
        //   level_order: 1,
        //   type: 'MENGETAHUI',
        //   role_id: null,
        //   user_id: null,
        // })
      }
    }, 2000)
  }
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
          setTimeout(() => {
            initEdit.value = true
          }, 1000)
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
              SendError(err)
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
  }

  onMounted(() => {
    if (route.query.id) {
      configStore.backdrop.enabled = true
      methods.onLoadData()
    }
  })
</script>
