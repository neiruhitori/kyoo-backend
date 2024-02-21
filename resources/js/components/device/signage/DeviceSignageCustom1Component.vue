<template>
    <div class="monitoring">
        <div class="monitor-container">
            <div class="monitor-sidebar">
                <div class="monitor-content-header">
                    <div class="monitor-company-logo">
                        <img
                            v-if="!!branch.logo"
                            :src="`/storage/${branch.logo}`"
                        />
                    </div>
                    <div class="monitor-time-container">
                        <div class="monitor-date">
                            <span>{{ currentDay }}</span>
                            <span>{{ currentFormattedDate }}</span>
                        </div>

                        <span class="monitor-hour">{{ currentTimes }}</span>
                    </div>
                </div>

                <span class="sidebar-subtitle">MENUNGGU</span>

                <div class="waiting-list" v-if="!waitingQueue.length">
                    <div class="waiting-card waiting-card-empty"/>
                    <div class="waiting-card waiting-card-empty"/>
                    <div class="waiting-card waiting-card-empty"/>
                    <div class="waiting-card waiting-card-empty"/>
                    <div class="waiting-card waiting-card-empty"/>
                </div>

                <div class="waiting-list" v-if="waitingQueue.length">
                    <div class="waiting-card" v-for="q in waitingQueue" :key="q.queue_no">
                        <h4 class="queue-no">{{ q.queue_no }}</h4>
                        <p v-if="q.name || q.phone">{{ q.name || q.phone }}</p>
                    </div>
                </div>
            </div>

            <div class="monitor-main-content">
                <video
                    v-if="promotionImages[0] && activeImage === 1 && isVideo(promotionImages[0])"
                    width="100%"
                    height="100%"
                    autoplay
                >
                    <source v-if="promotionMedia[0]" :src="promotionMedia[0].data" type="video/mp4" />
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
        </div>
        <div class="workstation-list">
            <div class="calling-card" v-for="workstation in workstations" :key="workstation.queue_no">
                <div class="calling-card-header">{{ workstation.label }}</div>

                <div class="calling-card-body">
                    <h4 class="queue-no" v-if="servingQueue[workstation.id]">
                        {{ servingQueue[workstation.id].queue_no }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="permission-wrapper" v-if="isAutoPlayBlocked">
            <div class="permission-body">
                <p>
                    Browser Anda memblokir audio autoplay. Tekan tombol dibawah
                    untuk mengaktifkan autoplay.
                </p>
                <button class="active-button" @click="isAutoPlayBlocked = false">
                    Aktifkan Suara Notifikasi
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { format } from "date-fns";
import { id } from "date-fns/locale";

const audioEl = new Audio();

export default {
    props: {
        branch: {
            type: Object,
            required: true,
        },
        features: {
            type: Array,
        },
        workstations: {
            type: Array,
        }
    },

    data() {
        const servingQueue = {};
        this.workstations.forEach(item => { servingQueue[item.id] = {} });
        return {
            config: this.branch.branch_configuration,
            isLoading: false,
            waitingQueue: [],
            servingQueue: servingQueue,
            activeImage: 1,
            currentDate: new Date(),
            promotionImages: [],
            isAutoPlayBlocked: false,
            playQueue: [],
            currentImageDuration: 5000,
            promotionMedia: [],
            callAudio: [],
            isPlaying: false,
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
                    console.error(error);
                }
            } else {
                self.currentImageDuration = 5000;
            }

            this.animateImage();
        },
    },

    created() {
        Echo.channel(`event_direct_queue_general.${this.branch.id}`)
            .listen("DirectQueue", () => {
                this.getQueues();
            })
            .listen("QueueStatusUpdated", async (message) => {
                if (
                    !message.queue_no ||
                    !["recall", "served"].includes(message.status)
                ) {
                    return await this.getQueues();
                }

                console.log("call queue no", message.queue_no);
                await this.getQueues(message.queue_no);

                if (
                    this.config.queue_voice &&
                    this.features.find((v) => v.additional_feature.name === "Panggilan Suara") &&
                    this.servingQueue[message.workstation_id] &&
                    message.status &&
                    ["recall", "served"].includes(message.status) &&
                    !message.not_called
                ) {
                    this.getQueueCallAudio(message);
                }
            });
    },

    async mounted() {
        this.getQueues();
        await this.getDisplayImage();

        this.animateImage();
        this.updateCurrentDate();

        this.checkAutoPlayPermission();
        this.subscribeAudioEvent();
        this.saveToLocal();
    },

    computed: {
        currentTimes() {
            return format(this.currentDate, "HH:mm", {
                locale: id,
            });
        },

        currentDay() {
            return format(this.currentDate, "EEEE", {
                locale: id,
            });
        },

        currentFormattedDate() {
            return format(this.currentDate, "dd MMMM yyyy", {
                locale: id,
            });
        },
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
            };
        },
        checkAutoPlayPermission() {
            audioEl.src = "";
            const audioPromise = audioEl.play();
            audioEl.pause();

            if (audioPromise !== undefined) {
                audioPromise
                    .then(() => (this.isAutoPlayBlocked = false))
                    .catch(() => (this.isAutoPlayBlocked = true));
            }
        },
        async getDisplayImage() {
            const self = this;

            await axios.get(`/display-images/${this.branch.id}`).then((res) => {
                self.promotionImages = res.data;
            });
        },
        updateCurrentDate() {
            const self = this;

            setInterval(function () {
                self.currentDate = new Date();
            }, 5000);
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

                base64Audios.push({ name: audio, audio: base64Audio });
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
        async getQueues(queue_no) {
            this.isLoading = true;
            try {
                const data = await axios.get(
                    `/device/branch/${this.$props.branch.id}/queue`
                );

                const queues = data.data.data;
                this.waitingQueue = queues.filter(
                    (v) => v.status === "waiting" || v.status === "requeue"
                );

                if (queue_no) {
                    const activeQueue = queues.find(
                        (v) => v.queue_no === queue_no
                    );
                    this.servingQueue[activeQueue.workstation_id] = activeQueue
                } else {
                    const servingQueue = {}
                    this.workstations.forEach(item => {
                        const activeQueue = queues.find((v) => v.status === "served" && v.workstation_id === item.id);
                        servingQueue[item.id] = activeQueue
                    });

                    this.servingQueue = servingQueue;
                }
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

            const servingQueue = this.servingQueue[message.workstation_id];
            const queueNo = servingQueue.queue_no;
            const workstation = this.workstations.find(workstation => workstation.id === servingQueue.workstation_id);
            const counter_id = workstation.label.replace(/\D/g, '');
            const audio = ['intro_bell', 'nomor_antrian'];

            audio.push(...queueNo.split(''), 'dicounter');
            if(counter_id) {
                audio.push(...counter_id.split(''));
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
        },
    },
};
</script>

<style scoped>
    .workstation-list {
        display: flex;
        gap: 0.5rem;
    }
    .workstation-card {
        background-color: #ecf0f1;
        border: 1px solid #bdc3c7;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        height: 6.3rem;
        width: 100%;
        color: #2c3e50;
    }
    .monitoring {
        padding: 1.625rem;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background-color: #ffffff;
        color: #2c3e50;
        gap: 1.625rem;
        height: 100vh;
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
    .monitor-container {
        display: flex;
        flex: 1 1 0%;
        gap: 1.625rem;
        overflow: hidden;
    }
    .monitor-sidebar {
        width: 100%;
        max-width: 23rem;
        overflow: hidden;
    }
    .calling-card {
        border-radius: 8px;
        overflow: hidden;
        width: 100%;
    }

    .calling-card-header {
        background-color: #1c56aa;
        color: #ffffff;
        height: 2.2rem;
        align-items: center;
        display: flex;
        justify-content: center;
    }
    .calling-card-body {
        background-color: #103c7c;
        color: #c1dbff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        height: 6.3rem;
    }
    .waiting-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .waiting-card {
        background-color: #ecf0f1;
        border: 1px solid #bdc3c7;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        height: 6.3rem;
        color: #2c3e50;
    }
    .waiting-card-empty {
        background-color: #ecf0f1;
    }
    .calling-card-header {
        font-weight: 600;
        text-align: center;
        font-size: 1rem;
        letter-spacing: 0.06em;
    }
    .sidebar-subtitle {
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: 0.06em;
        margin-bottom: 0.5rem;
        display: block;
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
    .monitor-time-container {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .monitor-date {
        text-align: right;
        display: grid;
    }
    .monitor-hour {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        background-color: #f5be3f;
        color: #000000;
        font-weight: 700;
        font-size: 1.5rem;
    }
    .monitor-main-content {
        width: 100%;
        border-radius: 8px;
        background-color: #ffffff;
        overflow: hidden;
        border: 0.5px solid #bdc3c7;
    }
    .monitor-main-content img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: relative;
    }
    .monitor-main-content video {
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
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #ffffff;
    }
    .permission-body {
        text-align: center;
    }
    .active-button {
        background-color: #f5be3f;
        border: none;
        color: #000000;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 1rem;
        display: inline-block;
        font-size: 1rem;
        margin-top: 1rem;
    }
</style>
