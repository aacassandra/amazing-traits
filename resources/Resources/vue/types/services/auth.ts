export interface UserSignInCredential {
  identity: string
  password: string
}
export interface UserAccVerifCredential {
  identity: string
}

export interface UserForgotPasswordCredential {
  identity: string
}

export interface UserChangePassword {
  password: string
  c_password: string
  otp_code: string
}

export interface UserSignUpCredential {
  first_name: string
  last_name: string
  username: string
  email: string
  phone: string
  password: string
  c_password: string
}

export interface UserSignUpVerification {
  otp_code: string
}
export interface UserForgotPasswordVerification {
  otp_code: string
}
export interface UserOtpDeleteVerification {
  otp_code: string
}

export interface ClusterVerification {
  otp_code: string
  password: string
  c_password: string
}

export interface NuxtAuth {
  signIn<T = any>(user: UserSignInCredential): Promise<T>
  signUp<T = any>(user: UserSignUpCredential): Promise<T>
  signOut<T = any>(): Promise<T>
  me<T = any>(): Promise<T>
}
