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

        <div class="grid grid-cols-12 px-8 mx-auto pb-10">
          <div class="col-span-12">
            <div class="alert shadow-lg">
              <div>
                <h3 class="font-bold">
                  <i class="fa-light fa-mobile"></i> Device Info
                </h3>
                <div class="text-xs mt-4">
                  <div class="font-semibold flex">
                    <div style="min-width: 100px">Brand</div>
                    <div>: <span id="brand" class="font-normal"></span></div>
                  </div>
                  <div class="font-semibold flex">
                    <div style="min-width: 100px">OS</div>
                    <div>: <span id="os" class="font-normal"></span></div>
                  </div>
                  <div class="font-semibold flex">
                    <div style="min-width: 100px">OS Version</div>
                    <div>
                      : <span id="os_version" class="font-normal"></span>
                    </div>
                  </div>
                  <div class="font-semibold flex">
                    <div style="min-width: 100px">App Version</div>
                    <div>
                      : <span id="app_version" class="font-normal"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <Tabs :items="pageConfigs.tabs">
          <template #tab1>
            <div
              id="box-chats"
              class="overflow-y-scroll pr-3"
              style="height: 300px"
            >
              <div
                v-for="(chat, index) in chats"
                :key="index"
                :class="{
                  'chat mb-3': true,
                  'chat-start': chat.user_id,
                  'chat-end': chat.staff_id,
                }"
              >
                <div class="chat-image avatar">
                  <div class="w-10 rounded-full">
                    <img
                      :src="
                        chat['user.avatar'] ||
                        `https://avatar.oxro.io/avatar.svg?name=${chat['created.name']}&background=73bf94&color=000&length=1`
                      "
                    />
                  </div>
                </div>
                <div class="chat-header">
                  <time class="text-xs opacity-50 mt-3">
                    {{
                      moment(chat.created_at, 'DD/MM/YYYY HH:mm').format(
                        'DD-MM-YYYY HH:mm',
                      )
                    }}
                  </time>
                </div>
                <div class="chat-bubble">
                  <span v-html="chat.message"></span>
                </div>
              </div>
            </div>
            <div class="px-0 mt-2.5">
              <form class="flex" @submit.prevent="methods.sendMessage()">
                <input
                  v-model="chat.message"
                  :disabled="isClosed"
                  type="text"
                  class="w-full border border-gray-300 input focus:outline-none focus:border-blue-500"
                  placeholder="Type your message here..."
                />
                <button
                  type="submit"
                  :disabled="isClosed"
                  class="ml-2 btn btn-primary text-white rounded-lg"
                >
                  Send <i class="fal fa-plane-up"></i>
                </button>
              </form>
            </div>
          </template>
          <template #tab2>
            <FormDetail
              ref="formDetailRefsTab1"
              v-model:form="schema.forms[1]"
              class="container mx-auto px-0 my-4"
            />
          </template>
        </Tabs>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
  import {
    defineComponent,
    onMounted,
    onUnmounted,
    provide,
    reactive,
    ref,
    watch,
  } from 'vue'
  import { useRoute } from 'vue-router'
  import { PageConfigs } from '~/types/pages/form/v1'
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { Card } from '~/components/atoms'
  import FormHeader from '~/components/organism/FormHeader/index.vue'
  import FormDetail from '~/components/organism/FormDetail/index.vue'
  import { Schema } from '~/types'
  import BackButton from '~/components/atoms/buttons/BackButton.vue'
  import {
    Axios,
    Notyf,
    Preparation,
    Swal,
    Transform,
    Cryptor,
  } from '~/services'
  import { useConfigStore } from '~/store/config'
  import router from '~/router'
  import { useI18n } from 'vue-i18n'
  import { Tabs } from '~/components/molecules'
  import { useAuthStore } from '~/store/auth'
  import Echo from 'laravel-echo'
  import Pusher from 'pusher-js'
  import moment from 'moment'
  import { useWebsocketStore } from '~/store/websocket'

  const authStore = useAuthStore()
  const { t } = useI18n()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const landingRoute = 'cluster-transaction-app_reports'
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
        text: 'Transaction',
      },
      {
        type: 'text-link',
        text: 'Reports',
        lang: 'menu.reports',
        route: landingRoute,
      },
      {
        type: 'text',
        text: '',
      },
    ],
    title: '',
    model: table_prefix + 't_app_reports',
    landingRoute,
    mode: '',
    tabs: [
      {
        key: 'tab1',
        label: 'Chats',
        icon: '<i class="fal fa-comment"></i>',
        active: true,
      },
      {
        key: 'tab2',
        label: 'Photos',
        icon: '<i class="fal fa-image-polaroid"></i>',
      },
    ],
  })

  defineComponent({
    name: 'TransReportsForm',
  })

  const formHeaderRefs = ref()
  const formDetailRefsTab1 = ref()
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

  const chat = reactive({
    message: '',
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
          created_id: {
            type: 'selectfield',
            version: 2,
            value: null,
            default: '',
            items: [],
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'User',
              information: '',
              inline: true,
              placeholder: 'Select one',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
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
          status: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Status',
              information: 'Status reported',
              inline: true,
              placeholder: 'Status reported',
              readonly: false,
              disabled: true,
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: [],
          },
          title: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Title',
              information: 'Title report',
              inline: true,
              placeholder: 'Title report',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
              hidden: false,
            },
            listener: () => {
              //
            },
            rules: ['required'],
          },
          ticket_number: {
            type: 'textfield',
            value: '',
            default: '',
            col: 'col-span-12 lg:col-span-6',
            options: {
              label: 'Ticket Number',
              information: 'Ticket Number report',
              inline: true,
              placeholder: 'Ticket Number report',
              readonly: false,
              disabled: true,
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
            col: 'col-span-12 lg:col-span-12',
            options: {
              label: 'Description',
              information: 'Description of report',
              inline: true,
              placeholder: 'Description',
              readonly: false,
              disabled: pageConfigs.mode === 'preview',
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
          key: 'form',
          model: pageConfigs.model,
          column: 'ihm_t_app_reports_d_photos',
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
          lineNumbers: true,
        },
        properties: [
          {
            field: 'photo_url',
            type: 'editor',
            width: '180px',
            label: 'Upload Photo',
            tdClass: 'custom-title',
            editor: {
              type: 'filefield',
              default: null,
              options: {
                clearable: false,
                viewer: 'image',
                placeholder: 'Upload an photo',
                readonly: false,
                disabled: pageConfigs.mode !== 'preview',
              },
              rules: ['required', 'mimes:jpg,png,jpeg,webp,gif'],
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

  const chats = ref([]) as any

  const isClosed = ref(false)
  const methods = {
    onLoadData: () => {
      Axios({
        url: `/api/v1/${pageConfigs.model}/${route.query.id}`,
      })
        .then((res: any) => {
          isClosed.value = res.data.data.status === 'CLOSED'
          Object.entries(res.data.data).forEach(([key, val]) => {
            Transform(schema, val, key)
          })
          configStore.backdrop.enabled = false

          $('#brand').html(
            res.data.data['ihm_m_users_d_device_histories.brand'],
          )

          $('#os').html(res.data.data['ihm_m_users_d_device_histories.os'])

          $('#os_version').html(
            res.data.data['ihm_m_users_d_device_histories.version'],
          )

          $('#app_version').html(
            res.data.data['ihm_m_users_d_device_histories.app_version'],
          )

          res.data.data.ihm_t_app_reports_d_chats.forEach((chat, index) => {
            let match = 0
            chats.value.forEach((chat2, index2) => {
              if (
                chat.created_at === chat2.created_at &&
                Cryptor.decrypt(chat.created_id) ===
                  Cryptor.decrypt(chat2.created_id)
              ) {
                let beforeNotMatch = 0
                if (
                  chats.value[index2 + 1] &&
                  Cryptor.decrypt(chats.value[index2 + 1].created_id) ===
                    Cryptor.decrypt(chat.created_id)
                ) {
                  beforeNotMatch++
                }

                if (!beforeNotMatch) {
                  match++
                  chats.value[index2].message += '<br/>' + chat.message
                }
              }
            })

            if (!match) {
              chats.value.push(chat)
            }
          })

          setTimeout(() => {
            const element = document.getElementById('box-chats')
            element.scrollTop = element.scrollHeight
          }, 100)
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
    sendMessage: () => {
      let data = {
        ihm_t_app_reports_id: route.query.id,
        staff_id: authStore.user.id,
        message: chat.message,
      }

      Axios({
        url: `/api/v1/ihm_t_app_reports_d_chats`,
        data,
        method: 'POST',
      })
        .then(() => {
          // ignore
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
    },
  }

  const webscoketStore = useWebsocketStore()
  onMounted(() => {
    if (route.query.id) {
      configStore.backdrop.enabled = true
      methods.onLoadData()
    }

    webscoketStore.echo
      .private('private_channel')
      .listen('.ihm_t_app_reports_d_chats', (e) => {
        if (
          e.action === 'created' &&
          e.data.ihm_t_app_reports_id == Cryptor.decrypt(route.query.id)
        ) {
          let match = 0
          chats.value.forEach((chat, index) => {
            if (
              chat.created_at === e.data.created_at &&
              Cryptor.decrypt(chat.created_id) ===
                Cryptor.decrypt(e.data.created_id)
            ) {
              let beforeNotMatch = 0
              if (
                chats.value[index + 1] &&
                Cryptor.decrypt(chats.value[index + 1].created_id) !==
                  Cryptor.decrypt(chat.created_id)
              ) {
                beforeNotMatch++
              }

              if (!beforeNotMatch) {
                chats.value[index].message += '<br />' + e.data.message
                match++
              }
            }
          })

          if (!match) {
            chats.value.push(e.data)
          }

          chat.message = ''

          setTimeout(() => {
            const element = document.getElementById('box-chats')
            element.scrollTop = element.scrollHeight
          }, 100)
        }
      })
  })

  onUnmounted(() => {
    webscoketStore.echo.leave('private_channel')
  })
</script>
