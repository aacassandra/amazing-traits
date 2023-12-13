<template>
  <div>
    <highlightjs :language="language" :code="code" />
  </div>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import 'highlight.js/lib/common'
  import hljsVuePlugin from '@highlightjs/vue-plugin'

  export default defineComponent({
    name: 'Highlight',
    components: {
      highlightjs: hljsVuePlugin.component,
    },
    props: {
      language: {
        type: String,
        default: 'js',
      },
      code: {
        type: String,
        default: '',
      },
    },
  })
</script>
