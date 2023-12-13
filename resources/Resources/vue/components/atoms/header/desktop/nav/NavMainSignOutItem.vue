<template>
  <div class="item item-sign-out" @click="onSignOut">
    <i :class="item.icon"></i>
    <span>{{ item.text }}</span>
  </div>
</template>

<script setup lang="ts">
  import { defineComponent } from 'vue'
  import { NavMainSignOut } from '~/types/config/navbars'
  import {Auth, Swal} from '~/services'
  import router from '~/router'

  defineProps({
    item: {
      type: Object as () => NavMainSignOut,
      required: true,
    },
  })

  defineComponent({
    name: 'NavMainSignOutItem',
  })

  const onSignOut = () => {
    const signout = () => {
      Auth.signOut().then(() => {
        router.push('/')
      })
    }

    Swal
      .confirm({
        title: 'Signout',
        html: 'If you want to signout click yes',
        icon: 'warning',
        button: {
          confirm: 'primary',
          size: 'md',
        },
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
      })
      .then((result: any) => {
        if (result.isConfirmed) {
          signout()
        }
      })
  }
</script>
