<template>
  <div class="w-full">
    <div class="grid grid-cols-12">
      <div class="col-span-12">
        <div class="chat chat-start">
          <div class="chat-bubble">
            It's over Anakin, <br />I have the high ground.
          </div>
        </div>
        <div class="chat chat-end">
          <div class="chat-bubble">You underestimate my power!</div>
        </div>
      </div>
    </div>
    <div class="absolute w-full" style="bottom: 50px; right: 50px">
      <div class="flex justify-end">
        <input
          v-model="message"
          type="text"
          class="input input-bordered input-sm mr-3"
        />
        <button type="button" class="btn btn-sm btn-primary" @click="send">
          Send
        </button>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
  import { defineComponent, onMounted, onUnmounted, ref } from 'vue'
  import { useAuthStore } from '~/store/auth'
  import { Axios } from '~/services'
  import Echo from 'laravel-echo'
  import Pusher from 'pusher-js'

  defineComponent({
    name: 'IndexPage',
  })

  const authStore = useAuthStore()

  const message = ref('')
  const pusher = ref()
  const echo = ref()
  const send = () => {
    Axios({
      method: 'POST',
      url: '/api/v1/ihm_t_messages',
      data: {
        to_id: 2,
        from_id: 1,
        message: message.value,
      },
    }).then((res) => {
      message.value = ''
    })
  }
  onMounted(() => {
    pusher.value = Pusher
    echo.value = new Echo({
      broadcaster: 'pusher',
      key: import.meta.env.VITE_PUSHER_APP_KEY,
      wsHost: import.meta.env.VITE_PUSHER_HOST,
      wsPort: import.meta.env.VITE_PUSHER_PORT,
      forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
      enabledTransports: ['ws', 'wss'],
      disableStats: true,
      encrypted: true,
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
      auth: {
        headers: {
          Authorization: localStorage.getItem('_token'),
        },
      },
    })

    echo.value.channel('public_channel').listen('.ihm_t_messages', (e) => {
      console.log(e)
    })
  })

  onUnmounted(() => {
    echo.value.leaveChannel(`public_channel`)
  })
</script>
