<template>
    <div class="container" v-bind:style="[background]">
        <div class="monitoring">
            <div class="monitor-content-header">
                <div class="monitor-company-logo">
                    <img
                        v-if="!!branch.logo"
                        :src="`/storage/${branch.logo}`"
                    />
                    <span v-bind:style="[branchName]">{{ branch.name }}</span>
                </div>
                <div class="monitor-time-container" v-bind:style="[dateTime]">
                    <div class="monitor-date">
                        <span>{{ currentFormattedDate }}</span>
                    </div>

                    <span class="monitor-hour"
                        >{{ currentTimes }}</span
                    >
                </div>
            </div>
            <div class="monitor-container">
                <div class="monitor-main-content">
                    <video
                        v-if="
                            promotionImages[0] &&
                            activeImage === 1 &&
                            isVideo(promotionImages[0])
                        "
                        width="100%"
                        height="100%"
                        autoplay
                    >
                        <source
                            v-if="promotionMedia[0]"
                            :src="promotionMedia[0].data"
                            type="video/mp4"
                        />
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

                <div class="monitor-sidebar" v-bind:style="[callingCardBody]">
                    <div class="oncall-waiting-card" v-if="Object.keys(servingQueue).length > 0">
                        <div
                            v-for="workstation in workstations.slice(0, 2)" :key="workstation.queue_no"
                            :key="workstation.queue_no"
                            class="waiting-card"
                            :id="'calling-no-' + workstation.id"
                        >
                            <div>
                                <span v-bind:style="[firstLetter]">{{ servingQueue[workstation.id].queue_no.substring(0, 1) }}</span><span v-bind:style="[nextLetter]">{{ servingQueue[workstation.id].queue_no.substring(1) }}</span>
                            </div>
                            <span v-bind:style="[counter]">{{ workstation.label }}</span>
                        </div>
                    </div>

                    <div class="sidebar-subtitle" v-bind:style="[cardCounterQueueNumber]">
                        <span>NO ANTRIAN</span>
                        <span>COUNTER</span>
                    </div>

                    <div class="waiting-list" v-for="workstation in workstations.slice(2)" :key="workstation.queue_no" v-if="Object.keys(servingQueue).length > 0">
                        <div
                            class="waiting-card"
                            :id="'calling-no-' + workstation.id"
                        >
                            <div>
                                <span v-bind:style="[firstLetter]">{{ servingQueue[workstation.id].queue_no.substring(0, 1) }}</span><span v-bind:style="[nextLetter]">{{ servingQueue[workstation.id].queue_no.substring(1) }}</span>
                            </div>
                            <svg width="36" height="22" viewBox="0 0 23 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.297 1.82943L20.8717 14.4041C23.4101 16.9425 23.4101 21.0581 20.8717 23.5965L8.297 36.1712C6.5185 37.9497 3.63981 37.9652 1.84229 36.2059C0.0087747 34.4114 0.00263408 31.4622 1.82866 29.6601L11.0673 20.5424C11.9269 19.6941 11.9269 18.3066 11.0673 17.4582L1.82866 8.34052C0.00262654 6.53838 0.00877646 3.58923 1.84229 1.79473C3.63981 0.0354595 6.51851 0.050936 8.297 1.82943Z" fill="#FFDB1B"/>
                            </svg>
                            <span v-bind:style="[counter]">{{ workstation.label }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="permission-wrapper" v-if="isAutoPlayBlocked">
                <div class="permission-body">
                    <p>
                        Browser Anda memblokir audio autoplay. Tekan tombol
                        dibawah untuk mengaktifkan autoplay.
                    </p>
                    <button
                        class="active-button"
                        @click="isAutoPlayBlocked = false"
                    >
                        Aktifkan Suara Notifikasi
                    </button>
                </div>
            </div>
        </div>
        <div class="running-text-container" v-bind:style="[callingCardHeader]">
            <div class="running-text" v-bind:style="[runningText]">
                {{
                    this.custom_layout_config.running_text || "Powered By Kyoo"
                }}
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
        custom_layout_config: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            config: this.branch.branch_configuration,
            workstations: [],
            isLoading: false,
            waitingQueue: [],
            servingQueue: [],
            activeImage: 1,
            currentDate: new Date(),
            promotionImages: [],
            isAutoPlayBlocked: false,
            playQueue: [],
            currentImageDuration: 5000,
            promotionMedia: [],
            callAudio: [],
            isPlaying: false,
            background:
                this.custom_layout_config.background_type == "image"
                    ? {
                          "background-image": `url('/storage/${this.custom_layout_config.background_image}')`,
                          "background-repeat": "no-repeat",
                          "background-size": "cover",
                      }
                    : {
                          "background-color":
                              this.custom_layout_config.background_color ||
                              "#182db3",
                      },
            branchName: {
                color:
                    this.custom_layout_config.sidebar_subtitle_color ||
                    "#FFFFFF"
            },
            dateTime: {
                color: this.custom_layout_config.datetime_color || "#FFFFFF",
            },
            callingCardHeader: {
                "background-color": this.custom_layout_config.calling_card_header_color || "#e0f0ff",
            },
            callingCardBody: {
                "background-color": this.custom_layout_config.calling_card_body_color || "#233c8c",
            },
            counter: {
                color:
                    this.custom_layout_config.waiting_list_font_color ||
                    "#118bfa",
            },
            cardCounterQueueNumber: {
                "background-color": this.custom_layout_config.waiting_list_card_color || "#f9fafb",
                color: this.custom_layout_config.font_queue_color || "#e0f0ff",
            },
            firstLetter: {
                color: this.custom_layout_config.font_queue_first_letter_color || "#118bfa"
            },
            nextLetter: {
                color: this.custom_layout_config.font_queue_color || "#e0f0ff"
            },
            runningText: {
                color:
                    this.custom_layout_config.running_text_color || "#FFFFFF",
                animation: `running-text-animate ${
                    this.custom_layout_config.running_text_speed || 10
                }s linear infinite`,
            },
            lineContainer: {
                "border-top": `2px solid ${
                    this.custom_layout_config.running_text_color || "#FFFFFF"
                }`,
            },
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
                this.getWorkstations();
                this.getQueues();
            })
            .listen("QueueStatusUpdated", async (message) => {
                if (
                    !message.queue_no ||
                    !["recall", "served"].includes(message.status)
                ) {
                    return await this.getQueues();
                }

                await this.getWorkstations();
                await this.getQueues(message.queue_no);

                if (
                    this.config.queue_voice &&
                    this.features.find(
                        (v) => v.additional_feature.name === "Panggilan Suara"
                    ) &&
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
        await this.getDisplayImage();
        this.getWorkstations();
        this.getQueues();

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
            return format(this.currentDate, "EEEE, dd-MM-yyyy", {
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
            const fetchMedia = this.promotionImages.map(async (media) => {
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
            const audios = ["intro_bell", "nomor_antrian", "dicounter"];
            for (let i = 0; i <= 9; i++) {
                audios.push(i.toString());
            }
            for (let i = 65; i <= 80; i++) {
                if (i !== 72) {
                    audios.push(String.fromCharCode(i));
                }
            }

            const base64Audios = [];
            const fetchAudio = audios.map(async (audio) => {
                const audio_url = `/storage/audio/vo/${audio}.wav`;
                const response = await fetch(audio_url);
                const blob = await response.blob();

                const base64Audio = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                });

                const contentType = "audio/wav";
                const dataURL = `data:${contentType};base64,${
                    base64Audio.split(",")[1]
                }`;

                base64Audios.push({ name: audio, audio: dataURL });
            });
            await Promise.all(fetchAudio);

            if (this.db) {
                this.db.close();
            }
            // Delete the old database
            const deleteRequest = indexedDB.deleteDatabase("Media");
            const db = await indexedDB.open("Media", 1);

            db.onupgradeneeded = (event) => {
                const database = event.target.result;

                const mediaObjectStore = database.createObjectStore("medias", {
                    keyPath: "id",
                    autoIncrement: true,
                });
                const audioObjectStore = database.createObjectStore("audios", {
                    keyPath: "id",
                });
            };

            db.onsuccess = async (event) => {
                const transaction = db.result.transaction(
                    ["medias", "audios"],
                    "readwrite"
                );
                const mediaObjectStore = transaction.objectStore("medias");
                const audioObjectStore = transaction.objectStore("audios");

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

            const request = window.indexedDB.open("Media", 1);
            request.onsuccess = (event) => {
                this.db = event.target.result;
                this.getMediaFromIndexedDB();
            };
        },
        getMediaFromIndexedDB() {
            const transaction = this.db.transaction(
                ["medias", "audios"],
                "readonly"
            );

            const mediaStore = transaction.objectStore("medias");
            const audioStore = transaction.objectStore("audios");
            const requestMedia = mediaStore.getAll();
            const requestAudio = audioStore.getAll();

            requestMedia.onsuccess = (event) => {
                this.promotionMedia = event.target.result;
            };

            requestAudio.onsuccess = (event) => {
                this.callAudio = event.target.result;
            };
        },
        async getWorkstations() {
            this.isLoading = true;
            try {
                const data = await axios.get(
                    `/device/branch/${this.$props.branch.id}/workstations`
                );

                const workstations = data.data.data;
                this.workstations = workstations;

                this.workstations.forEach((item) => {
                    this.$set(this.servingQueue, item.id, {});
                });
            } catch (error) {
                alert(error.response.data.message);
            }
            this.isLoading = false;
        },
        async getQueues(queue_no) {
            this.isLoading = true;
            try {
                const data = await axios.get(
                    `/device/branch/${this.$props.branch.id}/queue/served`
                );

                const queues = data.data.data;
                this.waitingQueue = queues.filter(
                    (v) => v.status === "waiting" || v.status === "requeue"
                );

                if (queue_no) {
                    const activeQueue = queues.find(
                        (v) => v.queue_no === queue_no
                    );
                    this.servingQueue[activeQueue.workstation_id] = activeQueue;
                } else {
                    const servingQueue = {};
                    this.workstations.forEach((item) => {
                        const activeQueue = queues.find(
                            (v) =>
                                v.status === "served" &&
                                v.workstation_id === item.id
                        );
                        servingQueue[item.id] = activeQueue;
                    });

                    this.servingQueue = servingQueue;
                }
            } catch (error) {
                alert(error.response.data.message);
            }
            this.isLoading = false;
        },
        isBlink(workstationId) {
            const callingNo = document.getElementById(
                "calling-no-" + workstationId
            );
            if (callingNo) {
                callingNo.classList.add("calling-no-blink");
                setTimeout(() => {
                    callingNo.classList.remove("calling-no-blink");
                }, 9000);
            }
        },
        async getQueueCallAudio(message) {
            if (this.isPlaying) {
                this.playQueue.push(message);
                return;
            }

            this.isPlaying = true;
            this.isBlink(message.workstation_id);

            const servingQueue = this.servingQueue[message.workstation_id];
            const queueNo = servingQueue.queue_no;
            const workstation = this.workstations.find(
                (workstation) => workstation.id === servingQueue.workstation_id
            );
            const counter_id = workstation.label.replace(/\D/g, "");
            const audio = ["intro_bell", "nomor_antrian"];

            audio.push(...queueNo.split(""), "dicounter");
            if (counter_id) {
                audio.push(...counter_id.split(""));
            }

            const playlist = [];
            audio.forEach((audioID) => {
                const matchingAudio = this.callAudio.find(
                    (call) => call.id === audioID
                );
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

                await new Promise((resolve) => {
                    audioEl.onended = resolve;
                });
            }

            this.isPlaying = false;
            if (this.playQueue.length > 0) {
                const nextMessage = this.playQueue.shift();
                await this.getQueueCallAudio(nextMessage);
            }

            const videoEl = document.querySelector("video");
            if (!videoEl) {
                return;
            }
            const originalVolume = videoEl.volume;
            videoEl.volume = 0.2;

            audioEl.onended = function () {
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
.container {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    color: #2c3e50;
    height: 100vh;
}
.monitoring {
    padding: 0.625rem;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    color: #2c3e50;
    gap: 0.625rem;
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
    padding-right: 20px;
}
.monitor-company-logo span {
    font-size: 24px;
    font-weight: 700;
    color: #fff;
}
.monitor-container {
    display: flex;
    flex: 1 1 0%;
    gap: 0.625rem;
    overflow: hidden;
}
.monitor-sidebar {
    width: 100%;
    max-width: 25rem;
    overflow: hidden;
}
.calling-card {
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
}
.calling-card-header {
    color: #233c8c;
    height: 5rem;
    align-items: center;
    display: flex;
    justify-content: center;
}
.calling-card-body {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    height: 7rem;
}

.waiting-list {
    padding: 10px 0;
}

.oncall-waiting-card {
    padding: 10px 0;
}

.oncall-waiting-card .waiting-card {
    padding: 32px 28px;
}

.waiting-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    height: 4rem;
}

.waiting-card span {
    font-size: 32px;
    font-weight: 700;
    color: white;
}

.calling-card-header {
    font-weight: 700;
    text-align: center;
    font-size: 2rem;
    letter-spacing: 0.06em;
}
.sidebar-subtitle {
    display: flex;
    justify-content: space-between;
    font-weight: 900;
    font-size: 1.5rem;
    letter-spacing: 0.06em;
    margin-bottom: 0.5rem;
    text-align: center;
    padding: 10px 20px;
    font-size: 32px;
    background-color: #FFDB1B;
}
.queue-no {
    font-size: 2.25rem;
}
@keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
.calling-no-blink {
    animation: blink 3s ease-in-out infinite;
}
.calling-no {
    font-size: 3.25rem;
}
.monitor-content {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.monitor-time-container {
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.monitor-date {
    text-align: right;
    display: grid;
    font-size: 1.625rem;
    font-weight: 700;
}
.monitor-hour {
    padding: 0 0.5rem;
    font-weight: 700;
    font-size: 3.5rem;
}
.monitor-main-content {
    width: 100%;
    background-color: #ffffff;
    overflow: hidden;
    border: 0.5px solid #bdc3c7;
}
.monitor-main-content img {
    width: 100%;
    height: 100%;
    object-fit: stretch;
    position: relative;
}
.monitor-main-content video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: relative;
}
.running-text-container {
    overflow: hidden;
}
.running-text {
    white-space: nowrap;
    overflow: hidden;
    padding-top: 0.9rem;
    padding-bottom: 0.9rem;
    font-size: 1.8rem;
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

@media only screen and (max-width: 1400px) {
    .monitor-company-logo img {
        height: 2.3rem;
    }
    .monitor-date {
        font-size: 16px;
    }
    .monitor-hour {
        font-size: 1.5rem;
    }
    .sidebar-subtitle {
        font-size: 1rem;
        letter-spacing: -0.04em;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        text-align: center;
        padding: 10px 20px;
        font-size: 24px;
    }
    .waiting-card {
        height: 3.25rem;
    }
    .queue-no {
        font-size: 2rem;
    }
    .monitor-sidebar {
        max-width: 25rem;
    }
    .calling-card-header {
        height: 2.5rem;
        font-size: 1.5rem;
    }
    .calling-card-body {
        height: 5rem;
    }
    .calling-no {
        font-size: 2.5rem;
    }
    .running-text {
        padding-top: 0.8rem;
        padding-bottom: 0.8rem;
        font-size: 1rem;
    }
}
</style>

<style>
@keyframes running-text-animate {
    0% {
        transform: translateX(100%);
    }
    100% {
        transform: translateX(-30%);
    }
}
</style>
