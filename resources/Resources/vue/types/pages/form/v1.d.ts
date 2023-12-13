import { Breadcrumb } from '~/types/components/atoms/breadcrumb'
import { TabsType } from '~/types/components/molecules/tabs'

export interface PageConfigs {
  breadcrumb: Array<Breadcrumb>
  title: string
  model?: string
  landingRoute?: string
  formRoute?: string
  mode?: 'create' | 'edit' | 'preview' | ''
  tabs?: TabsType
}
