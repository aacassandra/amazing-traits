import 'sweetalert2/src/sweetalert2.scss'
import { OptionsBasic, OptionsConfirm } from '~/types/components/atoms/swal'
import Swal from 'sweetalert2'

export default {
  basic: (options: OptionsBasic) => {
    const theme = localStorage.getItem('_theme')
    return new Promise((resolve) => {
      const SwalMixin = Swal.mixin({
        customClass: {
          confirmButton: `btn btn-${options.button.size} btn-${options.button.confirm}`,
          cancelButton: `btn btn-${options.button.size} btn-error`,
        },
        buttonsStyling: false,
        color: theme === 'dark' ? '#fff' : '#3d4451',
        background: theme === 'dark' ? '#3d4451' : '#fff',
      })
      SwalMixin.fire({
        title: options.title,
        html: options.html,
        icon: options.icon,
      }).then((res) => {
        resolve(res)
      })
    })
  },
  confirm: (options: OptionsConfirm) => {
    const theme = localStorage.getItem('_theme')
    return new Promise((resolve) => {
      const SwalMixin = Swal.mixin({
        customClass: {
          confirmButton: `btn btn-${options.button.size} btn-${options.button.confirm} text-white mr-2`,
          cancelButton: `btn btn-${options.button.size} btn-error text-white`,
        },
        buttonsStyling: false,
        color: theme === 'dark' ? '#fff' : '#3d4451',
        background: theme === 'dark' ? '#3d4451' : '#fff',
      })

      SwalMixin.fire({
        title: options.title,
        html: options.html,
        icon: options.icon,
        showCancelButton: options.showCancelButton,
        confirmButtonText: options.confirmButtonText,
        cancelButtonText: options.cancelButtonText,
      }).then((res) => {
        resolve(res)
      })
    })
  },
}
