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
              class="text-lg text-gray-900 dark:text-white ft-dmsans-700 px-1"
            >
              {{ t(pageConfigs.title) }}
            </div>
          </div>
          <div class="fle-grow-0">
            <div class="flex relative items-center">
              <label for="sdocs" class="cursor-pointer">
                <input
                  id="sdocs"
                  v-model="schema.forms[0].docs"
                  type="checkbox"
                  class="sr-only"
                />
                <div
                  class="w-11 h-6 bg-gray-200 rounded-full border border-gray-200 toggle-bg dark:bg-gray-700 dark:border-gray-600"
                ></div>
              </label>
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
          ref="formDetailRefs"
          v-model:form="schema.forms[1]"
          mode="create"
          class="my-10"
        />
      </template>
      <template #card-footer>
        <div class="grid grid-cols-12">
          <div class="col-span-12 flex justify-end">
            <button
              type="button"
              class="btn btn-primary dark:text-white btn-sm"
              @click="methods.onSubmit"
            >
              Submit
            </button>
          </div>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
  /* eslint-disable @typescript-eslint/no-unused-vars,no-console */
  import { defineComponent, reactive } from 'vue'
  import { provide, ref } from 'vue'
  import { PageConfigs } from '~/types/pages/form/v1'
  // Components
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { Card } from '~/components/atoms'
  import FormHeader from '~/components/organism/FormHeader/index.vue'
  import FormDetail from '~/components/organism/FormDetail/index.vue'
  import { Schema } from '~/types'
  import { Notyf, t } from '~/services'

  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX

  defineComponent({
    name: 'FormsIndex',
  })

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
        text: 'Form Example',
      },
    ],
    title: 'Form Example',
  })
  const formHeaderRefs = ref()
  const formDetailRefs = ref()

  const schema = reactive({
    version: 1,
    mode: 'create',
    forms: [
      {
        type: 'header',
        ready: 0,
        docs: false,
        model: 'example',
        key: 'example',
        properties: {
          text: {
            type: 'textfield',
            value: '',
            default: 'coba',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Text',
              information: 'Help text example: Text',
              inline: true,
              placeholder: 'Write a text here',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                if (value) {
                  element.selectV1.setValue('8wJHbquQHj')
                  element.selectV1Multiple.setValue(['8wJHbquQHj'])
                  element.selectV2.setValue('8wJHbquQHj')
                  element.selectV2Multiple.setValue(['8wJHbquQHj'])
                  element.popup.setValue('8wJHbquQHj')
                  element.popupMultiple.setValue(['8wJHbquQHj'])
                  // element.selectV2Multiple.setValue([])
                  // element.textEditor.setValue(`<b>${value}</b>`)
                  // element.date.setValue('2020-05-27')
                  // element.dateTime.setValue('2020-06-13 03:30')
                  // element.switch.setValue(true)
                  // element.datetimerange.setValue({
                  //   start: '2022-12-25 03:00',
                  //   end: '2022-12-28 17:30'
                  // })
                } else {
                  element.selectV1.setValue('')
                  element.selectV1Multiple.setValue([])
                  element.selectV2.setValue('')
                  element.selectV2Multiple.setValue([])
                  element.popup.setValue('')
                  element.popupMultiple.setValue([])
                  // element.selectV2.setValue('')
                  // element.selectV2Multiple.setValue([])
                  // element.textEditor.setValue('')
                  // element.selectV1Multiple.setValue([])
                  // element.date.setValue('')
                  // element.datetime.setValue('')
                  // element.datetimerange.setValue({
                  //   start: '',
                  //   end: ''
                  // })
                  // element.tags.setValue(['ada', 'qweqe'])
                  // element.switch.setValue(false)
                }
              }
            },
            rules: ['required', 'min:5', 'max:10'],
          },
          phone: {
            type: 'phonefield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Phone',
              information: 'Phone number',
              inline: true,
              placeholder: '+62 000-0000-0000',
              readonly: false,
              disabled: false,
              hidden: false,
              output: '+NN NNN-NNNN-NNNN',
            },
            rules: ['required'],
          },
          number: {
            type: 'numberfield',
            value: '',
            default: 0,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Number',
              information: 'Help text example: Number',
              inline: true,
              placeholder: 'Write a number here',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['required', 'min:5', 'max:7', 'decimal:2'],
          },
          password: {
            type: 'passwordfield',
            value: '',
            default: 'password',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Password',
              information: 'Help text example: Password',
              inline: true,
              placeholder: 'Write a password here',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: (_, type) => {
              if (type === 'change') {
                //
              }
            },
            rules: ['required', 'min:5', 'max:10'],
          },
          tags: {
            type: 'tagsfield',
            value: [],
            default: ['tag1', 'tag2'],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Tags',
              information: 'Help text example: Tags',
              inline: true,
              placeholder: 'tags1, tags2, ...',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['required'],
          },
          // selectV1: {
          //   type: 'selectfield',
          //   version: 1,
          //   value: null,
          //   default: '',
          //   items: [],
          //   col: 'col-span-12 lg:col-span-6',
          //   options: {
          //     label: 'Select v1',
          //     information: 'Help text example: Select v1',
          //     inline: true,
          //     placeholder: 'Select one',
          //     readonly: false,
          //     disabled: false,
          //     hidden: false,
          //     multiple: false,
          //     field: {
          //       value: 'id',
          //       display: 'username',
          //     },
          //     api: {
          //       model: table_prefix + 'd_example',
          //       root: 'data',
          //       parameters: {
          //         paginate: 25,
          //       },
          //       cache: false,
          //     },
          //   },
          //   listener: () => {
          //     //
          //   },
          //   rules: ['required'],
          // },
          // selectV1Multiple: {
          //   type: 'selectfield',
          //   version: 1,
          //   value: null,
          //   default: [],
          //   items: [],
          //   col: 'col-span-12 lg:col-span-6',
          //   options: {
          //     label: 'Select v1 Multiple',
          //     information: 'Help text example: Select v1',
          //     inline: true,
          //     placeholder: 'Select one',
          //     readonly: false,
          //     disabled: false,
          //     hidden: false,
          //     multiple: true,
          //     field: {
          //       value: 'id',
          //       display: 'username',
          //     },
          //     api: {
          //       model: table_prefix + 'd_example',
          //       root: 'data',
          //       parameters: {
          //         paginate: 25,
          //       },
          //       cache: false,
          //     },
          //   },
          //   listener: () => {
          //     //
          //   },
          //   rules: ['required', 'min:3', 'max:5'],
          // },
          selectV2: {
            type: 'selectfield',
            version: 2,
            value: null,
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Select v2',
              information: 'Help text example: Select v1',
              inline: true,
              placeholder: 'Select one',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
              field: {
                value: 'id',
                display: 'username',
                type: {
                  value: 'encrypted',
                  display: 'string',
                },
              },
              api: {
                model: table_prefix + 'd_example',
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
          selectV2Multiple: {
            type: 'selectfield',
            version: 2,
            value: [],
            default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Select v2 Multiple',
              information: 'Help text example: Select v1',
              inline: true,
              placeholder: 'Select many',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: true,
              clearable: true,
              field: {
                value: 'id',
                display: 'username',
                type: {
                  value: 'encrypted',
                  display: 'string',
                },
              },
              api: {
                model: table_prefix + 'd_example',
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
            rules: ['required', 'min:3', 'max:5'],
          },
          popup: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Popup',
              information: 'Help text example: Popup',
              inline: true,
              placeholder: 'Select one',
              readonly: false,
              disabled: false,
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
                display: 'username',
                type: {
                  value: 'encrypted',
                  display: 'string',
                },
                columns: [
                  {
                    label: 'First Name',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'first_name',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Last Name',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'last_name',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Gender',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'gender',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Age',
                    type: 'number',
                    model: table_prefix + 'd_example',
                    field: 'age',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Username',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'username',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Email',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'email',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Phone',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'phone',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Address',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'address',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Avatar',
                    type: 'image',
                    style: { width: '30px', height: '30px' },
                    field: 'avatar',
                  },
                ],
              },
              api: {
                model: table_prefix + 'd_example',
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
          popupMultiple: {
            type: 'popupfield',
            value: '',
            default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Popup Multiple',
              information: 'Help text example: Popup',
              inline: true,
              placeholder: 'Select many',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: true,
              clearable: true,
              tableConfigs: {
                fixedHeader: false,
                maxHeight: '455px',
                lineNumbers: true,
              },
              field: {
                value: 'id',
                display: 'username',
                type: {
                  value: 'encrypted',
                  display: 'string',
                },
                columns: [
                  {
                    label: 'First Name',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'first_name',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Last Name',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'last_name',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Gender',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'gender',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Age',
                    type: 'number',
                    model: table_prefix + 'd_example',
                    field: 'age',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Username',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'username',
                    width: '150px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Email',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'email',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Phone',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'phone',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Address',
                    type: 'string',
                    model: table_prefix + 'd_example',
                    field: 'address',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Avatar',
                    type: 'image',
                    style: { width: '30px', height: '30px' },
                    field: 'avatar',
                  },
                ],
              },
              api: {
                model: table_prefix + 'd_example',
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
            rules: ['required', 'min:3', 'max:5'],
          },
          date: {
            type: 'datefield',
            value: '',
            default: 'now',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Date',
              information: 'Help text example: Date',
              inline: true,
              placeholder: 'DD-MM-YYYY',
              output: 'DD-MM-YYYY',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                console.log(value)
              }
            },
            rules: ['required'],
          },
          dateTime: {
            type: 'datetimefield',
            value: '',
            default: 'now',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'DateTime',
              information: 'Help text example: DateTime',
              inline: true,
              placeholder: 'DD-MM-YYYY HH:mm',
              output: 'DD-MM-YYYY HH:mm',
              in24h: true,
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                console.log(value)
              }
            },
            rules: ['required'],
          },
          time: {
            type: 'timefield',
            value: '',
            default: 'now',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Time',
              information: 'Help text example: Time',
              inline: true,
              in24h: true,
              placeholder: 'H:i',
              output: 'HH:mm',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                console.log(value)
              }
            },
            rules: ['required'],
          },
          daterange: {
            type: 'daterangefield',
            value: {
              start: '',
              end: '',
            },
            default: {
              start: 'now',
              end: 'tomorrow',
            },
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Date Range',
              information: 'Help text example: Daterange',
              inline: true,
              placeholder: 'DD-MM-YYYY to DD-MM-YYYY',
              output: 'DD-MM-YYYY',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                console.log(value)
              }
            },
            rules: ['required'],
          },
          datetimerange: {
            type: 'datetimerangefield',
            value: {
              start: '',
              end: '',
            },
            default: {
              start: 'now',
              end: 'tomorrow',
            },
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Date Time Range',
              information: 'Help text example: Daterange',
              inline: true,
              placeholder: 'DD-MM-YYYY HH:mm to DD-MM-YYYY HH:mm',
              output: 'DD-MM-YYYY HH:mm',
              in24h: true,
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: (element, type, value) => {
              if (type === 'change') {
                console.log(value)
              }
            },
            rules: ['required'],
          },
          slug: {
            type: 'slugfield',
            value: '',
            default: 'percobaan nilai slug',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'URL Slug',
              information: 'Help text example: URL Slug',
              inline: true,
              readonly: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['required'],
          },
          image: {
            type: 'filefield',
            value: null,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Upload Image',
              viewer: 'image',
              information: 'Help text example: Upload Image',
              inline: true,
              disabled: false,
              hidden: false,
              clearable: true,
              autoUpload: false,
            },
            listener: () => {
              //
            },
            hasChanged: false,
            rules: ['required', 'mimes:jpg,jpeg,png,webp,gif'],
          },
          pdf: {
            type: 'filefield',
            value: null,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Upload PDF',
              viewer: 'pdf',
              information: 'Help text example: Upload PDF',
              inline: true,
              disabled: false,
              hidden: false,
              clearable: true,
              autoUpload: false,
            },
            listener: () => {
              //
            },
            hasChanged: false,
            rules: ['required', 'mimes:pdf'],
          },
          switch: {
            type: 'switchfield',
            value: false,
            default: true,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Switch',
              information: 'Help text example: Switch',
              inline: true,
              disabled: false,
              hidden: false,
            },
            listener: () => {
              //
            },
          },
          textarea: {
            type: 'textareafield',
            value: '',
            default: 'percobaan text area',
            col: 'col-span-12 lg:col-span-12',
            options: {
              label: 'Text Area',
              information: 'Help text example: Text Area',
              inline: true,
              placeholder: 'Write a text here',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['required', 'min:10', 'max:30'],
          },
          textEditorV1: {
            type: 'texteditorfield',
            version: 1,
            value: '',
            default: '',
            col: 'col-span-12',
            options: {
              label: 'Text Editor V1',
              information: 'Help text example: Text Editor',
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
          textEditorV2: {
            type: 'texteditorfield',
            version: 2,
            value: '',
            default: '',
            col: 'col-span-12',
            options: {
              label: 'Text Editor V2',
              information: 'Help text example: Text Editor',
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
          key: 'example',
          model: 'example',
          column: 'example_detail',
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
        },
        tableConfigs: {
          fixedHeader: false,
          maxHeight: '255px',
        },
        properties: [
          {
            field: 'text',
            type: 'editor',
            width: '180px',
            label: 'Text',
            tdClass: 'custom-title',
            editor: {
              type: 'textfield',
              default: 'helo text',
              options: {
                placeholder: 'text',
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) => {
                if (type === 'change') {
                  if (value) {
                    element.setValue({
                      field: 'number',
                      value: 1900,
                    })

                    element.setValue({
                      field: 'password',
                      value: 'pass',
                    })

                    element.setValue({
                      field: 'textarea',
                      value:
                        'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been',
                    })

                    element.setValue({
                      field: 'texteditor',
                      value:
                        '<b>is simply dummy</b> text of the printing and typesetting industry. Lorem Ipsum has been',
                    })

                    element.setValue({
                      field: 'selectv1',
                      value: '_d768772',
                    })

                    element.setValue({
                      field: 'selectv1multiple',
                      value: ['_d768772'],
                    })

                    element.setValue({
                      field: 'selectv2',
                      value: '_d768772',
                    })

                    element.setValue({
                      field: 'selectv2multiple',
                      value: ['_d768772'],
                    })

                    // element.setValue({
                    //   field: 'tags',
                    //   value: ['abc', 'bca']
                    // })
                    //
                    // element.setValue({
                    //   field: 'date',
                    //   value: '2022-12-28 17:30'
                    // })
                    //
                    // element.setValue({
                    //   field: 'daterange',
                    //   value: {
                    //     start: '2022-12-25 03:00',
                    //     end: '2022-12-28 17:30'
                    //   }
                    // })
                    // element.setValue({
                    //   field: 'selectv1',
                    //   value: 1
                    // })

                    element.setValue({
                      field: 'checkbox',
                      value: true,
                    })
                  } else {
                    element.setValue({
                      field: 'number',
                      value: 0,
                    })

                    element.setValue({
                      field: 'password',
                      value: '',
                    })

                    element.setValue({
                      field: 'textarea',
                      value: '',
                    })

                    element.setValue({
                      field: 'texteditor',
                      value: '',
                    })

                    element.setValue({
                      field: 'selectv1',
                      value: '',
                    })

                    element.setValue({
                      field: 'selectv1multiple',
                      value: [],
                    })

                    element.setValue({
                      field: 'selectv2',
                      value: '',
                    })

                    element.setValue({
                      field: 'selectv2multiple',
                      value: [],
                    })

                    element.setValue({
                      field: 'checkbox',
                      value: false,
                    })

                    // element.setValue({
                    //   field: 'tags',
                    //   value: []
                    // })
                    //
                    // element.setValue({
                    //   field: 'date',
                    //   value: ''
                    // })
                    //
                    // element.setValue({
                    //   field: 'daterange',
                    //   value: {
                    //     start: '',
                    //     end: ''
                    //   }
                    // })
                    // element.setValue({
                    //   field: 'name',
                    //   value: ''
                    // })
                  }
                }
              },
              rules: ['required'],
            },
          },
          {
            field: 'number',
            type: 'editor',
            width: '180px',
            label: 'Number',
            tdClass: 'custom-title',
            editor: {
              type: 'numberfield',
              default: 100,
              options: {
                placeholder: 'number',
                readonly: false,
                disabled: false,
              },
              listener: (element, type) => {
                if (type === 'change') {
                  //
                }
              },
              rules: ['required', 'min:5', 'max:5000'],
            },
          },
          {
            field: 'rupiah',
            type: 'editor',
            width: '180px',
            label: 'Price',
            editor: {
              type: 'rupiahfield',
              default: 0,
              options: {
                placeholder: 'Price',
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
            field: 'phone',
            type: 'editor',
            width: '180px',
            label: 'Phone',
            tdClass: 'custom-title',
            editor: {
              type: 'phonefield',
              default: '',
              options: {
                placeholder: '+62 000-0000-0000',
                readonly: false,
                disabled: false,
                display: '+NN NNN-NNNN-NNNN',
                output: 'NNNNNNNNNNNN',
              },
              rules: ['required'],
            },
          },
          {
            field: 'password',
            type: 'editor',
            width: '180px',
            label: 'Password',
            tdClass: 'custom-title',
            editor: {
              type: 'passwordfield',
              default: 'password',
              options: {
                placeholder: 'password',
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) => {
                if (type === 'change') {
                  //
                }
              },
              rules: ['required', 'min:6', 'max:10'],
            },
          },
          {
            field: 'active_flag',
            type: 'editor',
            width: '180px',
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
            field: 'checkbox',
            type: 'editor',
            width: '180px',
            label: 'Checkbox',
            tdClass: 'custom-title',
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              rules: [],
            },
          },
          {
            field: 'textarea',
            type: 'editor',
            width: '180px',
            label: 'Textarea',
            tdClass: 'custom-title',
            editor: {
              type: 'textareafield',
              default: 'helo text',
              options: {
                placeholder: 'title',
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) => {
                if (type === 'change') {
                  //
                }
              },
              rules: ['required', 'min:10', 'max:30'],
            },
          },
          {
            field: 'texteditorV1',
            type: 'editor',
            width: '180px',
            label: 'Text Editor V1',
            tdClass: 'custom-title',
            editor: {
              type: 'texteditorfield',
              version: 1,
              default:
                '<b>is simply dummy</b> text of the printing and typesetting industry. Lorem Ipsum has been',
              options: {
                clearable: true,
                placeholder: 'Text Editor',
                readonly: false,
                disabled: false,
              },
              rules: ['required'],
            },
          },
          {
            field: 'texteditorV2',
            type: 'editor',
            width: '180px',
            label: 'Text Editor V2',
            tdClass: 'custom-title',
            editor: {
              type: 'texteditorfield',
              version: 2,
              default:
                '<b>is simply dummy</b> text of the printing and typesetting industry. Lorem Ipsum has been',
              options: {
                clearable: true,
                placeholder: 'Text Editor',
                readonly: false,
                disabled: false,
              },
              rules: ['required'],
            },
          },
          {
            field: 'uploadimage',
            type: 'editor',
            width: '180px',
            label: 'Upload Image',
            tdClass: 'custom-title',
            editor: {
              type: 'filefield',
              default: null,
              options: {
                clearable: true,
                viewer: 'image',
                placeholder: 'Upload an image',
                readonly: false,
                disabled: false,
              },
              rules: ['required', 'mimes:jpg,png,jpeg,webp,gif'],
            },
          },
          {
            field: 'uploadpdf',
            type: 'editor',
            width: '180px',
            label: 'Upload PDF',
            tdClass: 'custom-title',
            editor: {
              type: 'filefield',
              default: null,
              options: {
                clearable: true,
                viewer: 'pdf',
                placeholder: 'Upload a PDF',
                readonly: false,
                disabled: false,
              },
              rules: ['required', 'mimes:pdf'],
            },
          },
          {
            field: 'selectv1',
            type: 'editor',
            width: '180px',
            label: 'Select',
            tdClass: 'custom-title',
            editor: {
              type: 'selectfield',
              version: 1,
              default: '',
              options: {
                placeholder: 'Select one',
                readonly: false,
                disabled: false,
                multiple: false,
                field: {
                  value: 'id',
                  display: 'username',
                },
                api: {
                  model: table_prefix + 'd_example',
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
            field: 'selectv1multiple',
            type: 'editor',
            width: '180px',
            label: 'Select Multiple',
            tdClass: 'custom-title',
            editor: {
              type: 'selectfield',
              version: 1,
              default: '',
              options: {
                placeholder: 'Select one',
                readonly: false,
                disabled: false,
                multiple: true,
                field: {
                  value: 'id',
                  display: 'username',
                },
                api: {
                  model: table_prefix + 'd_example',
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
              rules: ['required', 'min:3', 'max:5'],
            },
          },
          {
            field: 'selectv2',
            type: 'editor',
            width: '230px',
            label: 'Select2',
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
                  display: 'username',
                  type: {
                    value: 'encrypted',
                    display: 'string',
                  },
                },
                api: {
                  model: table_prefix + 'd_example',
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
            field: 'selectv2multiple',
            type: 'editor',
            width: '230px',
            label: 'Select2 Multiple',
            tdClass: 'custom-title',
            editor: {
              type: 'selectfield',
              version: 2,
              default: [],
              options: {
                placeholder: 'Select many',
                readonly: false,
                disabled: false,
                multiple: true,
                clearable: true,
                field: {
                  value: 'id',
                  display: 'username',
                  type: {
                    value: 'encrypted',
                    display: 'string',
                  },
                },
                api: {
                  model: table_prefix + 'd_example',
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
              rules: ['required', 'min:3', 'max:5'],
            },
          },
          // {
          //   field: 'popup',
          //   type: 'editor',
          //   width: '180px',
          //   label: 'Popup',
          //   tdClass: 'custom-title',
          //   editor: {
          //     type: 'popupfield',
          //     default: 1,
          //     options: {
          //       placeholder: 'Select one',
          //       readonly: false,
          //       disabled: false,
          //       multiple: false,
          //       clearable: true,
          //       tableConfigs: {
          //         fixedHeader: false,
          //         maxHeight: '255px',
          //       },
          //       field: {
          //         value: 'id',
          //         display: 'username',
          //         columns: [
          //           {
          //             label: 'First Name',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'first_name',
          //             width: '150px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Last Name',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'last_name',
          //             width: '150px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Gender',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'gender',
          //             width: '150px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Age',
          //             type: 'number',
          //             model: table_prefix + 'd_example',
          //             field: 'age',
          //             width: '200px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Username',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'username',
          //             width: '150px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Email',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'email',
          //             width: '200px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Phone',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'phone',
          //             width: '200px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Address',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'address',
          //             width: '200px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Avatar',
          //             type: 'image',
          //             style: { width: '30px', height: '30px' },
          //             field: 'avatar',
          //           },
          //         ],
          //       },
          //       api: {
          //         model: table_prefix + 'd_example',
          //         root: 'data',
          //         parameters: {
          //           paginate: 25,
          //         },
          //         cache: false,
          //       },
          //     },
          //     listener: () => {
          //       //
          //     },
          //     rules: ['required'],
          //   },
          // },
          // {
          //   field: 'popupmultiple',
          //   type: 'editor',
          //   width: '180px',
          //   label: 'Popup Multiple',
          //   tdClass: 'custom-title',
          //   editor: {
          //     type: 'popupfield',
          //     default: [],
          //     options: {
          //       placeholder: 'Select many',
          //       readonly: false,
          //       disabled: false,
          //       multiple: true,
          //       clearable: true,
          //       tableConfigs: {
          //         fixedHeader: false,
          //         maxHeight: '255px',
          //       },
          //       field: {
          //         value: 'id',
          //         display: 'username',
          //         columns: [
          //           {
          //             label: 'First Name',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'first_name',
          //             width: '150px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Last Name',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'last_name',
          //             width: '150px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Gender',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'gender',
          //             width: '150px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Age',
          //             type: 'number',
          //             model: table_prefix + 'd_example',
          //             field: 'age',
          //             width: '200px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Username',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'username',
          //             width: '150px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Email',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'email',
          //             width: '200px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Phone',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'phone',
          //             width: '200px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Address',
          //             type: 'string',
          //             model: table_prefix + 'd_example',
          //             field: 'address',
          //             width: '200px',
          //             filter: true,
          //             filterOptions: {
          //               enabled: true, // enable filter for this column
          //             },
          //           },
          //           {
          //             label: 'Avatar',
          //             type: 'image',
          //             style: { width: '30px', height: '30px' },
          //             field: 'avatar',
          //           },
          //         ],
          //       },
          //       api: {
          //         model: table_prefix + 'd_example',
          //         root: 'data',
          //         parameters: {
          //           paginate: 25,
          //         },
          //         cache: false,
          //       },
          //     },
          //     listener: () => {
          //       //
          //     },
          //     rules: ['required', 'min:3'],
          //   },
          // },
          {
            field: 'courseSubDtl.value',
            type: 'editor',
            width: '180px',
            label: 'Course Sub Detail 1',
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'Course Sub Detail 1',
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
                  maxHeight: '255px',
                },
              },
              listener: (element, type, value) => {
                if (type === 'change') {
                  console.log(value)
                }
              },
              properties: [
                {
                  field: 'text',
                  type: 'editor',
                  width: '180px',
                  label: 'Text',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'textfield',
                    default: 'helo text',
                    options: {
                      placeholder: 'text',
                      readonly: false,
                      disabled: false,
                    },
                    listener: (element, type, value) => {
                      if (type === 'change') {
                        if (value) {
                          // element.setValue({
                          //   field: 'number',
                          //   value: 1900
                          // })
                          //
                          // element.setValue({
                          //   field: 'password',
                          //   value: 'pass'
                          // })
                          //
                          // element.setValue({
                          //   field: 'textarea',
                          //   value:
                          //     'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been'
                          // })
                          //
                          // element.setValue({
                          //   field: 'selectv1',
                          //   value: '117_a119'
                          // })
                          //
                          // element.setValue({
                          //   field: 'selectv1multiple',
                          //   value: ['117_a119', '1b_48151']
                          // })
                          // element.setValue({
                          //   field: 'selectv2',
                          //   value: '_8af891'
                          // })
                          //
                          // element.setValue({
                          //   field: 'selectv2multiple',
                          //   value: ['_8af891']
                          // })
                          // element.setValue({
                          //   field: 'tags',
                          //   value: ['abc', 'bca']
                          // })
                          //
                          // element.setValue({
                          //   field: 'date',
                          //   value: '2022-12-28 17:30'
                          // })
                          //
                          // element.setValue({
                          //   field: 'daterange',
                          //   value: {
                          //     start: '2022-12-25 03:00',
                          //     end: '2022-12-28 17:30'
                          //   }
                          // })
                          // element.setValue({
                          //   field: 'selectv1',
                          //   value: 1
                          // })
                        } else {
                          // element.setValue({
                          //   field: 'number',
                          //   value: 0
                          // })
                          //
                          // element.setValue({
                          //   field: 'password',
                          //   value: ''
                          // })
                          //
                          // element.setValue({
                          //   field: 'textarea',
                          //   value: ''
                          // })
                          //
                          // element.setValue({
                          //   field: 'selectv1',
                          //   value: '117_a119'
                          // })
                          //
                          // element.setValue({
                          //   field: 'selectv1multiple',
                          //   value: []
                          // })

                          element.setValue({
                            field: 'selectv2',
                            value: '',
                          })

                          element.setValue({
                            field: 'selectv2multiple',
                            value: [],
                          })

                          // element.setValue({
                          //   field: 'tags',
                          //   value: []
                          // })
                          //
                          // element.setValue({
                          //   field: 'date',
                          //   value: ''
                          // })
                          //
                          // element.setValue({
                          //   field: 'daterange',
                          //   value: {
                          //     start: '',
                          //     end: ''
                          //   }
                          // })
                          // element.setValue({
                          //   field: 'name',
                          //   value: ''
                          // })
                        }
                      }
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'number',
                  type: 'editor',
                  width: '180px',
                  label: 'Number',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'numberfield',
                    default: 100,
                    options: {
                      placeholder: 'number',
                      readonly: false,
                      disabled: false,
                    },
                    listener: (element, type) => {
                      if (type === 'change') {
                        //
                      }
                    },
                    rules: ['required', 'min:5', 'max:5000'],
                  },
                },
                // {
                //   field: 'phone',
                //   type: 'editor',
                //   width: '180px',
                //   label: 'Phone',
                //   tdClass: 'custom-title',
                //   editor: {
                //     type: 'phonefield',
                //     default: '',
                //     options: {
                //       placeholder: '+62 000-0000-0000',
                //       readonly: false,
                //       disabled: false,
                //       output: '+NN NNN-NNNN-NNNN',
                //     },
                //     rules: ['required'],
                //   },
                // },
                {
                  field: 'password',
                  type: 'editor',
                  width: '180px',
                  label: 'Password',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'passwordfield',
                    default: 'password',
                    options: {
                      placeholder: 'password',
                      readonly: false,
                      disabled: false,
                    },
                    listener: (element, type, value) => {
                      if (type === 'change') {
                        //
                      }
                    },
                    rules: ['required', 'min:6', 'max:10'],
                  },
                },
                {
                  field: 'textarea',
                  type: 'editor',
                  width: '180px',
                  label: 'Textarea',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'textareafield',
                    default: 'helo text',
                    options: {
                      placeholder: 'title',
                      readonly: false,
                      disabled: false,
                    },
                    listener: (element, type, value) => {
                      if (type === 'change') {
                        //
                      }
                    },
                    rules: ['required', 'min:10', 'max:30'],
                  },
                },
                {
                  field: 'uploadimage',
                  type: 'editor',
                  width: '180px',
                  label: 'Upload Image',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'filefield',
                    default: null,
                    options: {
                      clearable: true,
                      viewer: 'image',
                      placeholder: 'Upload an image',
                      readonly: false,
                      disabled: false,
                    },
                    rules: ['required', 'mimes:jpg,png,jpeg,webp,gif'],
                  },
                },
                {
                  field: 'uploadpdf',
                  type: 'editor',
                  width: '180px',
                  label: 'Upload PDF',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'filefield',
                    default: null,
                    options: {
                      clearable: true,
                      viewer: 'pdf',
                      placeholder: 'Upload a PDF',
                      readonly: false,
                      disabled: false,
                    },
                    rules: ['required', 'mimes:pdf'],
                  },
                },
                {
                  field: 'texteditor',
                  type: 'editor',
                  width: '180px',
                  label: 'Text Editor',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'texteditorfield',
                    version: 1,
                    default: 'ini <b>text editor</b>',
                    options: {
                      clearable: true,
                      placeholder: 'Text Editor',
                      readonly: false,
                      disabled: false,
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'selectv1',
                  type: 'editor',
                  width: '180px',
                  label: 'Select',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'selectfield',
                    version: 1,
                    default: '',
                    options: {
                      placeholder: 'Select one',
                      readonly: false,
                      disabled: false,
                      multiple: false,
                      field: {
                        value: 'id',
                        display: 'username',
                      },
                      api: {
                        model: table_prefix + 'd_example',
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
                  field: 'selectv1multiple',
                  type: 'editor',
                  width: '180px',
                  label: 'Select Multiple',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'selectfield',
                    version: 1,
                    default: '',
                    options: {
                      placeholder: 'Select one',
                      readonly: false,
                      disabled: false,
                      multiple: true,
                      field: {
                        value: 'id',
                        display: 'username',
                      },
                      api: {
                        model: table_prefix + 'd_example',
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
                    rules: ['required', 'min:3', 'max:5'],
                  },
                },
                {
                  field: 'selectv2',
                  type: 'editor',
                  width: '180px',
                  label: 'Select2',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'selectfield',
                    version: 2,
                    options: {
                      placeholder: 'Select one',
                      readonly: false,
                      disabled: false,
                      multiple: false,
                      clearable: true,
                      field: {
                        value: 'id',
                        display: 'username',
                      },
                      api: {
                        model: table_prefix + 'd_example',
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
                  field: 'selectv2multiple',
                  type: 'editor',
                  width: '180px',
                  label: 'Select2 Multiple',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'selectfield',
                    version: 2,
                    default: [],
                    options: {
                      placeholder: 'Select many',
                      readonly: false,
                      disabled: false,
                      multiple: true,
                      clearable: true,
                      field: {
                        value: 'id',
                        display: 'username',
                      },
                      api: {
                        model: table_prefix + 'd_example',
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
                    rules: ['required', 'min:3', 'max:5'],
                  },
                },
                {
                  field: 'date',
                  type: 'editor',
                  width: '180px',
                  label: 'Date',
                  editor: {
                    type: 'datefield',
                    default: 'now',
                    options: {
                      readonly: false,
                      disabled: false,
                      placeholder: 'DD-MM-YYYY',
                      output: 'DD-MM-YYYY',
                    },
                    listener: () => {
                      //
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'datetime',
                  type: 'editor',
                  width: '180px',
                  label: 'DateTime',
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
                    rules: ['required'],
                  },
                },
                {
                  field: 'time',
                  type: 'editor',
                  width: '180px',
                  label: 'Time',
                  editor: {
                    type: 'timefield',
                    default: 'now',
                    options: {
                      placeholder: 'time',
                      readonly: false,
                      disabled: false,
                      output: 'HH:mm',
                    },
                    listener: () => {
                      //
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'daterange',
                  type: 'editor',
                  width: '180px',
                  label: 'Daterange',
                  editor: {
                    type: 'daterangefield',
                    default: { start: 'now', end: 'tomorrow' },
                    options: {
                      readonly: false,
                      disabled: false,
                      placeholder: 'DD-MM-YYYY',
                      output: 'DD-MM-YYYY',
                    },
                    listener: () => {
                      //
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'datetimerange',
                  type: 'editor',
                  width: '180px',
                  label: 'Datetimerange',
                  editor: {
                    type: 'datetimerangefield',
                    default: { start: 'now', end: 'tomorrow' },
                    options: {
                      readonly: false,
                      disabled: false,
                      in24h: true,
                      placeholder: 'DD-MM-YYYY HH:mm',
                      output: 'DD-MM-YYYY HH:mm',
                    },
                    listener: () => {
                      //
                    },
                    rules: ['required'],
                  },
                },
                // {
                //   field: 'popup',
                //   type: 'editor',
                //   width: '180px',
                //   label: 'Popup',
                //   tdClass: 'custom-title',
                //   editor: {
                //     type: 'popupfield',
                //     default: 1,
                //     options: {
                //       placeholder: 'Select one',
                //       readonly: false,
                //       disabled: false,
                //       multiple: false,
                //       clearable: true,
                //       tableConfigs: {
                //         fixedHeader: false,
                //         maxHeight: '255px',
                //       },
                //       field: {
                //         value: 'id',
                //         display: 'username',
                //         columns: [
                //           {
                //             label: 'First Name',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'first_name',
                //             width: '150px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Last Name',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'last_name',
                //             width: '150px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Gender',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'gender',
                //             width: '150px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Age',
                //             type: 'number',
                //             model: table_prefix + 'd_example',
                //             field: 'age',
                //             width: '200px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Username',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'username',
                //             width: '150px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Email',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'email',
                //             width: '200px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Phone',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'phone',
                //             width: '200px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Address',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'address',
                //             width: '200px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Avatar',
                //             type: 'image',
                //             style: { width: '30px', height: '30px' },
                //             field: 'avatar',
                //           },
                //         ],
                //       },
                //       api: {
                //         model: table_prefix + 'd_example',
                //         root: 'data',
                //         parameters: {
                //           paginate: 25,
                //         },
                //         cache: false,
                //       },
                //     },
                //     listener: () => {
                //       //
                //     },
                //     rules: ['required'],
                //   },
                // },
                // {
                //   field: 'popupmultiple',
                //   type: 'editor',
                //   width: '180px',
                //   label: 'Popup Multiple',
                //   tdClass: 'custom-title',
                //   editor: {
                //     type: 'popupfield',
                //     default: [],
                //     options: {
                //       placeholder: 'Select many',
                //       readonly: false,
                //       disabled: false,
                //       multiple: true,
                //       clearable: true,
                //       tableConfigs: {
                //         fixedHeader: false,
                //         maxHeight: '255px',
                //       },
                //       field: {
                //         value: 'id',
                //         display: 'username',
                //         columns: [
                //           {
                //             label: 'First Name',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'first_name',
                //             width: '150px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Last Name',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'last_name',
                //             width: '150px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Gender',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'gender',
                //             width: '150px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Age',
                //             type: 'number',
                //             model: table_prefix + 'd_example',
                //             field: 'age',
                //             width: '200px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Username',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'username',
                //             width: '150px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Email',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'email',
                //             width: '200px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Phone',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'phone',
                //             width: '200px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Address',
                //             type: 'string',
                //             model: table_prefix + 'd_example',
                //             field: 'address',
                //             width: '200px',
                //             filter: true,
                //             filterOptions: {
                //               enabled: true, // enable filter for this column
                //             },
                //           },
                //           {
                //             label: 'Avatar',
                //             type: 'image',
                //             style: { width: '30px', height: '30px' },
                //             field: 'avatar',
                //           },
                //         ],
                //       },
                //       api: {
                //         model: table_prefix + 'd_example',
                //         root: 'data',
                //         parameters: {
                //           paginate: 25,
                //         },
                //         cache: false,
                //       },
                //     },
                //     listener: () => {
                //       //
                //     },
                //     rules: ['required', 'min:3'],
                //   },
                // },
                {
                  label: 'field.action',
                  type: 'action',
                  width: '40px',
                  field: 'action',
                },
              ],
              rules: ['required', 'min:1'],
            },
          },
          {
            field: 'tags',
            type: 'editor',
            width: '180px',
            label: 'Tags',
            editor: {
              type: 'tagsfield',
              default: [],
              options: {
                placeholder: 'tags',
                readonly: false,
                disabled: false,
              },
              listener: (_) => {
                //
              },
              rules: ['required'],
            },
          },
          {
            field: 'date',
            type: 'editor',
            width: '180px',
            label: 'Date',
            editor: {
              type: 'datefield',
              default: 'now',
              options: {
                readonly: false,
                disabled: false,
                placeholder: 'DD-MM-YYYY',
                output: 'DD-MM-YYYY',
              },
              listener: () => {
                //
              },
              rules: ['required'],
            },
          },
          {
            field: 'datetime',
            type: 'editor',
            width: '180px',
            label: 'DateTime',
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
              rules: ['required'],
            },
          },
          {
            field: 'time',
            type: 'editor',
            width: '180px',
            label: 'Time',
            editor: {
              type: 'timefield',
              default: 'now',
              options: {
                placeholder: 'time',
                readonly: false,
                disabled: false,
                output: 'HH:mm',
              },
              listener: () => {
                //
              },
              rules: ['required'],
            },
          },
          {
            field: 'daterange',
            type: 'editor',
            width: '180px',
            label: 'Daterange',
            editor: {
              type: 'daterangefield',
              default: { start: 'now', end: 'tomorrow' },
              options: {
                readonly: false,
                disabled: false,
                placeholder: 'DD-MM-YYYY',
                output: 'DD-MM-YYYY',
              },
              listener: () => {
                //
              },
              rules: ['required'],
            },
          },
          {
            field: 'datetimerange',
            type: 'editor',
            width: '180px',
            label: 'Datetimerange',
            editor: {
              type: 'datetimerangefield',
              default: { start: 'now', end: 'tomorrow' },
              options: {
                readonly: false,
                disabled: false,
                in24h: true,
                placeholder: 'DD-MM-YYYY HH:mm',
                output: 'DD-MM-YYYY HH:mm',
              },
              listener: () => {
                //
              },
              rules: ['required'],
            },
          },
          {
            field: 'pinpoints',
            type: 'editor',
            width: '300px',
            label: 'Pinpoints',
            tdClass: 'custom-title',
            editor: {
              type: 'pinpointfield',
              default: [],
              options: {
                placeholder: 'Select many',
                readonly: false,
                disabled: false,
                multiple: true,
                clearable: true,
              },
              listener: () => {
                //
              },
              rules: ['required', 'min:3'],
            },
          },
          {
            field: 'location_point',
            type: 'editor',
            width: '300px',
            label: 'Coordinate of Office',
            tdClass: 'custom-title',
            editor: {
              type: 'pinpointfield',
              default: [],
              options: {
                placeholder: 'Select many',
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
            type: 'action',
            width: '80px',
            label: 'field.action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                onRemoveRow: (index) => {
                  if (schema.forms[1].type === 'detail') {
                    schema.forms[1].rows.splice(index, 1)
                  }
                },
              },
            },
          },
        ],
        rows: [],
      },
    ],
  }) as Schema
  provide('schema', schema)

  const methods = {
    onSubmit: () => {
      let errors = 0
      errors += formHeaderRefs.value.getValid()
      errors += formDetailRefs.value.getValidRows()

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
      }
    },
  }
</script>
