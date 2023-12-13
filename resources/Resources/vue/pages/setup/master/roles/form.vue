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
              :route="pageConfigs.landingRoute"
              :params="route.query"
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
        <Tabs :items="pageConfigs.tabs">
          <template #tab-1>
            <FormDetail
              ref="formDetailRefsDetailTab1"
              v-model:form="schema.forms[1]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab-2>
            <FormDetail
              ref="formDetailRefsDetailTab2"
              v-model:form="schema.forms[2]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab-3>
            <FormDetail
              ref="formDetailRefsDetailTab3"
              v-model:form="schema.forms[3]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab-4>
            <FormDetail
              ref="formDetailRefsDetailTab4"
              v-model:form="schema.forms[4]"
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
    t,
  } from '~/services'
  import { useConfigStore } from '~/store/config'
  import router from '~/router'
  import { Tabs } from '~/components/molecules'

  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'setup-master-roles'
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
        type: 'text-link',
        text: 'Roles',
        lang: 'menu.roles',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 'm_roles',
    landingRoute,
    mode: '',
    tabs: [
      {
        key: 'tab-1',
        label: 'Users',
        icon: '<i class="fa-light fa-users mr-2"></i>',
        active: true,
      },
      {
        key: 'tab-2',
        label: 'Desktop Permissions',
        icon: '<i class="fa-light fa-desktop mr-2"></i>',
      },
      {
        key: 'tab-3',
        label: 'Mobile Permissions',
        icon: '<i class="fa-light fa-mobile mr-2"></i>',
      },
      {
        key: 'tab-4',
        label: 'API Permissions',
        icon: '<i class="fa-light fa-chart-network mr-2"></i>',
      },
    ],
  })

  defineComponent({
    name: 'MasRolesForm',
  })

  const formHeaderRefs = ref()
  const formDetailRefsDetailTab1 = ref()
  const formDetailRefsDetailTab2 = ref()
  const formDetailRefsDetailTab3 = ref()
  const formDetailRefsDetailTab4 = ref()
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
        model: table_prefix + 'm_roles',
        key: 'form-roles',
        ready: 0,
        docs: false,
        properties: {
          name: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Role Name',
              information: 'Role name',
              inline: true,
              placeholder: '',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            listener: () => {
              //
            },
            // rules: ['required', 'no_special'],
            rules: ['required'],
          },
          note: {
            type: 'textareafield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Note',
              information: 'Catatan (optional)',
              inline: true,
              placeholder: '',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
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
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Active Flag',
              information: 'Untuk menonaktifkan role',
              inline: true,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            listener: (_) => {
              //
            },
          },
          errors: null,
        },
      },
      {
        type: 'detail',
        relation: {
          model: `${table_prefix}m_roles`,
          column: `${table_prefix}m_roles_d_users`,
          key: 'form-roles',
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
              maxHeight: '455px',
              lineNumbers: true,
            },
            uniqueColumn: 'id',
            includes: ['id:user_id'],
            columns: [
              {
                label: 'Username',
                field: 'username',
                model: `${table_prefix}m_users`,
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
                model: `${table_prefix}m_users`,
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
                model: `${table_prefix}m_users`,
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
              {
                label: 'Name',
                field: 'name',
                model: `${table_prefix}m_users`,
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
            ],
            api: {
              model: `${table_prefix}m_users`,
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
          maxHeight: '350px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'name',
            type: 'string',
            label: 'Name',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'username',
            type: 'string',
            label: 'Username',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'email',
            type: 'string',
            label: 'Email',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'phone',
            type: 'string',
            label: 'Phone',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            label: 'Photo',
            type: 'image',
            field: 'avatar',
            fieldText: 'name',
            style: {
              width: '30px',
              height: '30px',
              objectFit: 'scale-down',
              backgroundColor: '#eee',
              borderRadius: '50%',
            },
          },
          {
            type: 'action',
            width: '80px',
            label: 'Action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index, id) => {
                  if (schema.forms[1].type === 'detail' && id) {
                    schema.forms[1].rows.forEach((row, rowIndex) => {
                      if (row.id === id && schema.forms[1].type === 'detail') {
                        schema.forms[1].rows.splice(rowIndex, 1)
                      }
                    })
                  }
                },
              },
            },
          },
        ],
        includes: ['user_id'],
        transform: [
          'id:user_id',
          'username:user.username',
          'email:user.email',
          'name:user.name',
          'phone:user.phone',
        ],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          model: `${table_prefix}m_roles`,
          column: `${table_prefix}m_roles_d_ui_pc_permissions`,
          key: 'form-roles',
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
              maxHeight: '455px',
            },
            uniqueColumn: 'id',
            includes: ['id:menu_id'],
            columns: [
              {
                label: 'Menu Name',
                field: 'name',
                model: `${table_prefix}m_menu`,
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
              {
                label: 'Module',
                field: 'module',
                model: `${table_prefix}m_menu`,
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
              {
                label: 'Submodule',
                field: 'submodule',
                model: `${table_prefix}m_menu`,
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
              {
                label: 'Path URL',
                field: 'path_url',
                model: `${table_prefix}m_menu`,
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
            ],
            api: {
              model: `${table_prefix}m_menu`,
              root: 'data',
              parameters: {
                paginate: 25,
                where: `${table_prefix}m_menu.active_flag=1`,
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
          maxHeight: '350px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'name',
            type: 'string',
            width: '200px',
            label: 'Menu Name',
            sortable: true,
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'module',
            type: 'string',
            width: '200px',
            label: 'Module',
            sortable: true,
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'submodule',
            type: 'string',
            width: '200px',
            label: 'Submodule',
            sortable: true,
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'select_all',
            type: 'editor',
            width: '180px',
            label: 'Select All',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) => {
                if (type === 'change') {
                  console.log('beraksi')
                  element.setValue({
                    field: 'view',
                    value: value,
                  })
                  element.setValue({
                    field: 'preview',
                    value: value,
                  })
                  element.setValue({
                    field: 'create',
                    value: value,
                  })
                  element.setValue({
                    field: 'edit',
                    value: value,
                  })
                  element.setValue({
                    field: 'delete',
                    value: value,
                  })
                }
              },
              rules: [],
            },
          },
          {
            field: 'view',
            type: 'editor',
            width: '180px',
            label: 'View',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'preview',
            type: 'editor',
            width: '180px',
            label: 'Preview',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'create',
            type: 'editor',
            width: '180px',
            label: 'Create',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'edit',
            type: 'editor',
            width: '180px',
            label: 'Edit',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'delete',
            type: 'editor',
            width: '180px',
            label: 'Delete',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'customs',
            type: 'editor',
            width: '180px',
            label: 'Customs',
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'Customs',
                disabled: false,
                addRow: {
                  from: 'empty',
                  active: true,
                  className: 'text-white',
                  disabled: false,
                },
                modal: {
                  width: 'w-5/12 max-w-full',
                },
                clearAllRow: {
                  active: true,
                  disabled: false,
                },
                tableConfigs: {
                  fixedHeader: true,
                  maxHeight: '355px',
                  lineNumbers: true,
                },
              },
              properties: [
                {
                  field: 'name',
                  type: 'editor',
                  label: 'Permission Name',
                  editor: {
                    type: 'textfield',
                    default: '',
                    options: {
                      placeholder: '',
                      readonly: false,
                      disabled: false,
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'allow',
                  type: 'editor',
                  label: 'Allow',
                  sortable: false,
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
                  label: 'Action',
                  type: 'action',
                  width: '80px',
                  field: 'action',
                },
              ],
              rules: [],
            },
          },
          {
            type: 'action',
            width: '80px',
            label: 'Action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index, id) => {
                  if (schema.forms[2].type === 'detail' && id) {
                    schema.forms[2].rows.forEach((row, rowIndex) => {
                      if (row.id === id && schema.forms[2].type === 'detail') {
                        schema.forms[2].rows.splice(rowIndex, 1)
                      }
                    })
                  }
                },
              },
            },
          },
        ],
        includes: ['menu_id'],
        transform: [
          'id:menu_id',
          'name:menu.name',
          'module:menu.module',
          'submodule:menu.submodule',
        ],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          model: `${table_prefix}m_roles`,
          column: `${table_prefix}m_roles_d_ui_mb_permissions`,
          key: 'form-roles',
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
          maxHeight: '350px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'name',
            type: 'editor',
            width: '180px',
            label: 'Name',
            sortable: false,
            editor: {
              type: 'textfield',
              default: '',
              options: {
                placeholder: 'Permission name',
                readonly: false,
                disabled: false,
              },
              rules: ['required'],
            },
          },
          {
            field: 'module',
            type: 'editor',
            width: '180px',
            label: 'Module',
            sortable: false,
            editor: {
              type: 'textfield',
              default: '',
              options: {
                placeholder: 'Permission module',
                readonly: false,
                disabled: false,
              },
              rules: [],
            },
          },
          {
            field: 'submodule',
            type: 'editor',
            width: '180px',
            label: 'Submodule',
            sortable: false,
            editor: {
              type: 'textfield',
              default: '',
              options: {
                placeholder: 'Submodule name',
                readonly: false,
                disabled: false,
              },
              rules: [],
            },
          },
          {
            field: 'select_all',
            type: 'editor',
            label: 'Select All',
            width: '180px',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) => {
                if (type === 'change') {
                  element.setValue({
                    field: 'view',
                    value: value,
                  })
                  element.setValue({
                    field: 'preview',
                    value: value,
                  })
                  element.setValue({
                    field: 'create',
                    value: value,
                  })
                  element.setValue({
                    field: 'edit',
                    value: value,
                  })
                  element.setValue({
                    field: 'delete',
                    value: value,
                  })
                }
              },
              rules: [],
            },
          },
          {
            field: 'view',
            type: 'editor',
            width: '180px',
            label: 'View',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'preview',
            type: 'editor',
            width: '180px',
            label: 'Preview',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'create',
            type: 'editor',
            width: '180px',
            label: 'Create',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'edit',
            type: 'editor',
            width: '180px',
            label: 'Edit',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'delete',
            type: 'editor',
            width: '180px',
            label: 'Delete',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'customs',
            type: 'editor',
            width: '180px',
            label: 'Customs',
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'Customs',
                disabled: false,
                addRow: {
                  from: 'empty',
                  active: true,
                  className: 'text-white',
                  disabled: false,
                },
                modal: {
                  width: 'w-5/12 max-w-full',
                },
                clearAllRow: {
                  active: true,
                  disabled: false,
                },
                tableConfigs: {
                  fixedHeader: true,
                  maxHeight: '355px',
                  lineNumbers: true,
                },
              },
              properties: [
                {
                  field: 'name',
                  type: 'editor',
                  label: 'Permission Name',
                  editor: {
                    type: 'textfield',
                    default: '',
                    options: {
                      placeholder: '',
                      readonly: false,
                      disabled: false,
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'allow',
                  type: 'editor',
                  label: 'Allow',
                  sortable: false,
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
                  label: 'Action',
                  type: 'action',
                  width: '80px',
                  field: 'action',
                },
              ],
              rules: [],
            },
          },
          {
            type: 'action',
            width: '80px',
            label: 'Action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index, id) => {
                  if (schema.forms[3].type === 'detail') {
                    schema.forms[3].rows.forEach((row, rowIndex) => {
                      if (schema.forms[3].type === 'detail') {
                        if (id && row.id === id) {
                          schema.forms[3].rows.splice(rowIndex, 1)
                        } else if (rowIndex === index) {
                          schema.forms[3].rows.splice(rowIndex, 1)
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
      {
        type: 'detail',
        relation: {
          model: `${table_prefix}m_roles`,
          column: `${table_prefix}m_roles_d_api_permissions`,
          key: 'form-roles',
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
              maxHeight: '455px',
            },
            uniqueColumn: 'id',
            includes: ['id:model_id'],
            columns: [
              {
                label: 'Model Name',
                field: 'name',
                model: `${table_prefix}m_models`,
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
            ],
            api: {
              model: `${table_prefix}m_models`,
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
          maxHeight: '350px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'name',
            type: 'string',
            label: 'Model Name',
            width: '200px',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'select_all',
            type: 'editor',
            width: '180px',
            label: 'Select All',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) => {
                if (type === 'change') {
                  element.setValue({
                    field: 'get',
                    value: value,
                  })
                  element.setValue({
                    field: 'find',
                    value: value,
                  })
                  element.setValue({
                    field: 'store',
                    value: value,
                  })
                  element.setValue({
                    field: 'update',
                    value: value,
                  })
                  element.setValue({
                    field: 'destroy',
                    value: value,
                  })
                }
              },
              rules: [],
            },
          },
          {
            field: 'get',
            type: 'editor',
            width: '180px',
            label: 'Get',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'find',
            type: 'editor',
            width: '180px',
            label: 'Find',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'store',
            type: 'editor',
            width: '180px',
            label: 'Store',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'update',
            type: 'editor',
            width: '180px',
            label: 'Update',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'destroy',
            type: 'editor',
            width: '180px',
            label: 'Destroy',
            sortable: false,
            editor: {
              type: 'checkboxfield',
              default: true,
              options: {
                readonly: false,
                disabled: false,
              },
              listener: (element, type, value) =>
                methods.checkAll(element, type, value),
              rules: [],
            },
          },
          {
            field: 'customs',
            type: 'editor',
            width: '180px',
            label: 'Customs',
            sortable: false,
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'Customs',
                disabled: false,
                addRow: {
                  from: 'empty',
                  active: true,
                  className: 'text-white',
                  disabled: false,
                },
                modal: {
                  width: 'w-5/12 max-w-full',
                },
                clearAllRow: {
                  active: true,
                  disabled: false,
                },
                tableConfigs: {
                  fixedHeader: true,
                  maxHeight: '355px',
                  lineNumbers: true,
                },
              },
              properties: [
                {
                  field: 'method',
                  type: 'editor',
                  width: '230px',
                  label: 'Method',
                  editor: {
                    type: 'selectfield',
                    version: 2,
                    default: '',
                    options: {
                      items: ['GET', 'POST'],
                      placeholder: 'Select one',
                      readonly: false,
                      disabled: false,
                      multiple: false,
                      clearable: true,
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'name',
                  type: 'editor',
                  label: 'Permission Name',
                  editor: {
                    type: 'textfield',
                    default: '',
                    options: {
                      placeholder: '',
                      readonly: false,
                      disabled: false,
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'allow',
                  type: 'editor',
                  label: 'Allow',
                  sortable: false,
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
                  label: 'Action',
                  type: 'action',
                  width: '80px',
                  field: 'action',
                },
              ],
              rules: [],
            },
          },
          {
            type: 'action',
            width: '80px',
            label: 'Action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index, id) => {
                  if (schema.forms[4].type === 'detail' && id) {
                    schema.forms[4].rows.forEach((row, rowIndex) => {
                      if (row.id === id && schema.forms[4].type === 'detail') {
                        schema.forms[4].rows.splice(rowIndex, 1)
                      }
                    })
                  }
                },
              },
            },
          },
        ],
        includes: ['model_id'],
        transform: ['id:model_id', 'name:model.name'],
        rows: [],
      },
    ],
  }) as Schema
  provide('schema', schema)

  const configStore = useConfigStore()
  const methods = {
    onLoadData: () => {
      const url =
        `/api/v1/${pageConfigs.model}/${route.query.id}?` +
        new URLSearchParams(route.query)
      Axios({
        url,
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

          if (route.query.force_permissions) {
            url += `?ignore_pusher=true&force_permissions=${route.query.force_permissions}`
          } else {
            url += `?ignore_pusher=true`
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
      errors += formDetailRefsDetailTab1.value.getValidRows()
      errors += formDetailRefsDetailTab2.value.getValidRows()

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
    checkAll: (element, type, value) => {
      if (type === 'change' || type === 'init') {
        const all = element.getValues()
        let counter = 0
        Object.entries(all).forEach(([key, val]) => {
          if (typeof val === 'boolean' && key !== 'select_all') {
            if (val) {
              counter++
            }
          }
        })

        element.setValue({
          field: 'select_all',
          value: counter >= 5,
        })
      }
    },
  }

  onMounted(() => {
    if (route.query.id) {
      configStore.backdrop.enabled = true
      methods.onLoadData()
    }
  })
</script>
