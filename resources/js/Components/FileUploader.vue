<script>
    export default {
        watch: {
            chunks : {
                handler(n, o) {
                    if (n.length > 0) {
                        this.upload();
                    }else{
                        this.$emit('fileUploaded', this.file);
                    }
                },
                deep: true
            }
        },

        data() {
            return {
                file: null,
                chunks: [],
                uploaded: 0,
                chunksize: 1024 * 1000,
                progress: 0,
                speedUpload: 0,
                startTime: 0,
            };
        },

        computed: {
            formData() {
                let formData = new FormData;

                formData.set('is_last', this.chunks.length === 1);
                formData.set('file', this.chunks[0], `${this.file.name}.part`);

                return formData;
            },
            config() {
                return {
                    method: 'POST',
                    data: this.formData,
                    url: '/sendfile',
                    headers: {
                        'Content-Type': 'application/octet-stream'
                    },
                    onUploadProgress: event => {
                        this.uploaded += event.loaded;
                        this.progress = Math.min(Math.floor((this.uploaded/this.file.size) * 100),100);
                        if(this.startTime === 0)
                            this.startTime = (Date.now() / 1000);
                        else {
                            let time = (Date.now() / 1000) - this.startTime;
                            this.speedUpload = Math.floor((this.uploaded/1024) / time);
                        }
                    }
                };
            }
        },

        methods: {
            select(event) {
                this.progress = 0;
                this.uploaded = 0;
                this.startTime = 0;
                this.file = event.target.files.item(0);
                this.createChunks();
            },
            upload() {
                axios(this.config).then(response => {
                    this.chunks.shift();
                }).catch(error => {});
            },
            createChunks() {
                let chunks = Math.ceil(this.file.size / this.chunksize);

                for (let i = 0; i < chunks; i++) {
                    this.chunks.push(this.file.slice(
                        i * this.chunksize, Math.min(i * this.chunksize + this.chunksize, this.file.size), this.file.type
                    ));
                }
            }
        },

        emits: ['fileUploaded'],
    }
</script>

<template>
    <div>
        <input type="file" @change="select" name="">
        <span class="mx-2">{{ speedUpload }} kB/s</span>
        <progress  :value="progress" max="100"></progress>
    </div>
</template>