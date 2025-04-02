<template>
    <div class="row monitor-container">
        <div class="col-md-12 mt-2 mb-1">
            <span class="font-36 font-weight-700">{{ t('Scan Here') }}</span>
        </div>
        <div class="col-md-12">
            <span class="font-18 font-weight-400"
                >{{ t('To take a queue or check-in,') }}</span
            >
        </div>
        <div class="col-md-12">
            <span class="font-18  font-weight-400">{{ t('scan the QR Code below') }}</span>
        </div>
        <div class="col-md-12">
            <img v-bind:src="qr" class="qr-image" alt="qr-code" width="250" />
        </div>
        <div class="col-md-12 mb-1">
            <span class="font-24 font-weight-700">{{ branch.name }}</span>
        </div>
        <div class="col-md-12">
            <span class="font-18 font-weight-400">{{ branch.address }}</span>
        </div>
        <div class="col-md-12 mb-4">
            <span class="font-18 font-weight-400">{{
                `${address.regency}, ${address.province}`
            }}</span>
        </div>
        <div class="col-md-12 wrapper-yellow">
            <span class="font-18 font-weight-700">{{ t('HOW TO SCAN QR CODE') }}</span>
        </div>
        <div class="col-md-12 wrapper-blue">
            <div class="row">
                <div class="offset-md-3 col-md-2 wrapper-child-info">
                    <span class="number">1</span>
                    <span class="font-18 font-weight-400"
                        >{{ t('Use your phone') }} {{ t('camera app') }} {{ t('or visit') }}
                        <b>scan.kyoo.id</b></span
                    >
                </div>
                <div class="col-md-2 wrapper-child-info">
                    <span class="number">2</span>
                    <span class="font-18 font-weight-400"
                        >{{ t('Point your camera at') }} {{ t('the QR Code above') }}</span
                    >
                </div>
                <div class="col-md-2 wrapper-child-info">
                    <span class="number">3</span>
                    <span class="font-18 font-weight-400"
                        >{{ t('Select the queue') }} {{ t('service type') }}</span
                    >
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <img
                        v-bind:src="branch_logo"
                        alt="branch-logo"
                        width="50"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { format } from "date-fns";
import { id } from "date-fns/locale";
import ID from "../../lang/id.json";
import EN from "../../lang/en.json";

export default {
    props: {
        branch: {
            type: Object,
            required: true
        },
        address: {
            type: Object,
            required: true
        },
        qr: {
            type: String,
            required: true
        },
        lang:{
            type: String,
        }
    },

    async mounted() {
        // console.log(this.branch_logo);
    },

    data() {
        return {
            branch_logo: this.branch
                ? `/storage/${this.branch.logo}`
                : `/img/logo-color.svg`,
            currentLocale: this.lang || 'en',
            messages:{ en: EN,
                       id: ID,} ,
        };
    },
    methods:{
        t(key, params = {}) {
         const translation = this.messages[this.currentLocale] && this.messages[this.currentLocale][key];
            if (translation) {
                 return translation.replace(/\{(\w+)\}/g, (_, param) => params[param] || "");
            } else {
                 return key;
            }
        },
    }
};
</script>

<style scoped>
.monitor-container {
    display: flex;
    justify-content: center;
    text-align: center;
    color: #114077;
    background-color: #fafcff;
    font-family: sans-serif;
    height: 100vh;
}

.font-36 {
    font-size: 36px;
}

.font-24 {
    font-size: 24px;
}

.font-18 {
    font-size: 18px;
}

.font-weight-400 {
    font-weight: 400;
}
.font-weight-700 {
    font-weight: 700;
}

.qr-image {
    border: 4px solid #cce4ff;
    border-radius: 15px;
    margin: 40px 0;
}

.wrapper-yellow {
    padding: 15px;
    background-color: #f6d378;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wrapper-blue {
    padding: 15px;
    background-color: #cce4ff;
}

.number {
    display: inline-block;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background-color: #114077;
    color: #fafcff;
    text-align: center;
    line-height: 23px;
    font-size: 16px;
}

.wrapper-child-info {
    display: grid;
    text-align: initial;
    padding: 15px 12px;
    gap: 20px;
    align-content: baseline;
}
</style>
