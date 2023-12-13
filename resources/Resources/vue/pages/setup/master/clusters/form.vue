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
          <template #tab3>
            <FormDetail
              ref="formDetailRefsTab3"
              v-model:form="schema.forms[3]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab4>
            <FormDetail
              ref="formDetailRefsTab4"
              v-model:form="schema.forms[4]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab5>
            <FormDetail
              ref="formDetailRefsTab5"
              v-model:form="schema.forms[5]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab6>
            <FormDetail
              ref="formDetailRefsTab6"
              v-model:form="schema.forms[6]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab7>
            <FormDetail
              ref="formDetailRefsTab7"
              v-model:form="schema.forms[7]"
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
  const landingRoute = 'setup-master-clusters'
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
        text: 'Clusters',
        lang: 'menu.clusters',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 'm_clusters',
    landingRoute,
    mode: '',
    tabs: [
      {
        key: 'tab1',
        label: 'Employees',
        icon: '<i class="fa-light fa-user-group"></i>',
        active: true,
      },
      {
        key: 'tab2',
        label: 'Securities',
        icon: '<i class="fa-light fa-user-police"></i>',
      },
      {
        key: 'tab3',
        label: 'Patrols',
        icon: '<i class="fa-light fa-map-location-dot"></i>',
      },
      {
        key: 'tab4',
        label: 'Office',
        icon: '<i class="fa-light fa-building-user"></i>',
      },
      {
        key: 'tab5',
        label: 'Schedules',
        icon: '<i class="fa-light fa-user-clock"></i>',
      },
      {
        key: 'tab6',
        label: 'Salaries',
        icon: '<i class="fa-light fa-money-check-dollar-pen"></i>',
      },
      {
        key: 'tab7',
        label: 'Incentives',
        icon: '<i class="fa-light fa-hands-holding-dollar"></i>',
      },
    ],
  })

  defineComponent({
    name: 'MasClustersForm',
  })

  const formHeaderRefs = ref()
  const formDetailRefsTab1 = ref()
  const formDetailRefsTab2 = ref()
  const formDetailRefsTab3 = ref()
  const formDetailRefsTab4 = ref()
  const formDetailRefsTab5 = ref()
  const formDetailRefsTab6 = ref()
  const formDetailRefsTab7 = ref()
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

  const insentive_temp = reactive({
    role: '',
  })
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
              label: 'field.name',
              information: 'Name of cluster',
              inline: true,
              placeholder: 'field.name',
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
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Phone',
              information: 'Phone number of cluster',
              inline: true,
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
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Email',
              information: 'Email address of cluster',
              inline: true,
              placeholder: 'example@mail.com',
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
              inline: true,
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
          radius_checkin: {
            type: 'numberfield',
            value: '',
            default: 30,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Radius Checkin',
              information: '',
              inline: true,
              placeholder: 'Write a number here',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['nullable', 'min:30', 'max:127'],
          },
          radius_patrol: {
            type: 'numberfield',
            value: '',
            default: 30,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Radius Patrol',
              information: '',
              inline: true,
              placeholder: 'Write a number here',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['nullable', 'min:30', 'max:127'],
          },
          errors: null,
        },
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_employees',
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
              maxHeight: '450px',
              fixedHeader: true,
              selectAll: false,
            },
            uniqueColumn: 'id',
            includes: ['id:employee_id'],
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
                label: 'Username',
                field: 'username',
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
              model: 'ihm_m_users',
              root: 'data',
              parameters: {
                paginate: 25,
                wherejsoncontainsin: 'roles=["ADMIN", "STAFF", "RW", "RT"]',
                scopes: 'filter_by_uncluster_employees',
                cluster_id: route.query.id,
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
          lineNumbers: true,
        },
        properties: [
          {
            field: 'name',
            type: 'string',
            label: 'Name',
          },
          {
            field: 'username',
            type: 'string',
            label: 'Name',
          },
          {
            field: 'phone',
            type: 'string',
            label: 'Phone',
          },
          {
            field: 'email',
            type: 'string',
            label: 'Email',
          },
          {
            field: 'role',
            type: 'string',
            label: 'Role',
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
          'id:employee_id', // ini wajib untuk wherenotin, ketika sudah ada yang di select
          'name:employee.name',
          'username:employee.username',
          'email:employee.email',
          'phone:employee.phone',
          'role:employee.role',
        ],
        includes: ['ihm_m_clusters_id', 'employee_id'],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_securities',
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
              maxHeight: '450px',
              fixedHeader: true,
              selectAll: false,
            },
            uniqueColumn: 'id',
            includes: ['id:security_id'],
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
                label: 'Username',
                field: 'username',
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
              model: 'ihm_m_users',
              root: 'data',
              parameters: {
                paginate: 25,
                wherejsoncontainsin: 'roles=["SECURITY"]',
                // scopes: 'filter_by_cluster_securities',
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
          lineNumbers: true,
        },
        properties: [
          {
            field: 'name',
            type: 'string',
            label: 'Name',
          },
          {
            field: 'username',
            type: 'string',
            label: 'Username',
          },
          {
            field: 'phone',
            type: 'string',
            label: 'Phone',
          },
          {
            field: 'email',
            type: 'string',
            label: 'Email',
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
        transform: [
          'id:security_id',
          'name:security.name',
          'username:security.username',
          'email:security.email',
          'phone:security.phone',
        ],
        includes: ['ihm_m_clusters_id', 'security_id'],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_patrols',
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
          fixedHeader: true,
          maxHeight: '255px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'day_type_id',
            type: 'editor',
            label: 'Day',
            sortable: false,
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
                    where: '`group`="TIPE HARI PATROL"',
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
            type: 'action',
            width: '80px',
            label: 'field.action',
            field: 'action',
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
        includes: ['ihm_m_clusters_id'],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_offices',
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
          fixedHeader: true,
          maxHeight: '255px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'name',
            type: 'editor',
            width: '180px',
            label: 'Name',
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
            field: 'type_id',
            type: 'editor',
            label: 'Office Type',
            sortable: false,
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
                    where: '`group`="TIPE KANTOR"',
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
            type: 'action',
            width: '80px',
            label: 'field.action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index) => {
                  if (schema.forms[4].type === 'detail') {
                    schema.forms[4].rows.splice(index, 1)
                  }
                },
              },
            },
          },
        ],
        transform: [],
        includes: ['ihm_m_clusters_id'],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_schedules',
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
          fixedHeader: true,
          maxHeight: '255px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'day_type_id',
            type: 'editor',
            label: 'Day',
            sortable: false,
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
                    where: '`group`="TIPE HARI PATROL"',
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
            field: 'ihm_m_cluster_d_schedules_s_shifts',
            type: 'editor',
            width: '180px',
            label: 'Shift',
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'Detail Shift',
                modal: {
                  width: 'w-7/12',
                },
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
                  field: 'shift_id',
                  type: 'editor',
                  width: '230px',
                  label: 'Shift',
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
                      },
                      api: {
                        model: table_prefix + 'm_general',
                        root: 'data',
                        parameters: {
                          paginate: 25,
                          where: "`group` = 'TIPE SHIFT'",
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
                  field: 'start_time',
                  type: 'editor',
                  width: '180px',
                  label: 'Start Time',
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
                  field: 'end_time',
                  type: 'editor',
                  width: '180px',
                  label: 'End Time',
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
            type: 'action',
            width: '80px',
            label: 'field.action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index) => {
                  if (schema.forms[5].type === 'detail') {
                    schema.forms[5].rows.splice(index, 1)
                  }
                },
              },
            },
          },
        ],
        transform: [],
        includes: ['ihm_m_clusters_id'],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_salaries',
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
          fixedHeader: true,
          maxHeight: '255px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'role_id',
            type: 'editor',
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
            field: 'ihm_m_cluster_d_salaries_s_tenures',
            type: 'editor',
            width: '180px',
            label: 'field.tenure',
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'Detail Tenur',
                modal: {
                  width: 'w-7/12',
                },
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
                  field: 'year',
                  type: 'editor',
                  width: '180px',
                  label: 'Years',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'numberfield',
                    default: 0,
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
                    rules: ['required'],
                  },
                },
                {
                  field: 'salary',
                  type: 'editor',
                  label: 'Salary',
                  editor: {
                    type: 'rupiahfield',
                    default: 0,
                    options: {
                      placeholder: 'Salary',
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
            type: 'action',
            width: '80px',
            label: 'field.action',
            field: 'action',
            actions: {
              removeRow: {
                active: true,
                disabled: pageConfigs.mode === 'preview',
                onRemoveRow: (index) => {
                  if (schema.forms[6].type === 'detail') {
                    schema.forms[6].rows.splice(index, 1)
                  }
                },
              },
            },
          },
        ],
        transform: [],
        includes: ['ihm_m_clusters_id'],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_incentives',
        },
        addRow: {
          from: 'empty',
          active: true,
          className: 'text-white',
          disabled: false,
        },
        clearAllRow: {
          active: pageConfigs.mode !== 'preview',
          disabled: false,
        },
        tableConfigs: {
          fixedHeader: true,
          maxHeight: '255px',
          lineNumbers: true,
        },
        properties: [
          {
            field: 'role_id',
            type: 'editor',
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
              rules: ['required'],
            },
          },
          {
            field: 'price',
            type: 'editor',
            label: 'field.price',
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
            field: 'ihm_m_cluster_d_incentives_s_excludes',
            type: 'editor',
            width: '180px',
            label: 'field.excludes',
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'Excludes of Employee',
                disabled: false,
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
                      maxHeight: '450px',
                      fixedHeader: true,
                      selectAll: false,
                    },
                    uniqueColumn: 'id',
                    includes: ['id:employee_id'],
                    columns: [
                      {
                        label: 'Role',
                        field: 'role',
                        model: 'ihm_m_users',
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
                        style: {
                          width: '30px',
                          height: '30px',
                          borderRadius: '50%',
                        },
                        label: 'Photo',
                        field: 'avatar',
                      },
                    ],
                    api: {
                      model: 'ihm_m_users',
                      root: 'data',
                      parameters: {
                        paginate: 25,
                      },
                      cache: false,
                      overrideParams: (oldParams) => {
                        if (!insentive_temp.role) {
                          Notyf({
                            type: 'warning',
                            message: 'Mohon tunggu sebentar',
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

                        oldParams[
                          'where'
                        ] = `ihm_m_users.role = '${insentive_temp.role}'`

                        return oldParams
                      },
                    },
                  },
                },
                clearAllRow: {
                  active: true,
                  disabled: false,
                },
                modal: {
                  width: 'w-4/12',
                },
                tableConfigs: {
                  fixedHeader: false,
                  maxHeight: '255px',
                },
              },
              listener: (element, type, value) => {
                if (type === 'focus') {
                  const selected_role = element.getValue({
                    field: 'role_id',
                  })
                  Axios({
                    method: 'GET',
                    url: '/api/v1/ihm_m_roles/' + selected_role,
                  }).then((res: any) => {
                    insentive_temp.role = res.data.data.name
                  })
                }
              },
              properties: [
                {
                  field: 'employee_id',
                  type: 'editor',
                  label: 'field.employee',
                  editor: {
                    type: 'selectfield',
                    version: 2,
                    options: {
                      placeholder: 'Select one',
                      readonly: false,
                      disabled: true,
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
                  label: 'field.action',
                  type: 'action',
                  width: '80px',
                  field: 'action',
                },
              ],
              rules: [],
            },
          },
          {
            field: 'active_flag',
            type: 'editor',
            label: 'Active Flag',
            width: '180px',
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
                  if (schema.forms[5].type === 'detail') {
                    schema.forms[5].rows.splice(index, 1)
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
      errors += formDetailRefsTab3.value.getValidRows()
      errors += formDetailRefsTab4.value.getValidRows()
      errors += formDetailRefsTab5.value.getValidRows()
      errors += formDetailRefsTab6.value.getValidRows()
      errors += formDetailRefsTab7.value.getValidRows()

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
