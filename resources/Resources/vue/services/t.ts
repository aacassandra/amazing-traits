// import { useI18n } from 'vue-i18n'
import i18n from '~/locales'

export default (value) => {
  const { t, te } = i18n.global

  if (!value) {
    return value
  }

  if (typeof value === 'string') {
    if (!te(value)) {
      return value
    }

    return t(value)
  } else if (typeof value === 'object' && value.lang && value.option) {
    if (!te(value.lang)) {
      return value.lang
    }

    return t(value.lang, value.option)
  }
}
