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
  import { useAuthStore } from '~/store/auth'

  const { t } = useI18n()
  const authStore = useAuthStore()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'cluster-master-admins'
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
        text: 'Admins',
        lang: 'menu.admins',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 'm_admins',
    landingRoute,
    mode: '',
  })

  defineComponent({
    name: 'MasUsersForm',
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
        model: table_prefix + 'm_admins',
        key: 'change-data',
        ready: 0,
        docs: false,
        properties: {
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
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
              display: '+NN NNN-NNNN-NNNN',
              output: 'NNNNNNNNNNNNN',
            },
            rules: ['required'],
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
                  oldParams['wherein'] = 'name=["ADMIN", "STAFF", "RT", "RW"]'
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
    ],
  }) as Schema
  provide('schema', schema)

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
          console.log(res.data.data)
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
