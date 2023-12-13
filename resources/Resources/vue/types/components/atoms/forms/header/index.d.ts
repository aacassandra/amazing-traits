export interface Form {
  version: number
  schema: {
    [key: string]: any
    errors: any
  }
}

export interface Element {
  [key: string]: any
}

export interface OldParams {
  [key: string]: any
}

export interface Disabled {
  general: boolean
  detail?: boolean
  create?: boolean
  edit?: boolean
}

export type ApiInterface = {
  url?: string // don't use this, when you try using this, maybe you will retriving error like column ambigous
  model?: string // It is recommended to use this instead of "url".
  parameters?: {
    [key: string]: any
  }
  transform?(row: any): void
  cache?: boolean
  overrideParams?(element: Element, oldParams: OldParams): OldParams
  root?: string
}

export type Mode = 'general' | 'detail' | 'edit' | 'create'
