export interface BreadcrumbTextV1 {
  type: 'text'
  icon?: string
  text: string
  lang?: string
}

export interface BreadcrumbTextLinkV1 {
  type: 'text-link'
  icon?: string
  text: string
  lang?: string
  route: string
}

export type BreadcrumbV1 = BreadcrumbTextV1 | BreadcrumbTextLinkV1
