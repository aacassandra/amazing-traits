<script setup lang="ts">
  import SubmitButton from '~/components/atoms/buttons/SubmitButton.vue'
  import { Tabs } from '~/components/molecules'
  import FormHeader from '~/components/organism/FormHeader/index.vue'
  import FormDetail from '~/components/organism/FormDetail/index.vue'
  import { inject, onMounted, provide, reactive, ref } from 'vue'
  import { PageConfigs } from '~/types/pages/form/v1'
  import { Schema } from '~/types'
  import { useConfigStore } from '~/store/config'
  import { Axios, Notyf, Preparation, Swal, Transform, t } from '~/services'
  import router from '~/router'

  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const pageConfigs = inject('pageConfigs') as PageConfigs

  const formDetailRefsTab1 = ref()
  const formDetailRefsTab2 = ref()
  const formDetailRefsTab3 = ref()
  const formDetailRefsTab4 = ref()
  const formDetailRefsTab5 = ref()
  const formDetailRefsTab6 = ref()
  const formDetailRefsTab7 = ref()
  const formDetailRefsTab8 = ref()
  const formDetailRefsTab9 = ref()
  const formHeaderRefs = ref()
  const isSaving = ref(false)
  const clusterId = ref()
  const bot_phone_number = inject('bot_phone_number')

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
              label: 'Name',
              information: 'Name of cluster',
              inline: true,
              placeholder: 'Name',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['required'],
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
              disabled: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: [],
          },
          phone: {
            type: 'phonefield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Phone',
              information: 'Phone number of cluster admin',
              inline: true,
              placeholder: '+62 000-0000-0000',
              readonly: false,
              disabled: false,
              hidden: false,
              output: '+NN NNN-NNNN-NNNN',
            },
            rules: ['required'],
          },
          email: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Email',
              information: 'Email address of cluster admin',
              inline: true,
              placeholder: 'example@mail.com',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            rules: ['required'],
          },
          logo: {
            type: 'filefield',
            value: null,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Logo',
              viewer: 'image',
              information: 'Logo untuk klaster',
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
          picture: {
            type: 'filefield',
            value: null,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Picture',
              viewer: 'image',
              information: 'Gambar background untuk klaster',
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
          radius_checkin: {
            type: 'numberfield',
            value: '',
            default: 5,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Radius Checkin',
              information: 'Help text example: Radius Checkin',
              inline: true,
              placeholder: 'Write a number here',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['min:1'],
          },
          radius_patrol: {
            type: 'numberfield',
            value: '',
            default: 5,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Radius Patrol',
              information: 'Help text example: Radius Patrol',
              inline: true,
              placeholder: 'Write a number here',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['min:1'],
          },
          patrol_type_id: {
            type: 'selectfield',
            version: 2,
            value: null,
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Tipe Patrol',
              information: 'Tipe Patrol',
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
                  where: '`group`="TIPE PATROL"',
                },
                cache: false,
              },
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
                  if (schema.forms[1].type === 'detail') {
                    schema.forms[1].rows.splice(index, 1)
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
              default: null,
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
                  if (schema.forms[2].type === 'detail') {
                    schema.forms[2].rows.splice(index, 1)
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
                        scopes: 'filter_by_cluster',
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
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_weighting',
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
              selectAll: true,
              maxHeight: '455px',
              lineNumbers: true,
            },
            uniqueColumn: 'id',
            includes: ['value_1:type', 'value_2:value'],
            columns: [
              {
                label: 'Type',
                field: 'value_1',
                model: `${table_prefix}m_general`,
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
              {
                label: 'Value',
                field: 'value_2',
                model: `${table_prefix}m_general`,
                type: 'string',
                sortable: true,
                filter: false,
                filterOptions: {
                  enabled: false, // enable filter for this column
                },
              },
            ],
            api: {
              model: `${table_prefix}m_general`,
              root: 'data',
              parameters: {
                paginate: 25,
                where: "`group` = 'PEMBOBOTAN INSENTIF'",
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
            field: 'type',
            type: 'string',
            label: 'Type',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'value',
            type: 'editor',
            label: 'Value',
            editor: {
              type: 'numberfield',
              default: null,
              options: {
                placeholder: 'Value',
                readonly: false,
                disabled: false,
              },
              rules: ['required', 'decimal:2'],
            },
          },
        ],
        includes: ['type'],
        transform: [],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_group_wa',
        },
        addRow: {
          from: 'empty',
          active: true,
          className: 'text-white',
          disabled: false,
          max: 1,
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
            field: 'group_name',
            type: 'editor',
            label: 'field.group_name',
            editor: {
              type: 'textfield',
              default: null,
              options: {
                placeholder: 'field.group_name',
                readonly: false,
                disabled: false,
              },
              rules: ['required'],
            },
          },
          {
            field: 'admin_phone',
            type: 'editor',
            label: 'field.admin_phone',
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
            field: 'for_id',
            type: 'editor',
            label: 'field.for',
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
                    where: "`group` = 'GROUP WA UNTUK'",
                  },
                  cache: false,
                },
              },
              rules: ['required'],
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
                  if (schema.forms[7].type === 'detail') {
                    schema.forms[7].rows.splice(index, 1)
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
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_administrator',
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
            includes: ['id:user_id'],
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
                scopes: 'filter_by_cluster_employees',
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
            label: 'field.name',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'role',
            type: 'string',
            label: 'field.role',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'username',
            type: 'string',
            label: 'field.username',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'phone',
            type: 'string',
            label: 'field.phone',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'email',
            type: 'string',
            label: 'field.email',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            type: 'image',
            style: { width: '30px', height: '30px', borderRadius: '50%' },
            label: 'Photo',
            field: 'avatar',
          },
          {
            field: 'ihm_m_cluster_d_administrator_s_notification',
            type: 'editor',
            width: '180px',
            label: 'field.notification',
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'field.notification',
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
                    includes: ['value_1:notif_for'],
                    columns: [
                      {
                        label: 'field.notif_for',
                        field: 'value_1',
                        model: 'ihm_m_general',
                        type: 'string',
                        sortable: true,
                        filter: true,
                        filterOptions: {
                          enabled: true, // enable filter for this column
                        },
                      },
                    ],
                    api: {
                      model: 'ihm_m_general',
                      root: 'data',
                      parameters: {
                        paginate: 25,
                        where: "`group` = 'ADMINISTRATOR NOTIF AS'",
                      },
                      cache: false,
                      overrideParams: (oldParams) => {
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
                  width: 'w-6/12',
                },
                tableConfigs: {
                  fixedHeader: false,
                  maxHeight: '255px',
                },
                includes: ['notif_for'],
              },
              listener: (element, type, value) => {
                //
              },
              properties: [
                {
                  field: 'notif_for',
                  type: 'string',
                  label: 'field.notif_for',
                  filter: true,
                  filterOptions: {
                    enabled: true,
                  },
                },
                {
                  field: 'wa',
                  type: 'editor',
                  label: 'Whatsapp',
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
                  field: 'email',
                  type: 'editor',
                  label: 'field.email',
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
                  if (schema.forms[8].type === 'detail') {
                    schema.forms[8].rows.splice(index, 1)
                  }
                },
              },
            },
          },
        ],
        transform: [
          'name:user.name',
          'username:user.username',
          'role:user.role',
          'phone:user.phone',
          'email:user.email',
          'avatar:user.avatar',
        ],
        includes: ['user_id'],
        rows: [],
      },
      {
        type: 'detail',
        relation: {
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_m_cluster_d_schedulers',
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
            includes: ['id:command_id', 'value_1:command_name'],
            columns: [
              {
                label: 'field.command_name',
                field: 'value_1',
                model: `${table_prefix}m_general`,
                type: 'string',
                sortable: true,
                filter: true,
                filterOptions: {
                  enabled: true, // enable filter for this column
                },
              },
            ],
            api: {
              model: `${table_prefix}m_general`,
              root: 'data',
              parameters: {
                paginate: 25,
                where: "`group` = 'SCHEDULER COMMAND'",
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
            field: 'command_name',
            type: 'string',
            label: 'field.command_name',
            filter: true,
            filterOptions: {
              enabled: true,
            },
          },
          {
            field: 'ihm_m_cluster_d_schedulers_s',
            type: 'editor',
            width: '180px',
            label: 'field.schedulers',
            editor: {
              type: 'subdetailfield',
              options: {
                title: 'field.schedulers',
                disabled: false,
                addRow: {
                  from: 'empty',
                  active: true,
                  className: 'text-white',
                  disabled: false,
                  max: 3,
                },
                clearAllRow: {
                  active: true,
                  disabled: false,
                },
                modal: {
                  width: 'w-11/12',
                },
                tableConfigs: {
                  fixedHeader: false,
                  maxHeight: '255px',
                },
                includes: [],
              },
              listener: (element, type, value) => {
                //
              },
              properties: [
                {
                  field: 'minute',
                  sortable: false,
                  width: '180px',
                  type: 'editor',
                  label: 'field.minute',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'selectfield',
                    version: 1,
                    default: '-1',
                    options: {
                      placeholder: 'field.select_minute',
                      readonly: false,
                      disabled: false,
                      multiple: false,
                      field: {
                        value: 'value_1',
                        display: 'value_2',
                        type: {
                          value: 'string',
                          display: 'string',
                        },
                      },
                      api: {
                        model: table_prefix + 'm_general',
                        root: 'data',
                        parameters: {
                          paginate: 65,
                          where: "`group` = 'SCHEDULER SELECT MINUTE'",
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
                  field: 'hour',
                  sortable: false,
                  type: 'editor',
                  width: '180px',
                  label: 'field.hour',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'selectfield',
                    version: 1,
                    default: '-1',
                    options: {
                      placeholder: 'field.select_hour',
                      readonly: false,
                      disabled: false,
                      multiple: false,
                      field: {
                        value: 'value_1',
                        display: 'value_2',
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
                          where: "`group` = 'SCHEDULER SELECT HOUR'",
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
                  field: 'wday',
                  type: 'editor',
                  sortable: false,
                  width: '180px',
                  label: 'field.wday',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'selectfield',
                    version: 1,
                    default: -1,
                    options: {
                      placeholder: 'field.select_wday',
                      readonly: false,
                      disabled: false,
                      multiple: false,
                      field: {
                        value: 'value',
                        display: 'text',
                      },
                      items: [
                        {
                          value: -1,
                          text: 'options.every_day_of_week',
                        },
                        {
                          value: 0,
                          text: 'options.sunday',
                        },
                        {
                          value: 1,
                          text: 'options.monday',
                        },
                        {
                          value: 2,
                          text: 'options.tuesday',
                        },
                        {
                          value: 3,
                          text: 'options.wednesday',
                        },
                        {
                          value: 4,
                          text: 'options.thursday',
                        },
                        {
                          value: 5,
                          text: 'options.friday',
                        },
                        {
                          value: 6,
                          text: 'options.saturday',
                        },
                      ],
                    },
                    listener: () => {
                      //
                    },
                    rules: ['required'],
                  },
                },
                {
                  field: 'mday',
                  type: 'editor',
                  sortable: false,
                  width: '180px',
                  label: 'field.mday',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'selectfield',
                    version: 1,
                    default: -1,
                    options: {
                      placeholder: 'field.select_mday',
                      readonly: false,
                      disabled: false,
                      multiple: false,
                      field: {
                        value: 'value_1',
                        display: 'value_2',
                        type: {
                          value: 'string',
                          display: 'string',
                        },
                      },
                      api: {
                        model: table_prefix + 'm_general',
                        root: 'data',
                        parameters: {
                          paginate: 35,
                          where: "`group` = 'SCHEDULER SELECT DAY'",
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
                  field: 'month',
                  type: 'editor',
                  sortable: false,
                  width: '180px',
                  label: 'field.month',
                  tdClass: 'custom-title',
                  editor: {
                    type: 'selectfield',
                    version: 1,
                    default: -1,
                    options: {
                      placeholder: 'field.select_month',
                      readonly: false,
                      disabled: false,
                      multiple: false,
                      field: {
                        value: 'value',
                        display: 'text',
                      },
                      items: [
                        {
                          value: -1,
                          text: 'options.every_month',
                        },
                        {
                          value: 1,
                          text: 'options.january',
                        },
                        {
                          value: 2,
                          text: 'options.february',
                        },
                        {
                          value: 3,
                          text: 'options.march',
                        },
                        {
                          value: 4,
                          text: 'options.april',
                        },
                        {
                          value: 5,
                          text: 'options.may',
                        },
                        {
                          value: 6,
                          text: 'options.june',
                        },
                        {
                          value: 7,
                          text: 'options.july',
                        },
                        {
                          value: 8,
                          text: 'options.august',
                        },
                        {
                          value: 9,
                          text: 'options.september',
                        },
                        {
                          value: 10,
                          text: 'options.october',
                        },
                        {
                          value: 11,
                          text: 'options.november',
                        },
                        {
                          value: 12,
                          text: 'options.december',
                        },
                      ],
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
                  if (schema.forms[9].type === 'detail') {
                    schema.forms[9].rows.splice(index, 1)
                  }
                },
              },
            },
          },
        ],
        includes: ['command_id'],
        transform: ['command_name:command.value_1'],
        rows: [],
      },
    ],
  }) as Schema
  provide('schema', schema)

  const configStore = useConfigStore()
  const methods = {
    onLoadData: () => {
      Axios({
        url: `/api/v1/${pageConfigs.model}/current_cluster`,
      })
        .then((res: any) => {
          if (res.data.data.id) {
            clusterId.value = res.data.data.id
          } else {
            Notyf({
              type: 'error',
              message: 'Mohon maaf, Anda tidak berada di cluster manapun',
              duration: 3000,
              ripple: true,
              dismissible: true,
              position: {
                x: 'right',
                y: 'top',
              },
            })
          }

          Object.entries(res.data.data).forEach(([key, val]) => {
            Transform(schema, val, key)
          })
          configStore.backdrop.enabled = false
        })
        .catch((err) => {
          // Notyf({
          //   type: 'error',
          //   message: err.response.data.message,
          //   duration: 3000,
          //   ripple: true,
          //   dismissible: true,
          //   position: {
          //     x: 'right',
          //     y: 'top',
          //   },
          // })
        })
    },
    onSend: (json) => {
      for (let i = 0; i < schema.forms.length; i++) {
        const form = schema.forms[i]
        if (form.type === 'header') {
          isSaving.value = true

          let url = `/api/v1/${form.model}/${clusterId.value}`
          let method = 'PUT'

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
      errors += formDetailRefsTab8.value.getValidRows()
      errors += formDetailRefsTab9.value.getValidRows()

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

      let html = 'Are you sure? save this change, click Yes to continue'
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
            if (clusterId.value) {
              const debug = false
              if (!debug) {
                methods.onSend(json)
              } else {
                console.log(json)
              }
            } else {
              Notyf({
                type: 'error',
                message: 'Mohon maaf, Anda tidak berada di cluster manapun',
                duration: 3000,
                ripple: true,
                dismissible: true,
                position: {
                  x: 'right',
                  y: 'top',
                },
              })
            }
          })
        }
      })
    },
  }

  onMounted(() => {
    // configStore.backdrop.enabled = true
    methods.onLoadData()
  })
</script>

<template>
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
      <div v-if="bot_phone_number" class="alert">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          class="stroke-info shrink-0 w-6 h-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          ></path>
        </svg>
        <span
          >{{
            t({
              lang: 'others.please_ensure_group_wa',
              option: {
                phone_number: bot_phone_number,
              },
            })
          }}
        </span>
      </div>
      <FormDetail
        ref="formDetailRefsTab7"
        v-model:form="schema.forms[7]"
        class="container mx-auto px-0 my-4"
      />
    </template>
    <template #tab8>
      <FormDetail
        ref="formDetailRefsTab8"
        v-model:form="schema.forms[8]"
        class="container mx-auto px-0 my-4"
      />
    </template>
    <template #tab9>
      <FormDetail
        ref="formDetailRefsTab9"
        v-model:form="schema.forms[9]"
        class="container mx-auto px-0 my-4"
      />
    </template>
  </Tabs>

  <div class="grid grid-cols-12">
    <div class="col-span-12 flex justify-end pr-4">
      <SubmitButton :is-loading="isSaving" :on-click="methods.onSubmit">
        Update
      </SubmitButton>
    </div>
  </div>
</template>
