<template>
  <div class="card">
    <div class="card-body">
      <div class="form-group">
        <label for="name">Nama User</label>
        <input type="text" v-model="mName" id="name" class="form-control" autocomplete="off">
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" v-model="mEmail" id="email" class="form-control" autocomplete="off">
      </div>

      <div class="form-group">
        <label for="phone">No. HP</label>
        <input type="tel" v-model="mPhone" id="phone" class="form-control" autocomplete="off">
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    name: String,
    email: String,
    phone: String,
  },

  computed: {
    mName: {
      get() { return this.name },
      set(value) {
        return this.$emit('change', {
          field: 'name',
          value: value
        })
      } 
    },
    mEmail: {
      get() { return this.email },
      set(value) {
        return this.$emit('change', {
          field: 'email',
          value: value
        })
      }
    },
    mPhone: {
      get() { return this.phone },
      set(value) {
        return this.$emit('change', {
          field: 'phone',
          value: value
        })
      }
    },
  }
}
</script>