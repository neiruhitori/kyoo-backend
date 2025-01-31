<template>
  <div class="monitor-container">
    <div class="monitor-sidebar">
      <h6 class="sidebar-subtitle">{{ t('CALLED') }}</h6>

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

      <h6 class="sidebar-subtitle">{{ t('WAITING') }}</h6>

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
                <video
                    v-if="activeImage === 1 && isVideo(promotionImages[0])"
                    width="100%"
                    height="100%"
                    autoplay
                >
                    <source :src="promotionMedia[0].data" type="video/mp4" />
                    Your browser does not support the video tag.
                </video>
                <img
                    v-else
                    v-for="(bgImage, idx) in promotionMedia"
                    :key="idx + 1"
                    v-show="activeImage === idx + 1"
                    :src="bgImage.data"
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
import { id,enUS } from 'date-fns/locale'
import ID from "../../lang/id.json";
import EN from "../../lang/en.json";

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
    lang: {
      type: String,
    },
    config: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      config: this.branch.branch_configuration,
      callAudio: [],
      isLoading: false,
      waitingQueue: [],
      servingQueue: null,
      activeImage: 1,
      currentDate: new Date(),
      promotionImages: [],
      isAutoPlayBlocked: false,
      playQueue: [],
      currentImageDuration: 5000,
      isPlaying: false,
      messages: {
                    en: EN,
                    id: ID,
                    },
      currentLocale: this.lang || "en",
      localeDate: id,
      promotionMedia: null
    };
    },

  watch: {
    async activeImage(newValue) {
      const self = this;

      const videoExtensions = [".mp4"];
      const lowerCaseUrl =
        self.promotionImages[newValue - 1].url.toLowerCase();
        const isVideo = videoExtensions.some((ext) =>
          lowerCaseUrl.endsWith(ext)
        );

      if (isVideo) {
        try {
          const response = await fetch(
            self.promotionImages[newValue - 1].url
          );
          const blob = await response.blob();

          const video = document.createElement("video");
          video.src = URL.createObjectURL(blob);

          await new Promise((resolve) => {
            video.onloadedmetadata = function () {
              const duration = video.duration * 1000;
              self.currentImageDuration = duration;
              resolve();
            };
          });
        } catch (error) {
            // console.error("Error fetching the video:", error);
        }
      } else {
        self.currentImageDuration = 5000;
      }

      this.animateImage();
    },
  },

  created() {
    Echo.channel(`branches.${this.branch.id}.appointments`)
      .listen('AppointmentCreated', () => {
        this.getQueues();
      })
      .listen('AppointmentServed', async (q) => {
        await this.getQueues();

        if (
          this.servingQueue &&
          this.features.find(v => v.additional_feature.name === 'Panggilan Suara') &&
          this.config.queue_voice
        ) {
          this.getQueueCallAudio();
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
    this.subscribeAudioEvent();
    this.saveToLocal();
    this.setLocale();
  },

  computed: {
    currentTimes() {
      return format(this.currentDate, 'HH:mm', {
        locale: this.localeDate
      })
    },

    currentDay() {
      return format(this.currentDate, 'EEEE', {
        locale: this.localeDate
      })
    },

    currentFormattedDate() {
      return format(this.currentDate, 'dd MMMM yyyy', {
        locale: this.localeDate
      })
    }
  },

  methods: {
    t(key, params = {}) {
         const translation = this.messages[this.currentLocale] && this.messages[this.currentLocale][key];
            if (translation) {
                 return translation.replace(/\{(\w+)\}/g, (_, param) => params[param] || "");
            } else {
                 return key;
            }
        },
        setLocale() {
            this.localeDate = this.currentLocale == "id" ? id : enUS;
        },
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

    isVideo(bgImage) {
        const videoExtensions = [".mp4"];
        const lowerCaseUrl = bgImage.url.toLowerCase();
        const isVideo = videoExtensions.some((ext) =>
            lowerCaseUrl.endsWith(ext)
        );

        return isVideo;
    },

    animateImage() {
        const self = this;

      self.currentInterval = setInterval(function () {
        self.activeImage++;

        if (self.activeImage > self.promotionImages.length) {
          self.activeImage = 1;
        }

        if (self.currentInterval) {
          clearInterval(self.currentInterval);
        }
      }, this.currentImageDuration);
      },

      async saveToLocal() {
            // Fetch Image And Video
            const fetchMedia = this.promotionImages.map(async media => {
                const response = await fetch(media.url);
                const blob = await response.blob();

                const base64Media = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                });

                return base64Media;
            });
            const base64Medias = await Promise.all(fetchMedia);

            // Fetch Audio
            const audios = ['intro_bell', 'nomor_antrian', 'dicounter'];
            for (let i = 0; i <= 9; i++) {
                audios.push(i.toString());
            }
            for (let i = 65; i <= 80; i++) {
                if (i !== 72) {
                    audios.push(String.fromCharCode(i));
                }
            }

            const base64Audios = [];
            const fetchAudio = audios.map(async audio => {
                const audio_url = `/storage/audio/vo/${audio}.wav`
                const response = await fetch(audio_url);
                const blob = await response.blob();

                const base64Audio = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                });

                const contentType = 'audio/wav';
                const dataURL = `data:${contentType};base64,${base64Audio.split(',')[1]}`;

                base64Audios.push({ name: audio, audio: dataURL });
            });
            await Promise.all(fetchAudio);

            if (this.db) {
                this.db.close();
            }
            // Delete the old database
            const deleteRequest = indexedDB.deleteDatabase('Media');
            const db = await indexedDB.open('Media', 1);

            db.onupgradeneeded = event => {
                const database = event.target.result;

                const mediaObjectStore = database.createObjectStore('medias', { keyPath: 'id',autoIncrement: true });
                const audioObjectStore = database.createObjectStore('audios', { keyPath: 'id' });
            };

            db.onsuccess = async event => {
                const transaction = db.result.transaction(['medias', 'audios'], 'readwrite');
                const mediaObjectStore = transaction.objectStore('medias');
                const audioObjectStore = transaction.objectStore('audios');

                const clearMediaRequest = mediaObjectStore.clear();
                await clearMediaRequest.onsuccess;

                const clearAudioRequest = audioObjectStore.clear();
                await clearAudioRequest.onsuccess;

                base64Medias.forEach((data, index) => {
                    mediaObjectStore.add({ id: index + 1, data: data });
                });

                base64Audios.forEach((data, index) => {
                    audioObjectStore.add({ id: data.name, data: data.audio });
                });
            };

            const request = window.indexedDB.open('Media', 1);
            request.onsuccess = (event) => {
                this.db = event.target.result;
                this.getMediaFromIndexedDB();
            };
        },
        getMediaFromIndexedDB() {
            const transaction = this.db.transaction(['medias', 'audios'], 'readonly');

            const mediaStore = transaction.objectStore('medias');
            const audioStore = transaction.objectStore('audios');
            const requestMedia = mediaStore.getAll();
            const requestAudio = audioStore.getAll();

            requestMedia.onsuccess = (event) => {
                this.promotionMedia = event.target.result;
            };

            requestAudio.onsuccess = (event) => {
                this.callAudio = event.target.result;
            };
        },

    getDataFromIndexedDB() {
        const transaction = this.db.transaction(['media'], 'readonly');
        const store = transaction.objectStore('media');

        const request = store.getAll();

        request.onsuccess = (event) => {
            this.promotionMedia = event.target.result;
        };
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

    async getQueueCallAudio(message) {
            if (this.isPlaying) {
                this.playQueue.push(message);
                return;
            }

            this.isPlaying = true;

            const queueNo = this.servingQueue.number;
            const audio = ['intro_bell', 'nomor_antrian'];
            const counter_id = this.servingQueue.workstation.label.replace(/\D/g, "");

            audio.push(...queueNo.toString().split(''), 'dicounter');
            if (counter_id) {
                audio.push(...counter_id.split(""));
            }

            const playlist = [];
            audio.forEach(audioID => {
                const matchingAudio = this.callAudio.find(call => call.id === audioID);
                if (matchingAudio) {
                    playlist.push(matchingAudio.data);
                }
            });

            for (const audioData of playlist) {
                if (audioEl.paused) {
                    audioEl.src = audioData;
                    audioEl.play();
                } else {
                    this.playQueue.push(audioData);
                }

                await new Promise(resolve => {
                    audioEl.onended = resolve;
                });
            }

            this.isPlaying = false;
            if (this.playQueue.length > 0) {
                const nextMessage = this.playQueue.shift();
                await this.getQueueCallAudio(nextMessage);
            }

            const videoEl = document.querySelector('video');
            if (!videoEl) {
                return;
            }
            const originalVolume = videoEl.volume;
            videoEl.volume = 0.2;

            audioEl.onended = function() {
                videoEl.volume = originalVolume;
            };
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
