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
                <img v-for="(bgImage, idx) in promotionImages" :key="idx + 1" v-show="activeImage === idx + 1" :src="bgImage.url" />
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

const IMAGE_DURATION = 5000;
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
        };
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
                    ["recall", "served"].includes(message.status)
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
        animateImage() {
            const self = this;

            setInterval(function () {
                self.activeImage++;

                if (self.activeImage > self.promotionImages.length) {
                    self.activeImage = 1;
                }
            }, IMAGE_DURATION);
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
            const queueID = this.servingQueue[message.workstation_id].id
            const {
                data: { audio },
            } = await axios.get(`/queue-caller/${queueID}`);

            if (audioEl.paused) {
                audioEl.src = audio.data;
                audioEl.play();
            } else {
                this.playQueue.push(audio.data);
            }
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
