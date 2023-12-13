import {
  SidebarsDropdown,
  SidebarsGroup,
  SidebarsTextLink,
} from '~/types/components/organism/sidebar'
import Axios from '~/services/Axios'
import { useTempsStore } from '~/store/temps'

const SyncronizeMenu = (
  sidebars: Array<SidebarsGroup | SidebarsTextLink | SidebarsDropdown>,
  worktask = false,
) => {
  const tempStore = useTempsStore()
  const menus = []
  sidebars.forEach((sb) => {
    if (sb.type === 'text-link') {
      menus.push({
        uid: sb.id,
        name: sb.text,
        module: null,
        submodule: null,
        path_url: sb.route,
        active_flag: !sb.hide,
      })
    } else if (sb.type === 'dropdown') {
      sb.items.forEach((sb2) => {
        if (sb2.type === 'text-link') {
          menus.push({
            uid: sb2.id,
            name: sb2.text,
            module: sb.text,
            submodule: null,
            path_url: sb2.route,
            active_flag: !sb2.hide,
          })
        } else if (sb2.type === 'dropdown') {
          sb2.items.forEach((sb3) => {
            if (sb3.type === 'text-link') {
              menus.push({
                uid: sb3.id,
                name: sb3.text,
                module: sb.text,
                submodule: sb2.text,
                path_url: sb3.route,
                active_flag: !sb3.hide,
              })
            }
          })
        }
      })
    }
  })

  const table_prefix = import.meta.env.VITE_APP_TABLE_PREFIX

  if (worktask) {
    if (!tempStore.onSycronizingMenu) {
      tempStore.setValue('onSycronizingMenu', true)
      setTimeout(() => {
        tempStore.setValue('onSycronizingMenu', false)
      }, 1000)
    } else {
      return
    }
  }

  Axios({
    method: 'POST',
    url: `/api/v1/${table_prefix}m_menu/sync_menu`,
    data: {
      menus,
      worktask,
    },
  })
}

export default SyncronizeMenu
