<template>
  <div class="form-control w-full max-w-xs">
    <label class="label">
      <span class="label-text">Import Users</span>
    </label>
    <input
      type="file"
      class="file-input file-input-sm file-input-bordered w-full max-w-xs"
      :disabled="isLoading"
      @change="methods.onChange"
    />
  </div>
</template>
<script setup lang="ts">
  import { ref } from 'vue'
  import readXlsxFile from 'read-excel-file'
  import { Notyf, Axios } from '~/services'

  const isLoading = ref(false)
  const methods = {
    onChange: async (event) => {
      let cType = event.target.files[0].type
        .replace(
          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
          'xlsx',
        )
        .replace('application/vnd.ms-excel', 'xls')

      if (!(cType === 'xlsx' || cType === 'xls')) {
        return Notyf({
          type: 'error',
          message: 'Mohon maaf, tipe file yang anda masukan tidak valid',
          duration: 3000,
          ripple: true,
          dismissible: true,
          position: {
            x: 'right',
            y: 'top',
          },
        })
      }

      isLoading.value = true
      const fields = {
        NAMA: 'name',
        'JENIS KELAMIN': 'gender',
        'TANGGAL LAHIR': 'date_birth',
        ALAMAT: 'address',
        TELPON: 'phone',
        EMAIL: 'email',
        ROLE: 'role',
        CODE: 'r_code',
        RT: 'rt',
        RW: 'rw',
        USERNAME: 'username',
        PASSWORD: 'password',
      }

      let rt_rw = null
      await readXlsxFile(event.target.files[0], { sheet: 'RTRW' }).then(
        (rows) => {
          while (rows[0][8] !== 'CODE') {
            rows.shift()
          }

          const heads = rows.shift()
          if (heads.includes('CODE')) {
            rt_rw = rows.flatMap((value) => {
              const temp = {}

              Object.assign(
                temp,
                ...value.flatMap((value, index) => {
                  const head = heads[index]
                  const field = fields[head]
                  if (head !== null && head !== 'NO' && value)
                    return {
                      [field]: value,
                    }
                  return []
                }),
              )

              if (Object.keys(temp).length) return temp
              return []
            })
          }
        },
      )

      let users = null
      await readXlsxFile(event.target.files[0], { sheet: 'Users' }).then(
        (rows) => {
          while (rows[0][8] !== 'RT' && rows[0][9] !== 'RW') {
            rows.shift()
          }

          const heads = rows.shift()
          if (heads.includes('RT') && heads.includes('RW')) {
            users = rows.flatMap((value) => {
              const temp = {}
              Object.assign(
                temp,
                ...value.map((value, index) => {
                  const head = heads[index]
                  const field = fields[head]
                  if (head !== null && head !== 'NO' && value)
                    return {
                      [field]: value,
                    }
                  return []
                }),
              )

              if (Object.keys(temp).length) return temp
              return []
            })
          }
        },
      )

      const isEmptyArray = (arr) => {
        if (Array.isArray(arr) && arr.length) return false

        return true
      }

      if (isEmptyArray(rt_rw) || isEmptyArray(users)) {
        isLoading.value = false
        return Notyf({
          type: 'error',
          message: "Sheet 'RTRW' / 'Users' invalid atau kosong",
          duration: 3000,
          ripple: true,
          dismissible: true,
          position: {
            x: 'right',
            y: 'top',
          },
        })
      }

      await Axios({
        method: 'POST',
        url: `/api/v1/ihm_m_users/import_users`,
        data: {
          users: rt_rw,
        },
      }).catch((err) => {
        if (err.response.data?.message != 'Tidak ada users baru') {
          isLoading.value = false
          Notyf({
            type: 'error',
            message: err.response.data.message,
            duration: 3000,
            ripple: true,
            dismissible: true,
            position: {
              x: 'right',
              y: 'top',
            },
          })
        }
      })

      if (isLoading.value) {
        Axios({
          method: 'POST',
          url: `/api/v1/ihm_m_users/import_users`,
          data: {
            users,
          },
        })
          .then((res: any) => {
            if (res.data?.success) {
              Notyf({
                type: 'success',
                message: res.data.message,
                duration: 3000,
                ripple: true,
                dismissible: true,
                position: {
                  x: 'right',
                  y: 'top',
                },
              })
            }
          })
          .catch((err) => {
            Notyf({
              type: 'error',
              message: err.response.data.message,
              duration: 3000,
              ripple: true,
              dismissible: true,
              position: {
                x: 'right',
                y: 'top',
              },
            })
          })
          .finally(() => {
            isLoading.value = false
          })
      }
    },
  }
</script>
