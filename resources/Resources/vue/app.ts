/* eslint-disable @typescript-eslint/ban-ts-comment */
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from '~/App.vue'
import router from '~/router/index'
import { registerLayouts } from './layouts/register'
// @ts-ignore
import vClickOutside from 'click-outside-vue3'
import HighchartsVue from 'highcharts-vue'
import Highcharts from 'highcharts'
import exportingInit from 'highcharts/modules/exporting'
import i18n from './locales'

exportingInit(Highcharts)
const pinia = createPinia()
const app = createApp(App)

app.use(router)
app.use(pinia)
app.use(vClickOutside)
app.use(i18n)
// @ts-ignore
app.use(HighchartsVue)
registerLayouts(app)

app.mount('#app')
