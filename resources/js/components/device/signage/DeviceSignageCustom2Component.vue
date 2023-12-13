<template>
    <div class="container" v-bind:style="[background]">
        <div class="monitoring">
            <div class="monitor-content-header">
                <div class="monitor-company-logo">
                    <img
                        v-if="!!branch.logo"
                        :src="`/storage/${branch.logo}`"
                    />
                </div>
                <div class="monitor-time-container" v-bind:style="[dateTime]">
                    <div class="monitor-date">
                        <span>{{ currentDay }}</span>
                        <span>{{ currentFormattedDate }}</span>
                    </div>

                    <span class="monitor-hour" v-bind:style="[lineHour]">{{ currentTimes }} WIB</span>
                </div>
            </div>
            <div class="monitor-container">
                <div class="monitor-sidebar">
                    <span class="sidebar-subtitle" v-bind:style="[sidebarSubtitle]">MENUNGGU</span>

                    <div class="waiting-list" v-if="!waitingQueue.length">
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                        <div class="waiting-card" v-bind:style="[waitingCard]"/>
                    </div>

                    <div class="waiting-list" v-if="waitingQueue.length">
                        <div class="waiting-card" v-bind:style="[waitingCard]" v-for="q in waitingQueue" :key="q.queue_no">
                            <h4 class="queue-no">{{ q.queue_no }}</h4>
                        </div>
                    </div>
                </div>

                <div class="monitor-main-content">
                    <img v-for="(bgImage, idx) in promotionImages" :key="idx + 1" v-show="activeImage === idx + 1" :src="bgImage.url" />
                </div>
            </div>
            <div class="workstation-list">
                <div class="calling-card" v-for="workstation in workstations" :key="workstation.queue_no">
                    <div class="calling-card-header" v-bind:style="[callingCardHeader]">{{ workstation.label }}</div>

                    <div class="calling-card-body" v-bind:style="[callingCardBody]">
                        <h4 class="calling-no" v-if="servingQueue[workstation.id] && servingQueue[workstation.id].queue_no !== undefined">
                            <span v-bind:style="[firstLetter]">{{ servingQueue[workstation.id].queue_no.substring(0, 1) }}</span><span v-bind:style="[nextLetter]">{{ servingQueue[workstation.id].queue_no.substring(1) }}</span>
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
        <div class="running-text-container" v-bind:style="[lineContainer]">
            <div class="running-text" v-bind:style="[runningText]">
                {{ this.custom_layout_config.running_text || 'Powered By Kyoo' }}
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
        },
        custom_layout_config: {
            type: Object,
            required: true,
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
            background:
                this.custom_layout_config.background_type == "image"
                    ? {
                          "background-image": `url('/storage/${this.custom_layout_config.background_image}')`,
                          "background-repeat": "no-repeat",
                          "background-size": "cover"
                      }
                    : {
                          "background-color": this.custom_layout_config
                              .background_color || "#182db3"
                      },
            dateTime: {
                color: this.custom_layout_config.datetime_color || "#FFFFFF"
            },
            lineHour: {
                "border-left": `2px solid ${this.custom_layout_config.datetime_color || "#FFFFFF"}`
            },
            sidebarSubtitle: {
                color: this.custom_layout_config.sidebar_subtitle_color || "#FFFFFF",
                border: `2px solid ${this.custom_layout_config.sidebar_subtitle_color || "#FFFFFF"}`
            },
            waitingCard: {
                "background-color": this.custom_layout_config.waiting_list_card_color || "#f9fafb",
                color: this.custom_layout_config.waiting_list_font_color || "#118bfa"
            },
            callingCardHeader: {
                "background-color": this.custom_layout_config.calling_card_header_color || "#e0f0ff",
                color: this.custom_layout_config.calling_card_font_header_color || "#233c8c"
            },
            callingCardBody: {
                "background-color": this.custom_layout_config.calling_card_body_color || "#233c8c",
            },
            firstLetter: {
                color: this.custom_layout_config.font_queue_first_letter_color || "#118bfa"
            },
            nextLetter: {
                color: this.custom_layout_config.font_queue_color || "#e0f0ff"
            },
            runningText: {
                color: this.custom_layout_config.running_text_color || "#FFFFFF",
                animation: `running-text-animate ${this.custom_layout_config.running_text_speed || 10}s linear infinite`
            },
            lineContainer: {
                "border-top": `3px solid ${this.custom_layout_config.running_text_color || "#FFFFFF"}`
            }
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
            return format(this.currentDate, "dd-MM-yyyy", {
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
    .container{
        display: flex;
        flex-direction: column;
        overflow: hidden;
        color: #2c3e50;
        height: 100vh;
    }
    .monitoring {
        padding: 1.625rem;
        display: flex;
        flex-direction: column;
        overflow: hidden;
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
        max-width: 35rem;
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
        display: grid;
        gap: 0.5rem;
        grid-template-columns: auto auto;
    }
    .waiting-card {
        border: 1px solid #bdc3c7;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        height: 4.0rem;
    }
    .calling-card-header {
        font-weight: 700;
        text-align: center;
        font-size: 2rem;
        letter-spacing: 0.06em;
    }
    .sidebar-subtitle {
        font-weight: 900;
        font-size: 1.5rem;
        letter-spacing: 0.06em;
        margin-bottom: 0.5rem;
        display: block;
        text-align: center;
        padding: 12px 0;
        border-radius: 4px;
    }
    .queue-no {
        font-size: 2.25rem;
    }
    .calling-no{
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
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .monitor-date {
        text-align: right;
        display: grid;
        font-size: 1.5rem;
    }
    .monitor-hour {
        padding: 0.4rem 0.5rem;
        font-weight: 700;
        font-size: 3.5rem;
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
        object-fit: stretch;
        position: relative;
    }
    .running-text-container {
        overflow: hidden;
    }
    .running-text {
        white-space: nowrap;
        overflow: hidden;
        padding-top: 1.625rem;
        padding-bottom: 1.625rem;
        font-size: 1.625rem;
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

