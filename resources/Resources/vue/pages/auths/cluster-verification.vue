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
              <span class="text-primary">Masukkan</span> Password
            </div>
            <div
              class="ft-poppins-500 text-gray-700 dark:text-gray-400 text-center mt-3"
            >
              Masukkan password pada form di bawah ini<br />
              untuk melengkapi login ke backoffice
            </div>
            <form class="w-[80%]" @submit.prevent="onSubmit">
              <div class="form-control w-full">
                <label class="label" for="username">
                  <span class="label-text">Email</span>
                </label>
                <input
                  id="username"
                  autocomplete="off"
                  type="text"
                  :value="form.email"
                  placeholder="test@example.com"
                  class="input input-bordered w-full"
                  disabled
                />
              </div>

              <div class="form-control mt-2">
                <label class="label" for="password">
                  <span class="label-text">Password</span>
                </label>
                <input
                  id="password"
                  v-model="form.password"
                  :disabled="form.progress"
                  autocomplete="off"
                  type="password"
                  placeholder="****"
                  class="input input-bordered"
                />
              </div>

              <div class="form-control mt-2">
                <label class="label" for="c_password">
                  <span class="label-text">Konfirmasi Password</span>
                </label>
                <input
                  id="c_password"
                  v-model="form.c_password"
                  :disabled="form.progress"
                  autocomplete="off"
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
                Selanjutnya
                <div v-if="form.progress && !isError">
                  &nbsp;<i class="fad fa-spin fa-spinner-third"></i>
                </div>
              </button>
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
  import { onMounted, reactive, ref } from 'vue'
  import { useRoute } from 'vue-router'
  import { Auth, Notyf } from '~/services'
  import router from '~/router'
  import { useI18n } from 'vue-i18n'
  import axios from 'axios'

  const route = useRoute()
  const { t } = useI18n()
  const form = reactive({
    otp_code: '',
    email: '',
    password: '',
    c_password: '',
    progress: true,
  })

  const isError = ref(false)
  onMounted(() => {
    form.progress = true
    form.otp_code = route.query.otp_code as string
    axios('/api/v1/ihm_m_users/get_by_otp_code', {
      params: {
        otp_code: form.otp_code,
      },
    })
      .then((res: any) => {
        form.email = res.data.data.email
        form.progress = false
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

        isError.value = true
        // router.push('/').then(() => {
        //   window.location.reload()
        // })
      })
  })

  const onSubmit = async () => {
    if (!form.progress) {
      form.progress = true
      await Auth.clusterVerification({
        otp_code: form.otp_code,
        password: form.password,
        c_password: form.c_password,
      })
        .then((res: any) => {
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
          form.progress = false
        })
        .catch((err) => {
          err.response.data.errors.forEach((msg) => {
            Notyf({
              type: 'error',
              message: msg,
              duration: 3000,
              ripple: true,
              dismissible: true,
              position: {
                x: 'right',
                y: 'top',
              },
            })
          })
          form.progress = false
        })
    }
  }
</script>
