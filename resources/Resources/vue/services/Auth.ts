import axios from 'axios'
import {
  UserSignInCredential,
  UserSignUpCredential,
  UserSignUpVerification,
  UserAccVerifCredential,
  UserOtpDeleteVerification,
  ClusterVerification,
  UserForgotPasswordCredential,
  UserForgotPasswordVerification,
  UserChangePassword,
} from '~/types/services/auth'
import { useConfigStore } from '~/store/config'
import { useAuthStore } from '~/store/auth'

export default {
  signIn(credential: UserSignInCredential) {
    return new Promise((resolve, reject) => {
      axios('/api/signin', {
        method: 'POST',
        data: credential,
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },
  accVerification(credential: UserAccVerifCredential) {
    return new Promise((resolve, reject) => {
      axios('/api/verification', {
        method: 'POST',
        data: credential,
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  forgotPassword(credential: UserForgotPasswordCredential) {
    return new Promise((resolve, reject) => {
      axios('/api/forgot-password-bo', {
        method: 'POST',
        data: credential,
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  changePassword(credential: UserChangePassword) {
    return new Promise((resolve, reject) => {
      axios('/api/change-password', {
        method: 'POST',
        data: credential,
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  forgotPasswordVerification(data: UserForgotPasswordVerification,) {
    return new Promise((resolve, reject) => {
      axios('/api/forgot-verification/' + data.otp_code, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },

  signUp(credential: UserSignUpCredential) {
    return new Promise((resolve, reject) => {
      axios('/api/signup', {
        method: 'POST',
        data: credential,
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },
  signUpVerification(data: UserSignUpVerification) {
    return new Promise((resolve, reject) => {
      axios('/api/signup-verification/' + data.otp_code, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },
  clusterVerification(data: ClusterVerification) {
    const cookieToken = localStorage.getItem('_token')
    return new Promise((resolve, reject) => {
      axios('/api/v1/ihm_m_users/verification_new_cluster', {
        method: 'POST',
        data,
      })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },
  otpDeleteVerification(data: UserOtpDeleteVerification) {
    return new Promise((resolve, reject) => {
      axios('/api/delete-verification/' + data.otp_code, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  },
  signOut() {
    return new Promise((resolve, reject) => {
      const cookieToken = localStorage.getItem('_token')

      axios('/api/signout', {
        method: 'POST',
        headers: {
          Authorization: cookieToken,
          'Content-Type': 'application/json',
        },
      })
        .then((response) => {
          const configStore = useConfigStore()
          configStore.pagedrop.enabled = true
          const authStore = useAuthStore()
          setTimeout(() => {
            localStorage.removeItem('_loggedIn')
            localStorage.removeItem('_token')
            localStorage.removeItem('_me')

            authStore.user = undefined
            authStore.loggedIn = false
            resolve(true)
            setTimeout(() => {
              configStore.pagedrop.enabled = false
              resolve(response)
            }, 1000)
          }, 1000)
        })
        .catch((err) => {
          console.log('signout failed!', err.response)
          reject(err)
        })
    })
  },
  me() {
    return new Promise((resolve, reject) => {
      const cookieToken = localStorage.getItem('_token')
      const authStore = useAuthStore()
      axios('/api/me', {
        method: 'GET',
        headers: {
          Authorization: cookieToken,
          'Content-Type': 'application/json',
        },
      })
        .then((res) => {
          authStore.saveMe(res.data.data).then(() => {
            resolve(res.data.data)
          })
        })
        .catch((err) => {
          if (err.response.status === 401) {
            const authStore = useAuthStore()
            localStorage.removeItem('_loggedIn')
            localStorage.removeItem('_token')
            localStorage.removeItem('_me')
            authStore.user = undefined
            authStore.loggedIn = false
          }
          reject(err)
        })
    })
  },
}
