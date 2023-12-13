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
        <Tabs v-if="pageConfigs.mode === 'preview'" :items="tabs">
          <template #tab1>
            <FormDetail
              v-model:form="schema.forms[1]"
              class="container mx-auto px-0 my-4"
            />
          </template>
          <template #tab2>
            <FormDetail
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
  import { useAuthStore } from '~/store/auth'

  const { t } = useI18n()
  const authStore = useAuthStore()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'setup-master-users'
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
        text: 'Users',
        lang: 'menu.users',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 'm_users',
    landingRoute,
    mode: '',
  })

  defineComponent({
    name: 'MasUsersForm',
  })

  const formHeaderRefs = ref()
  const isSaving = ref(false)
  const route = useRoute()

  const tabs = ref<TabsType>([
    {
      key: 'tab1',
      label: 'History Device',
      icon: '<i class="fal fa-mobile"></i>',
      active: true,
    },
    {
      key: 'tab2',
      label: 'History Login',
      icon: '<i class="fal fa-door-open"></i>',
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
  })
  const schema = reactive({
    version: 1,
    mode: pageConfigs.mode,
    forms: [
      {
        type: 'header',
        model: table_prefix + 'm_users',
        key: 'change-data',
        ready: 0,
        docs: false,
        properties: {
          avatar: {
            type: 'filefield',
            value: null,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Avatar',
              viewer: 'image',
              information: 'Foto profil',
              inline: true,
              disabled: false,
              hidden: false,
              clearable: true,
              autoUpload: false,
            },
            hasChanged: false,
            rules: ['mimes:jpg,jpeg,png,webp,gif'],
          },
          username: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Username',
              information:
                'Username ini akan digunakan ketika login/masuk aplikasi',
              inline: true,
              placeholder: 'jhon_doe',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['required', 'no_space', 'no_special'],
          },
          name: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Name',
              information: '',
              inline: true,
              placeholder: 'Jhon',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            listener: () => {
              //
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
              information: 'Alamat email',
              inline: true,
              placeholder: 'example@gmail.com',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
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
              information: 'Phone number',
              inline: true,
              placeholder: '+62 000-0000-0000',
              readonly: false,
              disabled: false,
              hidden: false,
              display: '+NN NNN-NNNN-NNNN',
              output: 'NNNNNNNNNNNNN',
            },
            rules: ['required'],
          },
          gender: {
            type: 'selectfield',
            version: 2,
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            items: ['Laki-laki', 'Perempuan'],
            options: {
              label: 'Gender',
              information: 'Jenis kelamin',
              inline: true,
              placeholder: 'Gender',
              readonly: false,
              disabled: false,
              hidden: false,
              multiple: false,
              clearable: true,
            },
            rules: [],
          },
          date_birth: {
            type: 'datefield',
            value: '',
            default: 'now',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Date Of Birth',
              information: 'Tanggal lahir',
              inline: true,
              placeholder: 'DD-MM-YYYY',
              input: 'YYYY-MM-DD',
              output: 'DD-MM-YYYY',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            rules: [],
          },
          roles: {
            type: 'selectfield',
            version: 2,
            value: [],
            default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Roles',
              information: '',
              inline: true,
              placeholder: 'Select many',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
              multiple: true,
              clearable: true,
              field: {
                value: 'name',
                display: 'name',
              },
              api: {
                model: table_prefix + 'm_roles',
                root: 'data',
                parameters: {
                  paginate: 25,
                },
                overrideParams: (element, oldParams) => {
                  if (
                    !['SUPERADMIN', 'INTERNAL - ADMIN'].includes(
                      authStore.user.role,
                    )
                  ) {
                    oldParams['wherenotin'] =
                      'name=["SUPERADMIN", "INTERNAL - ADMIN"]'
                  }

                  return oldParams
                },
                cache: false,
              },
            },
            listener: () => {
              //
            },
            rules: ['required', 'min:1'],
          },
          address: {
            type: 'textareafield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Address',
              information: '',
              inline: true,
              placeholder: 'Write an address here',
              readonly: false,
              disabled: false,
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
              information: '',
              inline: true,
              placeholder: 'Write a description here',
              readonly: false,
              disabled: false,
              hidden: false,
            },
            rules: [],
          },
          province_id: {
            type: 'selectfield',
            version: 2,
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Province',
              information: 'Provinsi asal perumahan',
              inline: true,
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
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Regency',
              information: 'Kota asal perumahan',
              inline: true,
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
            rules: [],
          },
          password: {
            type: 'passwordfield',
            value: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Password',
              information:
                'Password adalah kata sandi untuk login/masuk aplikasi',
              inline: true,
              placeholder: '*****',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: pageConfigs.mode === 'preview',
              eye: {
                active: true,
                style: 'default',
              },
            },
            listener: () => {
              //
            },
            rules: [],
          },
          c_password: {
            type: 'passwordfield',
            value: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Password Confirmation',
              information: 'Password Confirmation adalah konfirmasi kata sandi',
              inline: true,
              placeholder: '*****',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: pageConfigs.mode === 'preview',
              eye: {
                active: true,
                style: 'default',
              },
            },
            listener: () => {
              //
            },
            rules: [],
          },
          active_flag: {
            type: 'switchfield',
            value: true,
            default: true,
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
    ],
  }) as Schema
  provide('schema', schema)

  if (pageConfigs.mode === 'preview') {
    schema.forms.push({
      type: 'detail',
      relation: {
        key: 'form',
        model: pageConfigs.model,
        column: 'm_users_d_device_histories',
      },
      addRow: {
        from: 'popup',
        active: false,
        disabled: true,
        options: {
          isMultiple: true,
          globalSearch: false,
          className: {
            addNewList: 'text-white',
            verivy: 'text-white',
          },
          tableConfigs: {
            fixedHeader: false,
            selectAll: false,
          },
          uniqueColumn: 'id',
          includes: [],
          columns: [
            {
              label: 'Name',
              field: 'name',
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
              type: 'string',
              sortable: true,
              filter: true,
              filterOptions: {
                enabled: true, // enable filter for this column
              },
            },
          ],
          api: {
            model: 'm_users',
            root: 'data',
            parameters: {
              paginate: 25,
            },
            cache: false,
          },
        },
      },
      clearAllRow: {
        active: false,
        disabled: false,
      },
      tableConfigs: {
        fixedHeader: false,
        maxHeight: '255px',
      },
      properties: [
        {
          field: 'brand',
          type: 'string',
          label: 'Brand',
        },
        {
          field: 'os',
          type: 'string',
          label: 'OS',
        },
        {
          field: 'version',
          type: 'string',
          label: 'Version',
        },
        {
          field: 'online',
          type: 'string',
          label: 'Current',
        },
      ],
      transform: [],
      includes: [],
      rows: [],
    })
    schema.forms.push({
      type: 'detail',
      relation: {
        key: 'form',
        model: pageConfigs.model,
        column: 'm_users_d_login_histories',
      },
      addRow: {
        from: 'popup',
        active: false,
        disabled: true,
        options: {
          isMultiple: true,
          globalSearch: false,
          className: {
            addNewList: 'text-white',
            verivy: 'text-white',
          },
          tableConfigs: {
            fixedHeader: false,
            selectAll: false,
          },
          uniqueColumn: 'id',
          includes: [],
          columns: [
            {
              label: 'Name',
              field: 'name',
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
              type: 'string',
              sortable: true,
              filter: true,
              filterOptions: {
                enabled: true, // enable filter for this column
              },
            },
          ],
          api: {
            model: 'm_users',
            root: 'data',
            parameters: {
              paginate: 25,
            },
            cache: false,
          },
        },
      },
      clearAllRow: {
        active: false,
        disabled: false,
      },
      tableConfigs: {
        fixedHeader: false,
        maxHeight: '255px',
      },
      properties: [
        {
          field: 'longlat_point',
          type: 'editor',
          width: '300px',
          label: 'Longlat Point',
          editor: {
            type: 'pinpointfield',
            default: [],
            options: {
              placeholder: 'Select many',
              readonly: false,
              disabled: true,
              multiple: false,
              clearable: true,
            },
            listener: () => {
              //
            },
            rules: [],
          },
        },
      ],
      transform: [],
      includes: [],
      rows: [],
    })
  }

  if (schema.forms[0].type === 'header') {
    if (pageConfigs.mode === 'create') {
      schema.forms[0].properties.password.rules = [
        'required',
        // 'min:6', // di handle backend
        // 'max:20',
        // 'min_number:1',
        // 'min_special:1',
        // 'min_uppercase:1',
        // 'min_lowercase:1',
      ]
      schema.forms[0].properties.c_password.rules = [
        'required',
        'match:password',
      ]
    } else if (pageConfigs.mode === 'edit') {
      schema.forms[0].properties.password.rules = []
      schema.forms[0].properties.c_password.rules = ['match:password']
    } else {
      //
    }
  }

  const configStore = useConfigStore()
  const methods = {
    onLoadData: () => {
      Axios({
        url: `/api/v1/${pageConfigs.model}/${route.query.id}`,
      })
        .then((res: any) => {
          temp.province_code = res.data.data['province.value_1']
          temp.regency_code = res.data.data['regency.value_1']
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
          console.log(schema)
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
