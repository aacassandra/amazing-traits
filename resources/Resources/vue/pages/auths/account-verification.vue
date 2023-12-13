<template>
  <div
    class="min-h-screen flex flex-col items-center justify-center sm:py-12 bg-base-100"
  >
    <ul class="steps mb-5">
      <li class="step step-primary">Verification Account</li>
      <li class="step">OTP Verification</li>
      <li class="step">Confirmation</li>
      <li class="step">Success</li>
    </ul>
    <div class="px-10 xs:p-0 mx-auto md:w-full md:max-w-5xl flex">
      <div class="hidden lg:block flex-grow">
        <img src="/image/storyset/acc_verif.png" alt="" style="width: 400px" />
      </div>
      <div class="flex-grow" style="min-width: 350px">
        <div class="flex justify-center">
          <AppLogo :full="true" mode="sign-in" />
        </div>
        <div
          class="bg-base-100 shadow-lg border w-full rounded-lg divide-y divide-gray-200 mt-5"
        >
          <form class="p-5" @submit.prevent="onSubmit">
            <div class="form-control">
              <label class="label" for="identity">
                <span class="label-text">Email / Phone</span>
              </label>
              <input
                id="identity"
                v-model="form.identity"
                type="text"
                placeholder="jhon_doe"
                class="input input-bordered"
              />
            </div>

            <button
              type="submit"
              :disabled="form.progress"
              class="mt-6 btn-block btn-primary transition duration-300 flex flex-row justify-center items-center text-white focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-3 text-center mr-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
            >
              <div class="mr-2 ft-nunito-700 normal-case">Verification</div>
              <div v-if="form.progress">
                <i class="fad fa-spin fa-spinner-third"></i>
              </div>
              <div v-else><i class="fad fa-sign-in-alt"></i></div>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
  import { inject, reactive, Ref, UnwrapRef } from 'vue'
  import AppLogo from '~/components/atoms/AppLogo.vue'
  import { useAuthStore } from '~/store/auth'
  import { useConfigStore } from '~/store/config'
  import { Auth, Axios, Notyf, Swal } from '~/services'
  import router from '~/router'
  import { useI18n } from 'vue-i18n'

  const { t } = useI18n()
  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX
  const authStore = useAuthStore()
  const configStore = useConfigStore()
  const form = reactive({
    identity: '',
    password: '',
    progress: false,
  })
  const permissions = inject('permissions') as Ref<UnwrapRef<Array<any>>>
  const onSubmit = () => {
    if (!form.progress) {
      form.progress = true
      setTimeout(() => {
        Auth.accVerification({
          identity: form.identity,
        })
          .then((res: any) => {
            setTimeout(() => {
              Notyf({
                type: 'success',
                message: `Account Verification Success`,
                duration: 3000,
                ripple: true,
                dismissible: true,
                position: {
                  x: 'right',
                  y: 'top',
                },
              })
            }, 1000)
            authStore.save(res.data.data).then(() => {
              Axios({
                url: `/api/v1/${table_prefix}configs?parameter=styles`,
                method: 'GET',
              }).then((res2: any) => {
                const { data } = res2.data
                configStore.styleId = data[2].id
                configStore.save(JSON.parse(data[2].value))
                router.push({ name: 'otp-verification' })
              })
            })
            form.progress = false
          })
          .catch((err) => {
            if (
              err.response.data.message.includes(
                'Personal access client not found',
              )
            ) {
              Notyf({
                type: 'error',
                message: `Personal access client not found, please contact developer`,
                duration: 3000,
                ripple: true,
                dismissible: true,
                position: {
                  x: 'right',
                  y: 'top',
                },
              })
            } else if (err.response.data.message.includes('Unauthorised')) {
              Notyf({
                type: 'error',
                message: `Your username or password is invalid`,
                duration: 3000,
                ripple: true,
                dismissible: true,
                position: {
                  x: 'right',
                  y: 'top',
                },
              })
            } else if (
              err.response.data.message.includes('connection to server at')
            ) {
              Swal.basic({
                icon: 'error',
                title: `Error ${err.response.data.statusCode}!`,
                html: 'Connection to server is failed, please contact developer',
                button: {
                  confirm: 'default',
                  size: 'md',
                },
              })
            } else {
              Swal.basic({
                icon: 'error',
                title: `Error ${err.response.data.statusCode}!`,
                html: err.response.data.message,
                button: {
                  confirm: 'default',
                  size: 'md',
                },
              })
            }
            form.progress = false
          })
      }, 1000)
    }
  }
</script>
