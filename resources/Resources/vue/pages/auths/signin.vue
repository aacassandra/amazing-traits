<template>
  <div class="absolute w-full h-full bg-base-200">
    <div class="w-full h-full flex">
      <div class="flex-grow flex items-center justify-center h-full">
        <div class="card shadow-lg w-[95%] 2xl:w-[70%] bg-base-100 border">
          <div class="card-body w-full flex flex-col items-center py-[40px]">
            <img
              src="/img/ihome%20-%20admin%20-%20text%20dark.webp"
              alt=""
              class="w-[200px] 2xl:w-[269px]"
            />
            <div
              class="ft-poppins-600 text-[25px] xl:text-[32px] mt-[30px] 2xl:mt-[56px]"
            >
              <span class="text-primary">Masuk</span> ke Backoffice
            </div>
            <form class="w-[80%]" @submit.prevent="onSubmit">
              <div class="form-control w-full">
                <label class="label" for="username">
                  <span class="label-text">Username</span>
                </label>
                <input
                  id="username"
                  v-model="form.identity"
                  autocomplete="off"
                  type="text"
                  placeholder="jhon_doe"
                  class="input input-bordered w-full"
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
                  autocomplete="off"
                  type="password"
                  placeholder="****"
                  class="input input-bordered"
                />
                <label class="label">
                  <a href="" class="opacity-0"></a>
                  <router-link
                    to="/forgot-password"
                    class="label-text-alt ft-poppins-600 text-[14px] text-primary mt-3"
                  >
                    Lupa password?
                  </router-link>
                </label>
              </div>

              <button
                type="submit"
                :disabled="form.progress"
                class="mt-6 btn-block btn-primary transition duration-300 flex flex-row justify-center items-center text-white focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-3 text-center mr-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
              >
                <div class="mr-2 ft-nunito-700 normal-case">Masuk</div>
                <div v-if="form.progress">
                  <i class="fad fa-spin fa-spinner-third"></i>
                </div>
                <div v-else><i class="fad fa-sign-in-alt"></i></div>
              </button>
              <p
                class="text-sm font-light ft-poppins-400 text-gray-700 dark:text-gray-400 text-center mt-3"
              >
                Belum punya akun?
                <a
                  :href="frontHostUrl + '/register'"
                  class="font-medium text-primary ft-poppins-500 hover:underline text-center"
                >
                  Daftar Disini
                </a>
              </p>
            </form>
          </div>
        </div>
      </div>
      <div class="flex-grow-0 hidden 2xl:block">
        <img src="/img/Converted_documents.webp" alt="" class="h-[100vh]" />
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
  import { inject, reactive, Ref, UnwrapRef } from 'vue'
  // import AppLogo from '~/components/atoms/AppLogo.vue'
  import { useAuthStore } from '~/store/auth'
  import { useConfigStore } from '~/store/config'
  import { Auth, Axios, Notyf, Swal } from '~/services'
  import router from '~/router'
  import { useI18n } from 'vue-i18n'

  const { t } = useI18n()
  const frontHostUrl = import.meta.env.VITE_FRONT_HOST_URL
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
