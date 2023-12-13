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

  const { t } = useI18n()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'setup-master-stores'
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
        text: 'Stores',
        lang: 'menu.stores',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 'm_stores',
    landingRoute,
    mode: '',
  })

  defineComponent({
    name: 'MasStoresForm',
  })

  const formHeaderRefs = ref()
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
        key: 'change-data',
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
              information: 'Phone number of cluster admin',
              inline: true,
              placeholder: '+62 000-0000-0000',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
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
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: [],
          },
          photo: {
            type: 'filefield',
            value: null,
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Photo',
              viewer: 'image',
              information: 'Photo of store',
              inline: true,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
              clearable: true,
              autoUpload: false,
            },
            hasChanged: false,
            rules: ['mimes:jpg,jpeg,png,webp'],
          },
          address: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Address',
              information: 'Address of store',
              inline: true,
              placeholder: 'Address',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
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
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            rules: [],
          },
          ihm_m_housings_id: {
            type: 'popupfield',
            value: '',
            // default: [],
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Housings',
              information: 'Choice Housings',
              inline: true,
              placeholder: 'Select one housings',
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
                display: 'name',
                columns: [
                  {
                    label: 'Name',
                    type: 'string',
                    model: table_prefix + 'm_housings',
                    field: 'name',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Phone',
                    type: 'string',
                    model: table_prefix + 'm_housings',
                    field: 'phone',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Email',
                    type: 'string',
                    model: table_prefix + 'm_housings',
                    field: 'email',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                  {
                    label: 'Address',
                    type: 'string',
                    model: table_prefix + 'm_housings',
                    field: 'address',
                    width: '200px',
                    filter: true,
                    filterOptions: {
                      enabled: true, // enable filter for this column
                    },
                  },
                ],
                type: {
                  value: 'encrypted',
                  display: 'string',
                },
              },
              api: {
                model: table_prefix + 'm_housings',
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
