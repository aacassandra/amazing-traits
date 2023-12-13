import { Field } from '~/types/components/atoms/forms/header/select'

export default (value: any, field: Field) => {
  if (field.type) {
    if (field.type.value === 'string') {
      if (typeof value === 'number') {
        return value.toString()
      } else if (typeof value === 'boolean') {
        return value.toString()
      } else {
        return value
      }
    } else if (field.type.value === 'number') {
      if (!isNaN(value)) {
        return value * 1
      } else {
        return value
      }
    } else if (field.type.value === 'boolean') {
      if (typeof value === 'string') {
        if (value === 'true') {
          return true
        } else if (value === 'false') {
          return false
        } else {
          return value
        }
      }
    }
  } else {
    return value
  }
}
