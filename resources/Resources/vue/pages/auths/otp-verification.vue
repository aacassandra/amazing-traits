<template>
  <div
    class="min-h-screen flex flex-col items-center justify-center sm:py-12 bg-base-100"
  >
    <ul class="steps mb-5">
      <li class="step step-primary">Verification Account</li>
      <li class="step step-primary">OTP Verification</li>
      <li class="step">Confirmation</li>
      <li class="step">Success</li>
    </ul>
    <div
      class="px-10 xs:p-0 mx-auto md:w-full md:max-w-5xl lg:max-w-7xl flex justify-center"
    >
      <div class="hidden lg:block flex-grow">
        <img src="/image/storyset/otp_verif.png" alt="" style="width: 400px" />
      </div>
      <div class="flex-grow" style="min-width: 650px; max-width: 650px">
        <div class="flex justify-center">
          <AppLogo :full="true" mode="sign-in" />
        </div>
        <div
          class="bg-base-100 shadow-lg w-full rounded-lg divide-y divide-gray-200 mt-5"
        >
          <form class="p-5 grid grid-cols-12" @submit.prevent="onSubmit">
            <div class="form-control col-span-12">
              <label class="label" for="otp_code">
                <span class="label-text">
                  Please insert your otp code from email
                </span>
              </label>
              <input
                id="otp_code"
                v-model="form.otp_code"
                type="text"
                placeholder="OTP Code"
                class="input input-bordered"
              />
            </div>

            <div class="col-span-12 mt-0">
              <button
                type="submit"
                :disabled="form.progress"
                class="mt-6 btn-block btn-primary transition duration-300 flex flex-row justify-center items-center text-white focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-3 text-center mr-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
              >
                <div class="mr-2 ft-nunito-700 normal-case">Verify</div>
                <div v-if="form.progress">
                  <i class="fad fa-spin fa-spinner-third"></i>
                </div>
                <div v-else><i class="fas fa-check-circle"></i></div>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { inject, reactive, Ref, UnwrapRef } from 'vue'
  import AppLogo from '~/components/atoms/AppLogo.vue'
  import { useConfigStore } from '~/store/config'
  import { Auth, Notyf, Swal } from '~/services'
  import router from '~/router'
  import { useI18n } from 'vue-i18n'

  const configStore = useConfigStore()
  const form = reactive({
    otp_code: '',
    progress: false,
  })

  const permissions = inject('permissions') as Ref<UnwrapRef<Array<any>>>
  const onSubmit = () => {
    if (!form.progress) {
      form.progress = true
      Auth.otpDeleteVerification({
        otp_code: form.otp_code,
      })
        .then((res: any) => {
          if (res.data.success) {
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
            router.push({ name: 'confirmation-delete' })
          }
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
          } else if (err.response.data.message.includes('Validation Error.')) {
            err.response.data.data.forEach((errMessage) => {
              Notyf({
                type: 'error',
                message: errMessage,
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
    }
  }
</script>
