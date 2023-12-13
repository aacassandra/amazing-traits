export interface SidebarsGroup {
  type: 'groupname'
  text: string
  lang?: string
  permissions?: Array<string>
}

export interface SidebarsTextLink {
  id: string
  type: 'text-link'
  text: string
  lang?: string
  icon: string
  route: string
  active: boolean
  permissions?: Array<string>
  hide?: boolean
  maintenance?: boolean
}

export interface SidebarsDropdownLvl4 {
  id?: string
  type: 'dropdown'
  text: string
  lang?: string
  icon: string
  active: boolean
  hide?: boolean
  maintenance?: boolean
  permissions?: Array<string>
  items: Array<SidebarsTextLink>
}

export interface SidebarsDropdownLvl3 {
  id?: string
  type: 'dropdown'
  text: string
  lang?: string
  icon: string
  active: boolean
  hide?: boolean
  maintenance?: boolean
  permissions?: Array<string>
  items: Array<SidebarsTextLink | SidebarsDropdownLvl4>
}

export interface SidebarsDropdownLvl2 {
  id?: string
  type: 'dropdown'
  text: string
  lang?: string
  icon: string
  active: boolean
  hide?: boolean
  maintenance?: boolean
  permissions?: Array<string>
  items: Array<SidebarsTextLink | SidebarsDropdownLvl3>
}

export interface SidebarsDropdown {
  id?: string
  type: 'dropdown'
  text: string
  lang?: string
  icon: string
  active: boolean
  hide?: boolean
  maintenance?: boolean
  permissions?: Array<string>
  items: Array<SidebarsTextLink | SidebarsDropdownLvl2>
}

export interface Sidebar {
  hidden: boolean
  vHidden: boolean
  data: Array<SidebarsGroup | SidebarsTextLink | SidebarsDropdown>
}
