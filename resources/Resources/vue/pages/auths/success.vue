<template>
  <div
    class="min-h-screen flex flex-col items-center justify-center sm:py-12 bg-base-100"
  >
    <ul class="steps mb-5">
      <li class="step step-primary">Verification Account</li>
      <li class="step step-primary">OTP Verification</li>
      <li class="step step-primary">Confirmation</li>
      <li class="step step-primary">Success</li>
    </ul>
    <div
      class="px-10 xs:p-0 mx-auto md:w-full md:max-w-5xl lg:max-w-7xl flex justify-center"
    >
      <div class="hidden lg:block flex-grow">
        <img src="/image/storyset/success.png" alt="" style="width: 400px" />
      </div>
      <div class="flex-grow" style="min-width: 650px; max-width: 650px">
        <div class="flex justify-center">
          <AppLogo :full="true" mode="sign-in" />
        </div>
        <div
          class="bg-base-100 shadow-lg w-full rounded-lg divide-y divide-gray-200 mt-5"
        >
          <div class="p-5">
            <div class="col-span-12 mt-0">
              <button
                @click="methods.back()"
                class="mt-6 btn-block btn-primary transition duration-300 flex flex-row justify-center items-center text-white focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-3 text-center mr-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
              >
                <div class="mr-2 ft-nunito-700 normal-case">Go Back</div>
                <div><i class="fas fa-arrow-left"></i></div>
              </button>
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
  import { Auth, Notyf, Swal, Axios } from '~/services'
  import router from '~/router'
  import { useI18n } from 'vue-i18n'

  const configStore = useConfigStore()

  const permissions = inject('permissions') as Ref<UnwrapRef<Array<any>>>

  const methods = {
    back: () => {
      router.push('/')
    },
  }
</script>
