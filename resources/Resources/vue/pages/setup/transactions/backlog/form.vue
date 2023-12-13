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

        <FormDetail
          ref="formDetailRefsDetailTab1"
          v-model:form="schema.forms[1]"
          class="mx-auto px-3 my-4"
        />
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
    t,
  } from '~/services'
  import { useConfigStore } from '~/store/config'
  import router from '~/router'

  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'setup-transactions-backlog'
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
        text: 'Transactions',
        lang: 'menu.transactions',
      },
      {
        type: 'text-link',
        text: 'Backlog',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 't_backlog',
    landingRoute,
    mode: '',
    tabs: [
      {
        key: 'tab-1',
        label: 'Acceptance Criteria',
        icon: '<i class="fa-light fa-list"></i>',
        active: true,
      },
    ],
  })

  defineComponent({
    name: 'SetupTransBacklogForm',
  })

  const formHeaderRefs = ref()
  const isSaving = ref(false)
  const route = useRoute()

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
        model: table_prefix + 't_backlog',
        key: 'form-backlog',
        ready: 0,
        docs: false,
        properties: {
          epic_id: {
            type: 'selectfield',
            version: 2,
            value: null,
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Epic',
              information: 'Epic',
              inline: true,
              placeholder: 'Select one',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                value: 'id',
                display: 'key',
                type: {
                  value: 'encrypted',
                  display: 'string',
                },
              },
              api: {
                model: table_prefix + 'm_epic',
                root: 'data',
                parameters: {
                  paginate: 25,
                  // where: "`group`='STACK CATEGORY'"
                },
                cache: false,
              },
            },
            listener: () => {
              //
            },
            rules: ['required'],
          },
          story_total: {
            type: 'numberfield',
            value: '',
            default: 0,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Story Total',
              information: '',
              inline: true,
              placeholder: '',
              readonly: false,
              disabled: true,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: [],
          },
          description: {
            type: 'texteditorfield',
            version: 2,
            value: '',
            default: '',
            col: 'col-span-12',
            options: {
              label: 'Description',
              information: 'Deskripsi tentang epic',
              inline: true,
              placeholder: 'Write a text here',
              disabled: false,
              readonly: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['required'],
          },
          errors: null,
        },
      },
      {
        type: 'detail',
        relation: {
          model: `${table_prefix}t_backlog`,
          column: `${table_prefix}t_backlog_d_stories`,
          key: 'form-backlog',
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
        },
        tableConfigs: {
          fixedHeader: false,
          maxHeight: '550px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'user_story_title',
            type: 'editor',
            label: 'User Story Title',
            width: '200px',
            sortable: false,
            editor: {
              type: 'textareafield',
              default: '',
              options: {
                placeholder: '',
                readonly: false,
                disabled: false,
              },
              rules: ['required', 'max:100'],
            },
          },
          {
            field: 'story',
            type: 'editor',
            label: 'Story',
            width: '200px',
            editor: {
              type: 'texteditorfield',
              version: 2,
              default: '',
              options: {
                clearable: true,
                placeholder: '',
                readonly: false,
                disabled: false,
              },
              rules: [],
            },
          },
          {
            field: 'due_date',
            type: 'editor',
            label: 'Due Date',
            width: '170px',
            editor: {
              type: 'datetimefield',
              default: 'now',
              options: {
                placeholder: 'DD-MM-YYYY HH:mm',
                readonly: false,
                disabled: false,
                output: 'DD-MM-YYYY HH:mm',
              },
              listener: () => {
                //
              },
              rules: [],
            },
          },
          {
            field: table_prefix + 't_backlog_d_stories_s_acs',
            type: 'editor',
            width: '200px',
            label: 'Acceptance Criteria',
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'Acceptance Criteria',
                disabled: false,
                addRow: {
                  from: 'empty',
                  active: true,
                  className: 'text-white',
                  disabled: false,
                },
                clearAllRow: {
                  active: true,
                  disabled: false,
                },
                tableConfigs: {
                  fixedHeader: false,
                  maxHeight: '500px',
                },
                modal: {
                  width: 'w-10/12',
                },
              },
              listener: () => {
                //
              },
              properties: [
                {
                  field: 'title',
                  type: 'editor',
                  width: '300px',
                  label: 'Title',
                  tdClass: '',
                  editor: {
                    type: 'textareafield',
                    default: '',
                    options: {
                      placeholder: '',
                      readonly: false,
                      disabled: false,
                    },
                    listener: () => {
                      //
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: table_prefix + 't_backlog_d_stories_s_acs_d_values',
                  type: 'editor',
                  label: 'Values',
                  editor: {
                    type: 'listfield',
                    default: [],
                    options: {
                      placeholder: '',
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
                  label: 'field.action',
                  type: 'action',
                  width: '80px',
                  field: 'action',
                },
              ],
              rules: ['required', 'min:1'],
            },
          },
          {
            field: table_prefix + 't_backlog_d_stories_s_dods',
            type: 'editor',
            label: 'Definition of Done',
            width: '400px',
            editor: {
              type: 'listfield',
              default: [],
              options: {
                placeholder: '',
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
            field: table_prefix + 't_backlog_d_stories_s_scenarios',
            type: 'editor',
            label: 'Scenarios',
            width: '400px',
            editor: {
              type: 'listfield',
              default: [],
              options: {
                placeholder: '',
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
            field: 'status_id',
            type: 'editor',
            width: '200px',
            label: 'Status',
            tdClass: 'custom-title',
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
                    where: "`group` = 'BACKLOG STATUS'",
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
            type: 'action',
            width: '80px',
            label: 'field.action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index, id) => {
                  if (schema.forms[1].type === 'detail') {
                    schema.forms[1].rows.forEach((row, rowIndex) => {
                      if (schema.forms[1].type === 'detail') {
                        if (id && row.id === id) {
                          schema.forms[1].rows.splice(rowIndex, 1)
                        } else if (rowIndex === index) {
                          schema.forms[1].rows.splice(rowIndex, 1)
                        }
                      }
                    })
                  }
                },
              },
            },
          },
        ],
        includes: [],
        transform: [],
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
