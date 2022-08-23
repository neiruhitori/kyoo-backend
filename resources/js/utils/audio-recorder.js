let mediaRecorder = null,
    audioBlobs = []

export default {
    start () {
        if (!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)) {
            return Promise.reject(new Error('mediaDevices API or getUserMedia method is not supported in this browser.'))
        }

        return navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                mediaRecorder = new MediaRecorder(stream)

                audioBlobs = []

                mediaRecorder.addEventListener('dataavailable', event => {
                    audioBlobs.push(event.data)
                })

                mediaRecorder.start()
            })
    },

    stop: function () {
        return new Promise(resolve => {
            const mimeType = mediaRecorder.mimeType

            mediaRecorder.addEventListener('stop', () => {
                const audioBlob = new Blob(audioBlobs, { type: mimeType })
                
                resolve(audioBlob)
            })

            mediaRecorder.stop()

            mediaRecorder = null
        })
    }
}