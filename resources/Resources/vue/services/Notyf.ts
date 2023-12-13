import { DeepPartial, INotyfNotificationOptions } from 'notyf/notyf.options'
import '~/assets/css/custom.notyf.css'
import { useInitStore } from '~/store/init'
import { computed } from 'vue'
import { Notyf } from 'notyf'

export default (options: DeepPartial<INotyfNotificationOptions>) => {
  const inits = useInitStore()

  if (!inits.notyf) {
    inits.notyf = computed(
      () =>
        new Notyf({
          types: [
            {
              type: 'primary',
              className: 'bg-primary',
              icon: false,
            },
            {
              type: 'secondary',
              className: 'bg-secondary',
              icon: false,
            },
            {
              type: 'accent',
              className: 'bg-accent',
              icon: false,
            },
            {
              type: 'neutral',
              className: 'bg-neutral',
              icon: false,
            },
            {
              type: 'info',
              className: 'bg-info',
              icon: false,
            },
            {
              type: 'success',
              className: 'bg-success',
              icon: {
                className: 'notyf__icon--success text-success',
                tagName: 'i',
              },
            },
            {
              type: 'error',
              className: 'notyf__toast--error bg-error',
              icon: {
                className: 'notyf__icon--error text-error',
                tagName: 'i',
              },
            },
            {
              type: 'warning',
              className: 'bg-warning',
              icon: false,
            },
          ],
          position: { x: 'right', y: 'top' },
        }),
    )
  }

  inits.notyf.open(options)
}
