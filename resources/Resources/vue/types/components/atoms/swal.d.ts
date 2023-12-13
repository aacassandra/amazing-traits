import { SweetAlertIcon } from 'sweetalert2';

interface Button {
  confirm:
    | 'success'
    | 'error'
    | 'warning'
    | 'info'
    | 'primary'
    | 'secondary'
    | 'accent'
    | 'default';
  size: 'xs' | 'sm' | 'md' | 'lg';
}

export interface OptionsBasic {
  title?: string;
  html?: string;
  icon?: SweetAlertIcon;
  button: Button;
}

export interface OptionsConfirm {
  title?: string;
  html?: string;
  icon?: SweetAlertIcon;
  button: Button;
  showCancelButton?: boolean;
  confirmButtonText?: string;
  cancelButtonText?: string;
}
