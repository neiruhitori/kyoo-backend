<template>
  <div class="monitor-container">
    <div class="monitor-sidebar">
      <div class="calling-card">
        <div class="calling-card-header">DIPANGGIL</div>

        <div class="calling-card-body">
          <h4 class="queue-no" v-if="servingQueue">{{ servingQueue.queue_no }}</h4>
          <p v-if="servingQueue && servingQueue.name">{{ servingQueue.name }}</p>
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
        <div class="waiting-card" v-for="q in waitingQueue" :key="q.queue_no">
          <h4 class="queue-no">{{ q.queue_no }}</h4>
          <p v-if="q.name || q.phone">{{ q.name || q.phone }}</p>
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
    branch_id_encrypted: {
      type: String,
      required: true,
    },
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
    Echo.channel(`event_direct_queue_general.${this.branch.id}`)
      .listen('DirectQueue', () => {
        this.getQueues();
      })
      .listen('QueueStatusUpdated', (message) => {
        this.getQueues();

        if (message.status && ['recall', 'served'].includes(message.status)) {
          this.getQueueCallAudio(message.queue_no);
        }
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
        const data = await axios.get(
          `/direct-queue/branch/${this.$props.branch_id_encrypted}/list`
        );

        const queues = data.data.data;
        
        this.waitingQueue = queues.filter(v => v.status === "waiting");
        this.servingQueue = queues.find(v => v.status === 'served');
      } catch (error) {
        alert(error.response.data.message);
      }
      this.isLoading = false;
    },

    async getQueueCallAudio(queueNo) {
      const { data: { audio } } = await axios.get(`/queue-caller`, {
        params: {
          queue_no: queueNo
        }
      });

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
}

.monitor-sidebar {
  width: 100%;
  max-width: 23rem;
  overflow: hidden;
}

.calling-card {
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 1.125rem;
}

.calling-card-header {
  background-color: #1C56AA;
  color: #FFFFFF;
  height: 2.2rem;
  align-items: center;
  display: flex;
  justify-content: center;
}

.calling-card-body {
  background-color: #103C7C;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  height: 6.3rem;
}

.waiting-list {
  display: flex;
  flex-direction: column;
  gap: .5rem;
}

.waiting-card {
  background-color: #FFFFFF;
  border: 2px solid #AAAAAA;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  height: 6.3rem;
}

.waiting-card-empty {
  background-color: #AAAAAA;
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
  margin-bottom: .5rem;
}

.queue-no {
  font-size: 3rem;
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
  font-weight: 700;
  font-size: 1.5rem;
}

.monitor-signature {
  display: inline-flex;
  align-items: center;
  color: #7A7A7A;
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
  background-color: #04111D;
  overflow: hidden; 
  border: .5px solid #DDDDDD;
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
