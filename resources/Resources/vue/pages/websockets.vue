<template>
  <div class="w-full">
    <div class="grid grid-cols-12">
      <div class="col-span-6 my-auto">
        <Breadcrumb :data="pageConfigs.breadcrumb" />
      </div>
    </div>

    <div>
      <div
        v-if="data.listening.isListen"
        class="alert alert-info mt-5 mb-5 text-white"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          class="stroke-current shrink-0 w-6 h-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          ></path>
        </svg>
        <span>Open your console, to see event.</span>
      </div>
      <Card>
        <template #card-header>
          <div class="flex">
            <div class="flex-grow">
              <div
                class="text-lg text-gray-900 dark:text-white ft-dmsans-700 px-1"
              >
                Listening
              </div>
            </div>
          </div>
        </template>

        <template #card-body>
          <form class="flex" @submit.prevent="methods.onListen(false)">
            <div class="flex-grow pr-5">
              <TextField
                v-model="data.listening.channel"
                :disabled="data.listening.isListen"
                name="channel-name"
                placeholder="Channel Name"
              />
            </div>
            <div class="flex-grow pr-5">
              <TextField
                v-model="data.listening.tableName"
                :disabled="data.listening.isListen"
                name="class-name"
                :placeholder="`.${table_prefix}table_name`"
              />
            </div>
            <div class="flex-grow-0 mr-5">
              <button
                type="submit"
                :disabled="data.listening.isListen"
                class="btn btn-sm btn-block btn-primary text-white normal-case mr-2"
                style="height: 42px"
              >
                Listen
              </button>
            </div>
            <div class="flex-grow-0">
              <button
                type="button"
                :disabled="!data.listening.isListen"
                class="btn btn-sm btn-block btn-error text-white normal-case"
                style="height: 42px"
                @click="methods.onListen(true)"
              >
                Destroy
              </button>
            </div>
          </form>
        </template>
      </Card>
    </div>
    <div class="pt-3">
      <Card>
        <template #card-header>
          <div class="flex">
            <div class="flex-grow">
              <div
                class="text-lg text-gray-900 dark:text-white ft-dmsans-700 px-1"
              >
                Sending
              </div>
            </div>
          </div>
        </template>

        <template #card-body>
          <div class="flex">
            <div class="flex-grow-0 pr-3" style="min-width: 150px">
              <SelectField
                v-model="data.sending.method"
                :out-val="data.sending.methodOutval"
                :disabled="!data.listening.isListen"
                :items="['POST', 'PUT', 'DELETE']"
                :clearable="true"
                name="method"
              />
            </div>
            <div class="flex-grow">
              <TextField
                v-model="data.sending.url"
                :out-val="data.sending.urlOutval"
                :disabled="!data.listening.isListen"
                name="Endpoint"
                placeholder="Endpoint"
                :from-generator="false"
              />
            </div>
          </div>
          <div class="mt-4">
            <codemirror
              v-model="data.sending.data"
              :disabled="!data.listening.isListen"
              placeholder="Write your json body here"
              :style="{ height: '300px' }"
              :autofocus="true"
              :indent-with-tab="true"
              :tab-size="2"
              :extensions="extensions"
            />
          </div>
          <div class="mt-4 flex justify-end">
            <button
              type="button"
              :disabled="!data.listening.isListen"
              class="btn btn-sm btn-primary text-white normal-case"
              style="height: 42px"
              @click="methods.onSend"
            >
              Submit
            </button>
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>
<script setup lang="ts">
  import Breadcrumb from '~/components/atoms/Breadcrumb.vue'
  import { onUnmounted, provide, reactive, ref } from 'vue'
  import { PageConfigs } from '~/types/pages/form/v1'
  import { Card } from '~/components/atoms'
  import FormHeader from '~/components/organism/FormHeader/index.vue'
  import FormDetail from '~/components/organism/FormDetail/index.vue'
  import TextField from '~/components/atoms/form/header/Textfield.vue'
  import SelectField from '~/components/atoms/form/header/SelectfieldV2.vue'
  import { useWebsocketStore } from '~/store/websocket'
  import { Axios, Notyf } from '~/services'
  import { Codemirror } from 'vue-codemirror'
  import { json } from '@codemirror/lang-json'
  import { oneDark } from '@codemirror/theme-one-dark'
  import Pusher from 'pusher-js'

  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const parentReady = ref(false)
  provide('parentReady', parentReady)
  const extensions = [json(), oneDark]
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
        text: 'Websockets',
      },
    ],
    title: 'Websockets',
  })

  const data = reactive({
    listening: {
      isListen: false,
      channel: 'private-channel',
      tableName: '',
    },
    sending: {
      method: 'POST',
      methodOutval: '',
      url: '/api/v1/...',
      urlOutval: '',
      data: '',
    },
  })

  const websocketStore = useWebsocketStore()
  const methods = {
    onListen: (is_destroy = false) => {
      if (!data.listening.isListen) {
        let is_error = 0
        if (!data.listening.channel) {
          Notyf({
            type: 'error',
            message: 'Channel name is required',
            duration: 3000,
            ripple: true,
            dismissible: true,
            position: {
              x: 'right',
              y: 'top',
            },
          })
          is_error++
        }

        if (!data.listening.tableName) {
          Notyf({
            type: 'error',
            message: 'Class name is required',
            duration: 3000,
            ripple: true,
            dismissible: true,
            position: {
              x: 'right',
              y: 'top',
            },
          })
          is_error++
        }
        if (is_error) {
          return
        }
        data.listening.isListen = true

        console.log(
          `${data.listening.channel} with ${data.listening.tableName} is listening!`,
        )
        data.sending.urlOutval = `/api/v1/${data.listening.tableName.replace(
          '.',
          '',
        )}`
        websocketStore.echo
          .private(data.listening.channel)
          .listen(data.listening.tableName, (e) => {
            console.log(e)
          })
      }

      if (is_destroy) {
        data.listening.isListen = false
        websocketStore.echo.leave(data.listening.channel)
        console.log(`${data.listening.channel} is destroyed`)
      }

      let id = ''
      let table = data.listening.tableName.replace('.', '')
      if (data.listening.channel.indexOf('private-channel.') > -1) {
        id = data.listening.channel.split('.')[1]
        table += `/${id}`
      }

      let config = {
        method: 'GET',
        url: `/api/v1/${table}`,
        params: {},
      }

      data.sending.urlOutval = config.url

      if (!id) {
        config.params['paginate'] = 1
      }

      Axios(config).then((res: any) => {
        let resData = {}
        if (id) {
          if (Object.keys(res.data.data).length) {
            resData = res.data.data

            data.sending.methodOutval = 'PUT'
          }
        } else {
          if (res.data.data.length) {
            resData = res.data.data[0]

            Object.entries(resData).forEach(([key, value]) => {
              if (typeof value === 'string') {
                resData[key] = ''
              } else if (typeof value === 'number') {
                resData[key] = 0
              } else if (typeof value === 'boolean') {
                resData[key] = false
              }
            })
          }
        }

        if (Object.keys(resData).length) {
          data.sending.data = JSON.stringify(resData, null, '  ')
        }
      })
    },
    onSend: () => {
      let is_error = 0
      if (!data.sending.method) {
        Notyf({
          type: 'error',
          message: 'Method is required',
          duration: 3000,
          ripple: true,
          dismissible: true,
          position: {
            x: 'right',
            y: 'top',
          },
        })
        is_error++
      }

      if (!data.sending.url) {
        Notyf({
          type: 'error',
          message: 'Endpoint is required',
          duration: 3000,
          ripple: true,
          dismissible: true,
          position: {
            x: 'right',
            y: 'top',
          },
        })
        is_error++
      }

      if (is_error) {
        return
      }

      Axios({
        method: data.sending.method,
        url: data.sending.url,
        data: data.sending.data,
      })
        .then((res) => {
          //
        })
        .catch((err) => {
          console.log(err.response)
        })
    },
  }

  onUnmounted(() => {
    if (data.listening.isListen) {
      websocketStore.echo.leave(data.listening.channel)
    }
  })
</script>
