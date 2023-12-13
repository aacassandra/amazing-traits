<template>
  <div
    class="min-h-screen flex items-center justify-center sm:py-12 bg-base-100"
  >
    <div
      class="px-10 xs:p-0 mx-auto md:w-full md:max-w-5xl lg:max-w-7xl flex justify-center"
    >
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
              <p
                class="text-sm font-light text-gray-500 dark:text-gray-400 text-center mt-3"
              >
                Sudah punya akun?
                <router-link
                  to="/"
                  class="font-medium text-blue-500 hover:underline text-center"
                >
                  Login Disini
                </router-link>
              </p>
            </div>
          </form>
          <div class="p-5 hidden">
            <div class="grid grid-cols-3 gap-1">
              <button
                type="button"
                class="transition duration-200 bg-orange-500 hover:bg-orange-600 focus:bg-orange-700 focus:shadow-sm focus:ring-4 focus:ring-orange-500 focus:ring-opacity-50 text-white w-full py-2.5 rounded-lg text-sm shadow-sm hover:shadow-md font-semibold text-center inline-block"
              >
                <i class="fab fa-google"></i> Google
              </button>
              <button
                type="button"
                class="transition duration-200 bg-blue-500 hover:bg-blue-600 focus:bg-blue-700 focus:shadow-sm focus:ring-4 focus:ring-orange-500 focus:ring-opacity-50 text-white w-full py-2.5 rounded-lg text-sm shadow-sm hover:shadow-md font-semibold text-center inline-block"
              >
                <i class="fab fa-facebook"></i> Facebook
              </button>
              <button
                type="button"
                class="transition duration-200 bg-blue-400 hover:bg-blue-500 focus:bg-blue-600 focus:shadow-sm focus:ring-4 focus:ring-orange-500 focus:ring-opacity-50 text-white w-full py-2.5 rounded-lg text-sm shadow-sm hover:shadow-md font-semibold text-center inline-block"
              >
                <i class="fab fa-twitter"></i> Twitter
              </button>
            </div>
          </div>
          <div class="py-1 hidden">
            <div class="grid grid-cols-2 gap-1">
              <div class="text-center sm:text-left whitespace-nowrap">
                <button
                  class="transition duration-200 mx-5 px-5 py-4 cursor-pointer font-normal text-sm rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 focus:outline-none focus:bg-gray-200 focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50 ring-inset"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    class="w-4 h-4 inline-block align-text-top"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"
                    />
                  </svg>
                  <span class="inline-block ml-1">Forgot Password</span>
                </button>
              </div>
              <div class="text-center sm:text-right whitespace-nowrap">
                <button
                  class="transition duration-200 mx-5 px-5 py-4 cursor-pointer font-normal text-sm rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-200 focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50 ring-inset"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    class="w-4 h-4 inline-block align-text-bottom"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                  </svg>
                  <span class="inline-block ml-1">Help</span>
                </button>
              </div>
            </div>
          </div>
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
      Auth.signUpVerification({
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
            router.push('/')
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
