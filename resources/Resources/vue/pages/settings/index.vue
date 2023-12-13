<template>
  <div class="w-full">
    <div class="grid grid-cols-12">
      <div class="col-span-6 my-auto">
        <Breadcrumb :data="pageConfigs.breadcrumb" />
      </div>
      <div class="col-span-12">
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
            <div
              :style="{ minHeight: `calc(${clientHeight}px - 340px)` }"
              class="mt-3"
            >
              <Tabs :items="tabs">
                <template #general>
                  <FormHeader
                    ref="formHeaderRefs"
                    v-model:form="schema.forms[0]"
                    class="mt-3 mb-5"
                  />
                  <div class="container px-4 mx-auto">
                    <div class="grid grid-cols-12">
                      <div class="col-span-12">
                        <SubmitButton :on-click="methods.onSubmit">
                          Submit
                        </SubmitButton>
                      </div>
                    </div>
                  </div>
                </template>
                <template #styles>
                  <FormHeader
                    ref="formHeaderRefs"
                    v-model:form="schema.forms[1]"
                    class="mt-3 mb-5"
                  />
                  <div class="container px-9 mx-auto">
                    <div class="grid grid-cols-12">
                      <div class="col-span-12 flex justify-end">
                        <button
                          :disabled="isLoading"
                          type="button"
                          class="btn btn-sm btn-error text-white mr-2"
                          @click="methods.resetStyle()"
                        >
                          Reset
                        </button>
                        <SubmitButton
                          :disabled="isLoading"
                          :is-loading="isLoading"
                          :on-click="methods.onSubmit"
                        >
                          Submit
                        </SubmitButton>
                      </div>
                    </div>
                  </div>
                </template>
              </Tabs>
            </div>
          </template>
        </Card>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
  import { inject, onMounted, onUnmounted, provide, ref, reactive } from 'vue'
  import { PageConfigs } from '~/types/pages/form/v1'
  // Components
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { Card } from '~/components/atoms'
  import { Tabs } from '~/components/molecules'
  import { TabsType } from '~/types/components/molecules/tabs'
  import { Schema } from '~/types'
  import FormHeader from '~/components/organism/FormHeader/index.vue'
  import SubmitButton from '~/components/atoms/buttons/SubmitButton.vue'
  import { useConfigStore } from '~/store/config'
  import { Preparation, Notyf, Swal, Axios } from '~/services'

  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const clientHeight = inject('clientHeight')
  const isLoading = ref(false)
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
        text: 'Settings',
      },
    ],
    title: 'Settings',
  })

  const tabs = ref<TabsType>([
    {
      key: 'general',
      label: 'General',
      icon: '<i class="fal fa-sliders-h"></i>',
      active: true,
    },
    {
      key: 'styles',
      label: 'Styles',
      icon: '<i class="fal fa-palette"></i>',
    },
  ])

  const formHeaderRefs = ref()
  const schema = reactive({
    version: 1,
    mode: 'edit',
    forms: [
      {
        type: 'header',
        model: table_prefix + 'configs',
        key: 'config',
        ready: 0,
        docs: false,
        properties: {
          active_flag: {
            type: 'switchfield',
            value: false,
            default: true,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Is Maintenance',
              information:
                'Menutup sementara aplikasi untuk kebutuhan maintenance',
              inline: true,
              disabled: false,
              hidden: false,
            },
            listener: (_, type) => {
              if (type === 'change') {
                //
              }
            },
          },
          errors: null,
        },
      },
      {
        type: 'header',
        model: 'style',
        key: 'change-style',
        ready: 0,
        docs: false,
        properties: {
          logo_text: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Logo Text Color',
              information: 'Warna text untuk Logo',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='text'",
                },
                cache: false,
              },
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          logo_text_dark: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Logo Text Color (Darkmode)',
              information: 'Warna text untuk logo ketika darkmode',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='text'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          sidebar_bg: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Sidebar Background Color',
              information: 'Warna background untuk bilah sidebar',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='background'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          sidebar_bg_dark: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Sidebar Background Color (Darkmode)',
              information:
                'Warna background untuk bilah sidebar ketika darkmode',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='background'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          sidebar_text: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Sidebar Text Color',
              information: 'Warna text untuk bilah sidebar',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='text'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          sidebar_text_dark: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Sidebar Text Color (Darkmode)',
              information: 'Warna text untuk bilah sidebar ketika darkmode',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='text'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          sidebar_border: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Sidebar Border Color',
              information: 'Warna border untuk bilah sidebar',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='border'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          sidebar_border_dark: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Sidebar Border Color (Darkmode)',
              information: 'Warna border untuk bilah sidebar ketika darkmode',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='border'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          header_bg: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Header Background Color',
              information: 'Warna background untuk header',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='background'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          header_bg_dark: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Header Background Color (Darkmode)',
              information: 'Warna background untuk header ketika darkmode',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='background'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          header_text: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Header Text Color',
              information: 'Warna text untuk header',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='text'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          header_text_dark: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Header Text Color (Darkmode)',
              information: 'Warna text untuk header ketika darkmode',
              inline: true,
              placeholder: 'Select color',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                output: 'default',
                value: 'code',
                display: 'code',
                columns: [
                  {
                    type: 'string',
                    label: 'Kode Warna',
                    width: '230px',
                    field: 'code',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    type: 'string',
                    label: 'HEX',
                    field: 'hex',
                    filter: true,
                    filterOptions: {
                      enabled: false, // enable filter for this column
                    },
                  },
                  {
                    type: 'div',
                    label: 'Example Color',
                    field: 'hex',
                    html: '<div style="width: 60px; height: 30px; background-color: :value"></div>',
                  },
                ],
              },
              api: {
                model: table_prefix + 'm_colors',
                root: 'data',
                parameters: {
                  paginate: 300,
                  where: "`group`='text'",
                },
                cache: false,
              },
            },
            listener: (element, type) => {
              if (type === 'change') {
                methods.onChangeStyle()
              }
            },
            rules: [],
          },
          errors: null,
        },
      },
    ],
  }) as Schema
  provide('schema', schema)

  const styleBackup = ref(null)
  const methods = {
    onChangeStyle: () => {
      let json = {}
      json['logo_text'] = schema.forms[1].properties['logo_text'].value
      json['logo_text_dark'] =
        schema.forms[1].properties['logo_text_dark'].value
      json['sidebar_bg'] = schema.forms[1].properties['sidebar_bg'].value
      json['sidebar_bg_dark'] =
        schema.forms[1].properties['sidebar_bg_dark'].value
      json['sidebar_text'] = schema.forms[1].properties['sidebar_text'].value
      json['sidebar_text_dark'] =
        schema.forms[1].properties['sidebar_text_dark'].value
      json['sidebar_border'] =
        schema.forms[1].properties['sidebar_border'].value
      json['sidebar_border_dark'] =
        schema.forms[1].properties['sidebar_border_dark'].value
      json['header_bg'] = schema.forms[1].properties['header_bg'].value
      json['header_bg_dark'] =
        schema.forms[1].properties['header_bg_dark'].value
      json['header_text'] = schema.forms[1].properties['header_text'].value
      json['header_text_dark'] =
        schema.forms[1].properties['header_text_dark'].value

      json = methods.jsonToStyleFormat(json)

      configStore.save(json)
    },
    onSaved: (json) => {
      isLoading.value = true
      Axios({
        url: `/api/v1/${table_prefix}configs/${configStore.styleId}`,
        data: {
          key: 'styles',
          value: JSON.stringify(json),
        },
        method: 'PUT',
      })
        .then((res: any) => {
          isLoading.value = false
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
          styleBackup.value = json
          configStore.save(json)
        })
        .catch((err) => {
          isLoading.value = false
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

      Swal.confirm({
        title: 'Confirmation',
        html: 'Are you sure? save this change, click Yes to continue',
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
            const formated = json['change-style'].style
            json = methods.jsonToStyleFormat(formated)
            methods.onSaved(json)
          })
        }
      })
    },
    initStyle: () => {
      const style = configStore.getStyle
      styleBackup.value = style
      if (schema.forms[1].type === 'header') {
        schema.forms[1].properties['logo_text'].outVal = style.logo.text.light
        schema.forms[1].properties['logo_text_dark'].outVal =
          style.logo.text.dark
        schema.forms[1].properties['sidebar_bg'].outVal = style.sidebar.bg.light
        schema.forms[1].properties['sidebar_bg_dark'].outVal =
          style.sidebar.bg.dark
        schema.forms[1].properties['sidebar_text'].outVal =
          style.sidebar.text.light
        schema.forms[1].properties['sidebar_text_dark'].outVal =
          style.sidebar.text.dark
        schema.forms[1].properties['sidebar_border'].outVal =
          style.sidebar.border.light
        schema.forms[1].properties['sidebar_border_dark'].outVal =
          style.sidebar.border.dark
        schema.forms[1].properties['header_bg'].outVal = style.header.bg.light
        schema.forms[1].properties['header_bg_dark'].outVal =
          style.header.bg.dark
        schema.forms[1].properties['header_text'].outVal =
          style.header.text.light
        schema.forms[1].properties['header_text_dark'].outVal =
          style.header.text.dark
      }
    },
    resetStyle: () => {
      Swal.confirm({
        title: 'Confirmation',
        html: 'Are you sure? you want to reset this, click Yes to continue',
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
          configStore.reset().then((json) => {
            methods.onSaved(json)
          })
        }
      })
    },
    jsonToStyleFormat: (json) => {
      return {
        logo: {
          text: {
            light: json.logo_text,
            dark: json.logo_text_dark,
          },
        },
        sidebar: {
          bg: {
            light: json.sidebar_bg,
            dark: json.sidebar_bg_dark,
          },
          text: {
            light: json.sidebar_text,
            dark: json.sidebar_text_dark,
          },
          border: {
            light: json.sidebar_border,
            dark: json.sidebar_border_dark,
          },
        },
        header: {
          bg: {
            light: json.header_bg,
            dark: json.header_bg_dark,
          },
          text: {
            light: json.header_text,
            dark: json.header_text_dark,
          },
        },
      }
    },
  }

  const configStore = useConfigStore()
  onMounted(() => {
    methods.initStyle()
  })

  onUnmounted(() => {
    const json = styleBackup.value
    configStore.save(json)
  })
</script>
