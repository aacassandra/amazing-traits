<template>
  <div
    class="min-h-screen flex items-center justify-center sm:py-12 bg-base-100"
  >
    <div class="px-10 xs:p-0 mx-auto md:w-full md:max-w-5xl flex">
      <div class="hidden lg:block flex-grow">
        <img
          src="/image/storyset/drone-surveillance-amico.png"
          alt=""
          style="width: 400px"
        />
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
              <label class="label" for="username">
                <span class="label-text">Username</span>
              </label>
              <input
                id="username"
                v-model="form.identity"
                type="text"
                placeholder="jhon_doe"
                class="input input-bordered"
              />
              <label class="label hidden">
                <a href="#" class="label-text-alt">Forgot username?</a>
              </label>
            </div>

            <div class="form-control mt-2">
              <label class="label" for="password">
                <span class="label-text">Password</span>
              </label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                placeholder="****"
                class="input input-bordered"
              />
            </div>

            <button
              type="submit"
              :disabled="form.progress"
              class="mt-6 btn-block btn-primary transition duration-300 flex flex-row justify-center items-center text-white focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-3 text-center mr-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
            >
              <div class="mr-2 ft-nunito-700 normal-case">Login</div>
              <div v-if="form.progress">
                <i class="fad fa-spin fa-spinner-third"></i>
              </div>
              <div v-else><i class="fad fa-sign-in-alt"></i></div>
            </button>
            <p
              class="text-sm font-light text-gray-500 dark:text-gray-400 text-center mt-3 hidden"
            >
              Belum punya akun?
              <router-link
                to="/signup"
                class="font-medium text-blue-500 hover:underline text-center"
              >
                Daftar Disini
              </router-link>
            </p>
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
              <router-link
                to="/"
                type="button"
                class="transition duration-200 bg-blue-400 hover:bg-blue-500 focus:bg-blue-600 focus:shadow-sm focus:ring-4 focus:ring-orange-500 focus:ring-opacity-50 text-white w-full py-2.5 rounded-lg text-sm shadow-sm hover:shadow-md font-semibold text-center inline-block"
              >
                <i class="fab fa-twitter"></i> Twitter
              </router-link>
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
        Auth.signIn({
          identity: form.identity,
          password: form.password,
        })
          .then((res: any) => {
            permissions.value = res.data.data.permissions
            setTimeout(() => {
              Notyf({
                type: 'success',
                message: `Welcome back, ${res.data.data.name}`,
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
                router.push('/dashboard')
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
