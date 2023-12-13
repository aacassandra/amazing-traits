/* eslint-disable @typescript-eslint/ban-ts-comment */
import { App } from 'vue'
import { ModuleNamespace } from 'vite/types/hot'

/**
 * Register layouts in the app instance
 *
 * @param {App<Element>} app
 */
export function registerLayouts(app: App<Element>) {
  // @ts-ignore
  const layouts: any = import.meta.globEager<string, ModuleNamespace>('./*.vue')

  Object.entries(layouts).forEach(([, layout]) => {
    // @ts-ignore
    app.component(layout?.default?.__name, layout?.default)
  })
}
