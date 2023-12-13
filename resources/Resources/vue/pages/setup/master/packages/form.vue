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
          <template #tab2>
            <FormDetail
              ref="formDetailRefsTab2"
              v-model:form="schema.forms[2]"
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
  import { Axios, Notyf, Preparation, Swal, Transform } from '~/services'
  import { useConfigStore } from '~/store/config'
  import router from '~/router'
  import { useI18n } from 'vue-i18n'
  import { Tabs } from '~/components/molecules'

  const { t } = useI18n()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'setup-master-packages'
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
        text: 'Packages',
        lang: 'menu.packages',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 'm_packages',
    landingRoute,
    mode: '',
    tabs: [
      {
        key: 'tab1',
        label: 'Features',
        icon: '<i class="fa-light fa-hand-holding-box"></i>',
        active: true,
      },
      {
        key: 'tab2',
        label: 'Payment Methods',
        icon: '<i class="fa-light fa-credit-card"></i>',
      },
    ],
  })

  defineComponent({
    name: 'MasClustersForm',
  })

  const formHeaderRefs = ref()
  const formDetailRefsTab1 = ref()
  const formDetailRefsTab2 = ref()
  const isSaving = ref(false)
  const route = useRoute()

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
        model: pageConfigs.model,
        key: 'form',
        ready: 0,
        docs: false,
        properties: {
          name: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Name',
              information: 'Name of packages',
              inline: true,
              placeholder: 'Name',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['required'],
          },
          price: {
            type: 'rupiahfield',
            value: 0,
            default: 0,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Price',
              information: 'Price of package',
              inline: true,
              placeholder: 'Price',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: [],
          },
          description: {
            type: 'textareafield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Description',
              information: 'Description of package',
              inline: true,
              placeholder: 'Description',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: [],
          },
          active_flag: {
            type: 'switchfield',
            value: 1,
            default: 1,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Active Flag',
              information: 'Jika hidup maka data ini bisa di gunakan',
              inline: true,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
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
          column: 'ihm_m_packages_d_features',
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
          fixedHeader: true,
          maxHeight: '255px',
        },
        properties: [
          {
            field: 'value',
            type: 'editor',
            label: 'Value',
            editor: {
              type: 'textfield',
              default: '',
              options: {
                placeholder: 'Value',
                readonly: false,
                disabled: false,
              },
              rules: ['required', 'min:1'],
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
        includes: ['ihm_m_packages_id'],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_packages_d_payment_periods',
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
          fixedHeader: true,
          maxHeight: '255px',
        },
        properties: [
          {
            field: 'months',
            type: 'editor',
            label: 'Months',
            editor: {
              type: 'numberfield',
              default: 1,
              options: {
                placeholder: 'Months',
                readonly: false,
                disabled: false,
              },
              rules: ['required', 'min:1'],
            },
          },
          {
            field: 'discount',
            type: 'editor',
            label: 'Discount (%)',
            editor: {
              type: 'numberfield',
              default: 0,
              options: {
                placeholder: 'Discount',
                readonly: false,
                disabled: false,
              },
              rules: ['nullable'],
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
                  if (schema.forms[2].type === 'detail') {
                    schema.forms[2].rows.splice(index, 1)
                  }
                },
              },
            },
          },
        ],
        transform: [],
        includes: ['ihm_m_packages_id'],
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
      errors += formDetailRefsTab2.value.getValidRows()

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

  onMounted(() => {
    if (route.query.id) {
      configStore.backdrop.enabled = true
      methods.onLoadData()
    }
  })
</script>
