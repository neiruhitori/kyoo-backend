<template>
  <div class="monitor-container">
    <div class="monitor-sidebar">
      <h6 class="sidebar-subtitle">DIPANGGIL</h6>

      <div class="calling-card">
        <div class="calling-counter" v-if="servingQueue">
          <div class="calling-card-header">Counter</div>

          <div class="calling-card-body">
            <h4 class="queue-no">
              {{
                servingQueue.workstation
                  ? servingQueue.workstation.label[servingQueue.workstation.label.length - 1]
                  : ''
              }}
            </h4>
          </div>
        </div>

        <div class="calling-service" v-if="servingQueue">
          <div class="calling-card-header">
            {{ servingQueue && servingQueue.service
              ? servingQueue.service.name
              : 'Layanan' }}
          </div>
  
          <div class="calling-card-body">
            <h4 class="queue-no">{{ servingQueue.number }}</h4>
          </div>
        </div>
      </div>

      <h6 class="sidebar-subtitle">MENUNGGU</h6>

      <div class="waiting-list" v-if="!waitingQueue.length">
        <div class="waiting-card waiting-card-empty"></div>
        <div class="waiting-card waiting-card-empty"></div>
        <div class="waiting-card waiting-card-empty"></div>
        <div class="waiting-card waiting-card-empty"></div>
        <div class="waiting-card waiting-card-empty"></div>
      </div>

      <div class="waiting-list">
        <div class="waiting-card" v-for="q in waitingQueue" :key="q.booking_code">
          <div class="waiting-card-header">
            {{ q.service ? q.service.name : ' ' }}
          </div>
  
          <div class="waiting-card-body">
            <h4 class="queue-no">{{ q.number }}</h4>
          </div>
        </div>
      </div>
    </div>

    <div class="monitor-content">
      <div class="monitor-content-header">
        <div class="monitor-company-logo">
          <img v-if="!!branch.logo" :src="`/storage/${branch.logo}`" />
        </div>

        <div class="monitor-time-container">
          <div class="monitor-date">
            <div>{{ currentDay }}</div>
            <div>{{ currentFormattedDate }}</div>
          </div>

          <div class="monitor-hour">
            {{ currentTimes }}
          </div>
        </div>
      </div>

      <div class="monitor-main-content">
        <img
          v-for="(bgImage, idx) in promotionImages"
          :key="idx + 1"
          v-show="activeImage === idx + 1"
          :src="bgImage.url"
        />
      </div>

      <div class="monitor-content-footer">
        <div class="monitor-signature">
          <span>Powered by</span>
          <img src="/img/logo-color.svg">
        </div>
      </div>
    </div>

    <div class="permission-wrapper" v-if="isAutoPlayBlocked">
      <div class="permission-body">
        <p>Browser Anda memblokir audio autoplay. Tekan tombol dibawah untuk mengaktifkan autoplay.</p>
        <button class="active-button" @click="isAutoPlayBlocked = false">Aktifkan Suara Notifikasi</button>
      </div>
    </div>
  </div>
</template>

<script>
import { format } from 'date-fns'
import { id } from 'date-fns/locale'

const IMAGE_DURATION = 5000 
const audioEl = new Audio();

export default {
  props: {
    branch: {
      type: Object,
      required: true,
    },
    features: {
      type: Array
    },
    signature: {
      type: String,
      required: true,
    },
    config: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      isLoading: false,
      waitingQueue: [],
      servingQueue: null,
      activeImage: 1,
      currentDate: new Date(),
      promotionImages: [],
      isAutoPlayBlocked: false,
      playQueue: []
    };
  },

  created() {
    Echo.channel(`branches.${this.branch.id}.appointments`)
      .listen('AppointmentCreated', () => {
        this.getQueues();
      })
      .listen('AppointmentServed', async (q) => {
        await this.getQueues();

        if (
          this.features.find(v => v.additional_feature.name === 'Panggilan Suara') &&
          this.config.queue_voice
        ) {
          this.getQueueCallAudio(q.id);
        }
      })
      .listen('AppointmentEndServed', () => {
        this.getQueues();
      })
      .listen('AppointmentCanceledEvent', () => {
        this.getQueues();
      });
  },

  async mounted() {
    this.getQueues();
    await this.getDisplayImage();

    this.animateImage();  
    this.updateCurrentDate();

    this.checkAutoplayPermission();
    this.subscribeAudioEvent()
  },

  computed: {
    currentTimes() {
      return format(this.currentDate, 'HH:mm', {
        locale: id
      })
    },

    currentDay() {
      return format(this.currentDate, 'EEEE', {
        locale: id
      })
    },
    
    currentFormattedDate() {
      return format(this.currentDate, 'dd MMMM yyyy', {
        locale: id
      })
    }
  },

  methods: {
    subscribeAudioEvent() {
      const self = this;
      audioEl.onended = function () {
        if (self.playQueue.length) {
          audioEl.src = self.playQueue[0];
          self.playQueue.shift();
          audioEl.play();
        }
      }
    },

    checkAutoplayPermission() {
      audioEl.src = '';
      const audioPromise = audioEl.play();
      audioEl.pause();

      if (audioPromise !== undefined) {
        audioPromise
          .then(() => this.isAutoPlayBlocked = false)
          .catch(() => this.isAutoPlayBlocked = true)
      }
    },

    async getDisplayImage() {
      const self = this;

      await axios.get(`/display-images/${this.branch.id}`)
        .then(res => {
          self.promotionImages = res.data;
        })
    },

    updateCurrentDate() {
      const self = this

      setInterval(function () {
        self.currentDate = new Date()
      }, 5000)
    },

    animateImage() {
      const self =  this

      setInterval(function () {  
        self.activeImage++;

        if (self.activeImage > self.promotionImages.length) {
          self.activeImage = 1;
        }
      }, IMAGE_DURATION)
    },

    async getQueues() {
      this.isLoading = true;
      try {
        const { data } = await axios.get(
          `/branches/${this.$props.signature}/appointments`
        );

        const servingQueue = data.filter(v => v.status === 'served');
        
        this.waitingQueue = data.filter(v => v.status !== 'served');
        this.servingQueue = servingQueue[servingQueue.length - 1];
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },

    async getQueueCallAudio(id) {
      const { data: { audio } } = await axios.get(`/appointments/${id}/call`);

      if (audioEl.paused) {
        audioEl.src = audio.data;
        audioEl.play();
      } else {
        this.playQueue.push(audio.data);
      }
    }
  },
};
</script>

<style scoped>
.monitor-container {
  padding: 1.625rem;
  display: flex;
  gap: 1.625rem;
  height: 100vh;
  background-color: #0D1117;
  color: #FFFFFF;
}

.monitor-sidebar {
  width: 100%;
  max-width: 23rem;
  overflow: hidden;
}

.calling-card {
  display: flex;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 2rem;
  background-color: #103C7C;
  min-height: 6.5rem;
}

.calling-card-header {
  color: #FFFFFF;
  height: 2.5rem;
  align-items: center;
  display: flex;
  justify-content: center;
}

.calling-card-body {
  color: #C1DBFF;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  height: 5.5rem;
}

.calling-counter {
  background-color: #1C56AA;
  border-radius: 8px;
  width: 7.5rem; 
}

.calling-service {
  flex: 1 1 0%;
}

.waiting-list {
  display: flex;
  flex-direction: column;
  gap: .5rem;
}

.waiting-card {
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: .5rem;
  background-color: #161B22;
  border: 1px solid #30363D;
}

.waiting-card-header {
  background-color: #30363D;
  color: #FFFFFF;
  height: 2.5rem;
  align-items: center;
  display: flex;
  justify-content: center;
}
.waiting-card-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 5.5rem;
  color: #C4CCD4;
}

.waiting-card-empty {
  background-color: #161B22;
  height: 6.5rem;
}

.calling-card-header {
  font-weight: 600;
  text-align: center;
  font-size: 1rem;
  letter-spacing: 0.06em;
}

.sidebar-subtitle {
  font-weight: 700;
  text-align: center;
  font-size: 1rem;
  letter-spacing: 0.06em;
  margin-bottom: 1rem;
}

.queue-no {
  font-size: 3rem;
  text-transform: uppercase;
}

.monitor-content {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.monitor-content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.monitor-company-logo {
  display: flex;
  align-items: center;
}

.monitor-company-logo img {
  height: 3.3rem;
}

.monitor-time-container {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.monitor-date {
  text-align: right;
}

.monitor-hour {
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 8px;
  padding: .75rem 1.5rem;
  background-color: #F5BE3F;
  color: #000000;
  font-weight: 700;
  font-size: 1.5rem;
}

.monitor-signature {
  display: inline-flex;
  align-items: center;
  display: flex;
  gap: .5rem;
  float: right;
}

.monitor-signature img {
  height: 2rem;
}

.monitor-main-content {
  flex: 1 1 0%;
  border-radius: 8px;
  background-color: #FFFFFF;
  overflow: hidden; 
  border: .5px solid #30363D;
}

.monitor-main-content img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  position: relative;
}

.permission-wrapper {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, .8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #FFFFFF;
}

.permission-body {
  text-align: center;
}

.active-button {
  background-color: #F5BE3F;
  border: none;
  color: #000000;
  padding: .375rem .75rem;
  border-radius: 8px;
  font-size: 1rem;
  display: inline-block;
  font-size: 1rem;
  margin-top: 1rem;
}
</style>
