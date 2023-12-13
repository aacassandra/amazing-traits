import { Field } from '~/types/components/atoms/forms/header/select'
import Cryptor from '~/services/Cryptor'

export default (curentWhere = '', field: Field, value: any) => {
  const valueEncrypted = Cryptor.decrypt(value)
  if (curentWhere && Object.keys(curentWhere).length) {
    if (
      (field.type && field.type.value === 'encrypted') ||
      (valueEncrypted && typeof value === 'string')
    ) {
      return (curentWhere += ` and this.${field.value}=':${value}:'`)
    } else if (field.type && field.type.value === 'string') {
      return (curentWhere += ` and this.${field.value}='${value}'`)
    } else if (field.type && field.type.value === 'number') {
      return (curentWhere += ` and this.${field.value}=${value}`)
    } else if (field.type && field.type.value === 'boolean') {
      return (curentWhere += ` and this.${field.value}=${value}`)
    } else {
      return (curentWhere += ` and this.${field.value}='${value}'`)
    }
  } else {
    if (
      (field.type && field.type.value === 'encrypted') ||
      (valueEncrypted && typeof value === 'string')
    ) {
      return (curentWhere = `this.${field.value}=':${value}:'`)
    } else if (field.type && field.type.value === 'string') {
      return (curentWhere = `this.${field.value}='${value}'`)
    } else if (field.type && field.type.value === 'number') {
      return (curentWhere = `this.${field.value}=${value}`)
    } else if (field.type && field.type.value === 'boolean') {
      return (curentWhere = `this.${field.value}=${value}`)
    } else {
      return (curentWhere = `this.${field.value}='${value}'`)
    }
  }
}
