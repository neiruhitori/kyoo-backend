<template>
    <div class="container" v-bind:style="[background]">
        <div class="monitoring">
            <div class="monitor-content-header">
                <div class="monitor-company-logo">
                    <img
                        v-if="!!branch.logo"
                        :src="`/storage/${branch.logo}`"
                        :style="{ height: `${custom_layout_config.logo_size || 2.3}rem` }"
                    />
                </div>
                <div class="monitor-time-container" v-bind:style="[dateTime]">
                    <div class="monitor-date" v-bind:style="[monitorDate]">
                        <span>{{ currentDay }}</span>
                        <span>{{ currentFormattedDate }}</span>
                    </div>

                    <span class="monitor-hour" v-bind:style="[lineHour]">{{ currentTimes }} {{ branch.timezone }}</span>
                </div>
            </div>
            <div class="monitor-container">
                <div class="monitor-sidebar">
                    <span class="sidebar-subtitle" v-bind:style="[sidebarSubtitle]">{{ t('WAITING') }}</span>

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
        <template v-if="promotionImages.length">
            <div class="video-container">
        <video
            v-if="promotionImages[activeImage - 1].type === 'video'"
            width="100%"
            height="100%"
            autoplay
            :key="activeImage"
        >
            <source :src="promotionImages[activeImage - 1].url" type="video/mp4" />
            Your browser does not support the video tag.
        </video>

        <iframe
            v-if="promotionImages[activeImage - 1].type === 'youtube'"
            width="100%"
            height="100%"
            id="player"
            :key="activeImage"
            :src="`https://www.youtube.com/embed/${getYouTubeId(promotionImages[activeImage - 1].url)}?enablejsapi=1&controls=0&autoplay=1`"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
        ></iframe>

        <img
            v-else-if="promotionImages[activeImage - 1].type === 'image'"
            :src="promotionImages[activeImage - 1].url"
            alt="Promotion Image"
            width="100%"
            height="100%"
        />
        <div class="iframe-overlay"></div>
    </div>
    </template>
                </div>
            </div>
            <div class="workstation-list">
                <div class="calling-card" v-for="workstation in workstations" :key="workstation.queue_no">
                    <div class="calling-card-header" v-bind:style="[callingCardHeader]">{{ workstation.label }}</div>

                    <div class="calling-card-body" v-bind:style="[callingCardBody]">
                        <h4 class="calling-no" :id="'calling-no-' + workstation.id" v-if="servingQueue[workstation.id] && servingQueue[workstation.id].queue_no !== undefined">
                            <span v-bind:style="[firstLetter]">{{ servingQueue[workstation.id].queue_no.substring(0, 1) }}</span><span v-bind:style="[nextLetter]">{{ servingQueue[workstation.id].queue_no.substring(1) }}</span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="permission-wrapper" v-if="isAutoPlayBlocked">
                <div class="permission-body">
                    <p>
                      Your browser is blocking audio autoplay. Press the button below to enable autoplay.
                    </p>
                    <button class="active-button" @click="isAutoPlayBlocked = false">
                        Enable Notification Sound
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
import moment from 'moment';
import 'moment-timezone';
import ID from "../../../../lang/id.json";
import EN from "../../../../lang/en.json";

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
        },
        display_duration: {
            type: Number,
        },
        lang:{
            type: String,
        }
    },

    data() {
        const servingQueue = {};
        this.workstations.forEach(item => { servingQueue[item.id] = {} });
        return {
            config: this.branch.branch_configuration,
            isLoading: false,
            waitingQueue: [],
            currentLocale: this.lang || 'en',
            vo_style: this.branch?.branch_configuration.vo_call_style ?? 'standard',
            vo_format: this.branch?.branch_configuration.signage_vo_format ?? 'wav',
            messages:{ en: EN,
                       id: ID,} ,
            servingQueue: servingQueue,
            activeImage: 1,
            currentDate: moment.tz('Asia/Jakarta'),
            promotionImages: [],
            isAutoPlayBlocked: false,
            playQueue: [],
            displayDuration: this.display_duration,
            currentImageDuration: this.display_duration || 5000,
            promotionMedia: [],
            callAudio: [],
            isPlaying: false,
            country: this.branch.country,
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
                "border-left": `2px solid ${this.custom_layout_config.datetime_color || "#FFFFFF"}`,
                "font-size": `${this.custom_layout_config.text_time_size ? parseFloat(this.custom_layout_config.text_time_size) + 0.5 : 1.5}rem`,
            },
            monitorDate: {
                "font-size": `${this.custom_layout_config.text_time_size || 1}rem`,
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
                "font-size": `${this.custom_layout_config.running_text_size || 1.625}rem` ,
                animation: `running-text-animate ${this.custom_layout_config.running_text_speed || 10}s linear infinite`
            },
            lineContainer: {
                "border-top": `2px solid ${this.custom_layout_config.running_text_color || "#FFFFFF"}`
            },
            volumeYT: this.custom_layout_config.youtube_volume
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
            const isYouTube = this.isYouTube(self.promotionImages[newValue - 1].url);

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
                    this.animateImage();
                } catch (error) {
                    console.error(error);
                }
            } else if(isYouTube){                
                try{
                    setTimeout(() => {
                        this.player = new YT.Player('player', {
                        playerVars: {
                            'playsinline': 1
                        },
                        events: {
                            'onReady': this.onPlayerReady,
                            'onStateChange': this.onPlayerStateChange
                        }
                    });
                    }, 1000);
                }catch(error){
                    console.error(error);
                }
            }else {
                self.currentImageDuration = self.displayDuration;
                this.animateImage();
            }

            
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
        this.initCurrentDate();
        this.getQueues();
        await this.getDisplayImage();
        await this.initMedia();

        this.animateImage();
        this.updateCurrentDate();

        this.checkAutoPlayPermission();
        this.subscribeAudioEvent();
        this.saveToLocal();
    },

    computed: {
        currentTimes() {
            return this.currentDate.format('HH:mm');
        },

        currentDay() {
            return this.currentDate.format('dddd');
        },

        currentFormattedDate() {
            return this.currentDate.format('DD-MM-YYYY');
        },

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
        initCurrentDate() {
            let currentDate = moment.tz('Asia/Jakarta');
            const branchTimeZone = this.branch.timezone;

            const timezoneMap = {
            'WIB': 'Asia/Jakarta',
            'WITA': 'Asia/Makassar',
            'WIT': 'Asia/Jayapura',
            'SGT': 'Asia/Singapore', 
            'MYT': 'Asia/Singapore', 
            'BNT': 'Asia/Singapore',
            'ICT': 'Asia/Bangkok', 
            'AEST': 'Australia/Sydney', 
            'AWST': 'Australia/Perth', 
            'ACST': 'Australia/Darwin',  
            'TLT': 'Asia/Dili', 
            };

            currentDate = moment.tz(timezoneMap[branchTimeZone]);
            this.currentDate = currentDate;
        },
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
        async initMedia() {
        const firstMedia = this.promotionImages[0]; // Ambil media pertama

        if (firstMedia.type === 'video') {
            // Video lokal
            try {
                const response = await fetch(firstMedia.url);
                const blob = await response.blob();
                const video = document.createElement("video");
                video.src = URL.createObjectURL(blob);

                await new Promise((resolve) => {
                    video.onloadedmetadata = () => {
                        this.currentImageDuration = video.duration * 1000; // Durasi dalam milidetik
                        resolve();
                    };
                });

                // console.log(`Durasi video pertama: ${this.currentImageDuration} ms`);
            } catch (error) {
                console.error("Error loading video:", error);
            }
        } else if (firstMedia.type === 'youtube') {
            // Video YouTube
            try {
                this.player = await new YT.Player('player', {
                playerVars: {
                    'playsinline': 1
                },
                events: {
                    'onReady': this.onPlayerReady,
                    'onStateChange': this.onPlayerStateChange
                }
                });
        
            } catch (error) {
                console.error("Error loading YouTube video:", error);
            }
        } else if (firstMedia.type === 'image') {
            this.currentImageDuration = this.displayDuration;
            // console.log("Media pertama adalah gambar. Durasi default:", this.currentImageDuration);
        }

        this.animateImage();
    },

        onPlayerReady(event) {
            this.player.setVolume(this.volumeYT);
            event.target.playVideo();
            this.player = event.target;
        },
        onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING) {
                const ytduration = event.target.getDuration() * 1000 - 1000; // Durasi video dalam milidetik
                // console.log("Video Playing - Duration:", ytduration);
            // Cek apakah audio signage masih berjalan sebelum mengubah volume
            if (this.isPlaying) {
                // console.log("Audio signage is active, setting YouTube volume to 0%");
                this.player.setVolume(0); // Set volume ke 0% saat audio signage berjalan
            } 
                // Set durasi hanya jika belum ditetapkan
                if (!this.currentImageDuration || this.currentImageDuration !== ytduration) {
                    this.currentImageDuration = ytduration;
                }

                // Pastikan animasi berjalan dengan durasi yang benar
                this.animateImage(); 
                
            }else if(event.data == YT.PlayerState.ENDED){
                this.currentImageDuration = 0;
                this.animateImage();
            }
        },
        async getDisplayImage() {
            const self = this;
            try {
                    const response = await axios.get(`/display-images/${this.branch.id}`);
                    self.promotionImages = response.data.map((item) => ({
                        name: item.name,
                        url: item.url,
                        type: self.getMediaType(item.url), // Menentukan jenis media
                    }));
                } catch (error) {
                    console.error('Error fetching display images:', error);
                }
                
        },
        getMediaType(url) {
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                return 'youtube';
            } else if (url.endsWith('.mp4')) {
                return 'video';
            } else {
                return 'image';
            }
        },
        updateCurrentDate() {
            const self = this;

            setInterval(function () {
                self.initCurrentDate();
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
        isYouTube(url) {
        const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/;
        return youtubeRegex.test(url);
    },
    getYouTubeId(url) {
        const urlParams = new URLSearchParams(new URL(url).search);
        return urlParams.get('v') || url.split('/').pop();
    },
        animateImage() {
            const self = this;
            // Clear interval sebelumnya jika ada
            if (self.currentInterval) {
                clearInterval(self.currentInterval);
            }


            self.currentInterval = setInterval(function () {
                self.activeImage++;

                if (self.activeImage > self.promotionImages.length) {
                    self.activeImage = 1;
                }

                if (self.currentInterval) {
                    clearInterval(self.currentInterval);
                }
            }, this.currentImageDuration || 5000);
        },
        
        async saveToLocal() {
            const fetchMedia = this.promotionImages.map(async media => {
                if (this.isYouTube(media.url)) {
                    return null; // Skip fetching YouTube media
                }
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
            let audios = ['intro_bell'];
            const isIndonesia = this.country === 'Indonesia';

            audios.push(isIndonesia ? 'nomor_antrian' : 'customer_number');
            if (this.vo_style !== 'simple') {
                const phrase = isIndonesia ? ['dicounter'] : ['please_proceed', 'to_counter'];
                audios.push(...phrase);
            }

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
                let audio_url = `/storage/audio/vo/${audio}.${this.vo_format}`;
                if (this.country != 'Indonesia') {
                    audio_url = `/storage/audio/vo_en/${audio}.wav`
                }
                const response = await fetch(audio_url);
                const blob = await response.blob();

                const base64Audio = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                });

                const contentType = `audio/${this.vo_format === 'mp3' ? 'mpeg' : 'wav'}`;
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

                    if (activeQueue && activeQueue.workstation_id) {
                        this.servingQueue[activeQueue.workstation_id] = activeQueue;
                    }
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
        isBlink(workstationId) {
            const callingNo = document.getElementById('calling-no-' + workstationId);
            if (callingNo) {
                callingNo.classList.add('calling-no-blink');
                setTimeout(() => {
                    callingNo.classList.remove('calling-no-blink');
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

            if(!servingQueue?.queue_no) {
                this.isPlaying = false;
                if (this.playQueue.length > 0) {
                    const nextMessage = this.playQueue.shift();
                    await this.getQueueCallAudio(nextMessage);
                }
                return;
            }

            const queueNo = servingQueue.queue_no;
            const workstation = this.workstations.find(workstation => workstation.id === servingQueue.workstation_id);
            const counter_id = workstation.label.replace(/\D/g, '');
            let audio = ['intro_bell'];
            const isIndonesia = this.country === 'Indonesia';

            audio.push(isIndonesia ? 'nomor_antrian' : 'customer_number');
            audio.push(...queueNo.toString().split(''));

            if (this.vo_style !== 'simple') {
                const phrase = isIndonesia ? ['dicounter'] : ['please_proceed', 'to_counter'];
                audio.push(...phrase, ...(counter_id ? counter_id.split('') : []));
            }
           

            const playlist = [];
            audio.forEach(audioID => {
                const matchingAudio = this.callAudio.find(call => call.id === audioID);
                if (matchingAudio) {
                    playlist.push(matchingAudio.data);
                }
            });

             if (this.player && this.player.setVolume) {
                this.player.setVolume(0); // Set volume ke 0%
                }
                await new Promise(wait => setTimeout(wait, 1000));

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
            await new Promise(wait => setTimeout(wait, 1000));

            if (this.player && this.player.setVolume) {
                    this.player.setVolume(this.volumeYT); // Kembalikan volume YouTube
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
.iframe-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0); /* Transparan */
    z-index: 1; /* Pastikan overlay di atas elemen lain */
}

.video-container {
    position: relative; /* Untuk membungkus overlay */
    width: 100%;
    height: 100%;
}
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
        max-width: 40rem;
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
        height: 4rem;
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
        border-radius: 8px;
    }
    .queue-no {
        font-size: 2.25rem;
    }
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }
    .calling-no-blink {
        animation: blink 3s ease-in-out infinite;
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
        /* font-size: 1.5rem; */
    }
    .monitor-hour {
        padding: 0.4rem 0.5rem;
        font-weight: 700;
        /* font-size: 2.5rem; */
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
        object-fit: fill;
        position: relative;
    }
    .monitor-main-content video {
        width: 100%;
        height: 100%;
        object-fit: fill;
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
        /* font-size: 1.625rem; */
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
        .monitor-date{
            font-size: 16px;
        }
        .monitor-hour {
            font-size: 1.5rem;
        }
        .sidebar-subtitle {
            font-size: 1rem;
            letter-spacing: 0.06em;
            margin-bottom: 0.5rem;
            display: block;
            text-align: center;
            padding: 10px 0;
            border-radius: 8px;
        }
        .waiting-card {
            border-radius: 8px;
            height: 3.25rem;
        }
        .queue-no {
            font-size: 2rem;
        }
        .monitor-sidebar {
            max-width: 30rem;
        }
        .calling-card-header {
            height: 2.5rem;
            font-size: 1.5rem;
        }
        .calling-card-body {
            height: 5rem;
        }
        .calling-no{
            font-size: 2.50rem;
        }
        .running-text {
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
            /* font-size: 1rem; */
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
