<template>
  <div style="max-width: 765px; margin: 0 auto;">
    <div class="mb-3">
      <h3 class="mb-0">WhatsApp Session</h3>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="!qr">
          <p>Informasi Session</p>

          <table class="table mb-0">
            <tr class="px-2">
              <th>Status</th>
              <td>
                <span :class="`fas fa-circle ${isOnline ? 'text-success' : 'text-danger' } mr-1`"></span>
                {{ isOnline ? 'Online' : 'Offline' }}
              </td>
            </tr>

            <tr>
              <th>No. Telepon</th>
              <td>{{ me.phone_number || '-' }}</td>
            </tr>

            <tr>
              <th>Nama</th>
              <td>{{ me.name || '-' }}</td>
            </tr>
          </table>
        </div>

        <div v-else>
          <div class="d-flex justify-content-between">
            <div class="align-self-center">
              <p class="lead mb-0 mt-0">Scan kode QR untuk login ke WhatsApp</p>
              <span v-if="isQrExpired" class="text-danger">QR kadaluarsa. Refresh halaman ini untuk memperbarui QR</span>
            </div>
  
            <div class="position-relative">
              <img :src="this.qr" alt="QR code">

              <div v-if="isQrExpired" class="qr-refresh-wrapper">
                <div class="qr-refresh-btn">
                  <span class="fas fa-exclamation-triangle"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    this.fetchQr()
  },

  data() {
    return {
      qr: '',
      issuedAt: null,
      me: {
        phone_number: '',
        name: ''
      },
      isOnline: false,
      isQrExpired: false,
    }
  },

  methods: {
    async fetchQr() {
      try {
        const { data: me } = await axios.get('/admin/waSession/me')
        this.me = me
        this.isOnline = true
        this.qr = ''
        this.issuedAt = null
      } catch (err) {
        this.isOnline = false
        this.isQrExpired = false

        const { data } = await axios.get('/admin/waSession/qr')
        if (data.qr) {
          this.qr = data.qr
          this.issuedAt = data.issued_at

          console.log({
            issuedAt: moment(data.issued_at).format('DD/MM/YYYY HH:mm:ss'),
            expiredAt: moment(data.issued_at).add(45, 'seconds').format('DD/MM/YYYY HH:mm:ss'),
          })

          setTimeout(() => {
            this.isQrExpired = true
          }, moment(data.issued_at).add(45, 'seconds').diff(moment()))
        }
      }
    }
  }
}
</script>

<style scoped>
.qr-refresh-wrapper {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(246, 194, 62, .8);
  display: flex;
  align-items: center;
  justify-content: center;
}

.qr-refresh-btn {
  font-size: 2.5rem;
  border: none;
  outline: none;
  border-radius: 9999px;
  width: 130px;
  height: 130px;
  background-color: #f6c23e;
  color: black;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}
</style>
