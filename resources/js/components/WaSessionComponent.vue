<template>
  <div style="max-width: 800px; margin: 0 auto;">
    <div class="mb-3">
      <h3 class="mb-0">WhatsApp Session</h3>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="!qr">
          <table class="table table-bordered  mb-0">
            <tr class="px-2">
              <th>Status</th>
              <td>
                <span class="fas fa-circle text-success mr-1"></span>
                Online
              </td>
            </tr>

            <tr>
              <th>No. Telepon</th>
              <td>{{ me.phone_number }}</td>
            </tr>

            <tr>
              <th>Nama</th>
              <td>{{ me.name }}</td>
            </tr>
          </table>
        </div>

        <div v-else>
          <p>Scan kode QR untuk login ke WhatsApp</p>

          <div>
            <img :src="this.qr" alt="QR code">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    this.initData()
  },

  data() {
    return {
      qr: '',
      me: {
        phone_number: '',
        name: ''
      }
    }
  },

  methods: {
    async initData() {
      console.log('Refreshed...')

      try {
        const { data: me } = await axios.get('/admin/waSession/me')
        this.me = me
        this.qr = ''
      } catch (err) {
        const { data } = await axios.get('/admin/waSession/qr')
        this.qr = data.qr
      }

      setTimeout(async () => {
        this.initData()
      }, 5000)
    }
  }
}
</script>
