import flatpickr from 'flatpickr'
import { Options } from 'flatpickr/dist/types/options'
import { Instance } from 'flatpickr/dist/types/instance'

export default (selector: string, config?: Options): Instance | Instance[] => {
  return flatpickr(selector, config)
}
