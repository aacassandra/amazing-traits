import Notyf from '~/services/Notyf'
import Swal from '~/services/Swal'

export default (err: any) => {
  if ([400, 401, 402, 404, 422].includes(err.response.status)) {
    if (err.response.data.data && err.response.data.data.length) {
      err.response.data.data.forEach((dt) => {
        Notyf({
          type: 'error',
          message: dt,
          duration: 3000,
          ripple: true,
          dismissible: true,
          position: {
            x: 'right',
            y: 'top',
          },
        })
      })
    } else if (err.response.data.errors && err.response.data.errors.length) {
      if (err.response.data.message.indexOf('SQLSTATE') > -1) {
        Swal.basic({
          icon: 'error',
          title: `Error ${err.response.status}!`,
          html: err.response.data.message,
          button: {
            confirm: 'default',
            size: 'md',
          },
        })
      } else {
        err.response.data.errors.forEach((dt) => {
          Notyf({
            type: 'error',
            message: dt,
            duration: 3000,
            ripple: true,
            dismissible: true,
            position: {
              x: 'right',
              y: 'top',
            },
          })
        })
      }
    } else {
      Notyf({
        type: 'error',
        message: err.response.statusText,
        duration: 3000,
        ripple: true,
        dismissible: true,
        position: {
          x: 'right',
          y: 'top',
        },
      })
    }
  }
}
