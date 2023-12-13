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
              <span class="text-primary">Lupa</span> Password
            </div>
            <form class="w-[80%]" @submit.prevent="onSubmit">
              <div class="form-control w-full">
                <label class="label" for="identity">
                  <span class="label-text">Email / Phone</span>
                </label>
                <input
                  id="identity"
                  v-model="form.identity"
                  autocomplete="off"
                  type="text"
                  placeholder=""
                  class="input input-bordered w-full"
                />
              </div>

              <div class="flex justify-between mt-4">
                <router-link to="/" type="button" class="btn btn-error normal-case">
                  <i class="fa-duotone fa-arrow-left"></i> Kembali
                </router-link>
                <button
                  type="submit"
                  :disabled="form.progress"
                  class="btn btn-primary text-white normal-case"
                >
                  <div class="mr-2 ft-nunito-700 normal-case">Submit</div>
                  <div v-if="form.progress">
                    <i class="fad fa-spin fa-spinner-third"></i>
                  </div>
                  <div v-else><i class="fad fa-sign-in-alt"></i></div>
                </button>
              </div>
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
    progress: false,
  })
  const permissions = inject('permissions') as Ref<UnwrapRef<Array<any>>>
  const onSubmit = () => {
    if (!form.progress) {
      form.progress = false
      setTimeout(() => {
        Auth.forgotPassword({
          identity: form.identity,
        })
          .then((res: any) => {
            setTimeout(() => {
              Notyf({
                type: 'success',
                message: `Please check your email or phone to reset your password`,
                duration: 3000,
                ripple: true,
                dismissible: true,
                position: {
                  x: 'right',
                  y: 'top',
                },
              })
            }, 1000)
            router.push('/forgot-password-verification')
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
                title: `Error ${err.response.status}!`,
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
