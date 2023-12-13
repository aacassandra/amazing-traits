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

  const { t } = useI18n()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'setup-master-housings'
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
        text: 'Housings',
        lang: 'menu.housings',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 'm_housings',
    landingRoute,
    mode: '',
  })

  defineComponent({
    name: 'MasHousingsForm',
  })

  const formHeaderRefs = ref()
  const formDetailRefsTab1 = ref()
  const formDetailRefsTab2 = ref()
  const isSaving = ref(false)
  const route = useRoute()

  const tabs = ref<TabsType>([
    {
      key: 'tab1',
      label: 'Clusters',
      icon: '<i class="fal fa-house"></i>',
      active: true,
    },
    {
      key: 'tab2',
      label: 'Stores',
      icon: '<i class="fal fa-store"></i>',
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

  const temp = reactive({
    province_code: null,
    regency_code: null,
    district_code: null,
    village_code: null,
  })
  const schema = reactive({
    version: 1,
    mode: pageConfigs.mode,
    forms: [
      {
        type: 'header',
        model: table_prefix + 'm_housings',
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
              information: 'Nama perumahan',
              inline: false,
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
          phone: {
            type: 'phonefield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-3',
            options: {
              label: 'Phone',
              information: 'Kontak telepon perumahan',
              inline: false,
              placeholder: '+62 000-0000-0000',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
              output: '+NN NNN-NNNN-NNNN',
            },
            rules: [],
          },
          email: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-3',
            options: {
              label: 'Email',
              information: 'Alamat email perumahan',
              inline: false,
              placeholder: 'example@mail.com',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: [],
          },
          address: {
            type: 'textareafield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Address',
              information: 'Alamat perumahan',
              inline: false,
              placeholder: 'Address',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
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
              information: 'Description of cluster',
              inline: false,
              placeholder: 'Description',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: [],
          },
          province_id: {
            type: 'selectfield',
            version: 2,
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-3',
            options: {
              label: 'Province',
              information: 'Provinsi asal perumahan',
              inline: false,
              placeholder: 'Province',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                value: 'id',
                display: 'value_3',
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
                  where: "`group` = 'SELECT PROVINCES'",
                },
                cache: false,
              },
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                element.regency_id.outVal = ''
                if (value) {
                  temp.province_code =
                    element.province_id.getValueFull().value_1
                }
              }
            },
            rules: [],
          },
          regency_id: {
            type: 'selectfield',
            version: 2,
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-3',
            options: {
              label: 'Regency',
              information: 'Kota asal perumahan',
              inline: false,
              placeholder: 'Regency',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                value: 'id',
                display: 'value_3',
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
                  where: "`group` = 'SELECT REGENCIES'",
                },
                cache: false,
                overrideParams: (element, oldParams) => {
                  oldParams['where'] += ` and value_2='${temp.province_code}'`
                  return oldParams
                },
              },
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                element.district_id.outVal = ''
                if (value) {
                  temp.regency_code = element.regency_id.getValueFull().value_1
                }
              }
            },
            rules: [],
          },
          district_id: {
            type: 'selectfield',
            version: 2,
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-3',
            options: {
              label: 'District',
              information: 'Kecamatan asal perumahan',
              inline: false,
              placeholder: 'District',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                value: 'id',
                display: 'value_3',
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
                  where: "`group` = 'SELECT DISTRICTS'",
                },
                cache: false,
                overrideParams: (element, oldParams) => {
                  oldParams['where'] += ` and value_2='${temp.regency_code}'`
                  return oldParams
                },
              },
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                element.village_id.outVal = ''
                if (value) {
                  temp.district_code =
                    element.district_id.getValueFull().value_1
                }
              }
            },
            rules: [],
          },
          village_id: {
            type: 'selectfield',
            version: 2,
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-3',
            options: {
              label: 'Village',
              information: 'Kelurahan asal perumahan',
              inline: false,
              placeholder: 'Village',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                value: 'id',
                display: 'value_3',
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
                  where: "`group` = 'SELECT VILLAGES'",
                },
                cache: false,
                overrideParams: (element, oldParams) => {
                  oldParams['where'] += ` and value_2='${temp.district_code}'`
                  return oldParams
                },
              },
            },
            rules: [],
          },
          active_flag: {
            type: 'switchfield',
            value: false,
            default: true,
            col: 'col-span-12 lg:col-span-1',
            options: {
              label: 'Status',
              information:
                'Jika dimatikan maka data tidak akan bisa digunakan sementara',
              inline: false,
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
          column: 'ihm_m_housings_d_clusters',
        },
        addRow: {
          from: 'popup',
          active: pageConfigs.mode !== 'preview',
          disabled: false,
          options: {
            isMultiple: true,
            globalSearch: false,
            className: {
              addNewList: 'text-white',
              verivy: 'text-white',
            },
            tableConfigs: {
              fixedHeader: true,
              selectAll: false,
            },
            uniqueColumn: 'id',
            includes: ['id:cluster_id'],
            columns: [
              {
                label: 'Name',
                field: 'name',
                model: 'ihm_m_clusters',
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
                model: 'ihm_m_clusters',
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
                model: 'ihm_m_clusters',
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
            ],
            api: {
              model: 'ihm_m_clusters',
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
          fixedHeader: true,
          maxHeight: '255px',
        },
        properties: [
          {
            field: 'name',
            type: 'string',
            label: 'Name',
          },
          {
            field: 'email',
            type: 'string',
            label: 'Email',
          },
          {
            field: 'phone',
            type: 'string',
            label: 'Phone',
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
        transform: [
          'id:cluster_id',
          'name:cluster.name',
          'email:cluster.email',
          'phone:cluster.phone',
        ],
        includes: ['ihm_m_housings_id', 'cluster_id'],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_stores',
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
            field: 'name',
            type: 'editor',
            width: '200px',
            label: 'Name',
            tdClass: 'custom-title',
            editor: {
              type: 'textfield',
              default: '',
              options: {
                placeholder: 'Name',
                readonly: false,
                disabled: false,
              },
              rules: ['required'],
            },
          },
          {
            field: 'description',
            type: 'editor',
            width: '200px',
            label: 'Description',
            tdClass: 'custom-title',
            editor: {
              type: 'textareafield',
              default: '',
              options: {
                placeholder: 'Description',
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
            field: 'address',
            type: 'editor',
            width: '200px',
            label: 'Address',
            tdClass: 'custom-title',
            editor: {
              type: 'textareafield',
              default: '',
              options: {
                placeholder: 'Address',
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
            field: 'phone',
            type: 'editor',
            width: '200px',
            label: 'Phone',
            tdClass: 'custom-title',
            editor: {
              type: 'textfield',
              default: '',
              options: {
                placeholder: 'Phone',
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
            field: 'email',
            type: 'editor',
            width: '200px',
            label: 'Email',
            tdClass: 'custom-title',
            editor: {
              type: 'textareafield',
              default: '',
              options: {
                placeholder: 'Email',
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
            field: 'photo',
            type: 'editor',
            width: '200px',
            label: 'Upload Photo',
            tdClass: 'custom-title',
            editor: {
              type: 'filefield',
              default: null,
              options: {
                clearable: true,
                viewer: 'image',
                placeholder: 'Upload an Photo',
                readonly: false,
                disabled: false,
              },
              rules: ['mimes:jpg,png,jpeg,webp,gif'],
            },
          },
          {
            field: 'longlat_point',
            type: 'editor',
            width: '300px',
            label: 'Coordinate',
            tdClass: 'custom-title',
            editor: {
              type: 'pinpointfield',
              default: [],
              options: {
                placeholder: 'Select Location',
                readonly: false,
                disabled: false,
                multiple: false,
                clearable: true,
              },
              listener: () => {
                //
              },
              rules: [],
            },
          },
          {
            field: 'active_flag',
            type: 'editor',
            width: '200px',
            label: 'Active Flag',
            tdClass: 'custom-title',
            editor: {
              type: 'switchfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
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
        includes: ['ihm_m_housings_id'],
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
          temp.province_code = res.data.data['province.value_1']
          temp.regency_code = res.data.data['regency.value_1']
          temp.district_code = res.data.data['district.value_1']
          temp.village_code = res.data.data['village.value_1']
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
