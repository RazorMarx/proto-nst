<script>
import axios from 'axios';

    export default {
        data() {
            return {
                files: [],
                chunksize: 1024 * 4e3,
                isDragging: false,
            };
        },

        methods: {
            pickFiles(event) {
                if(event.target.files.length > 0){
                    for(let i = 0; i < event.target.files.length; i++){
                        this.files.push({
                            file: event.target.files.item(i),
                            progress: 0,
                            uploaded: 0,
                            startTime: 0,
                            speedUpload: 0,
                            chunksize: this.chunksize,
                            chunks: [],
                            sended: false,
                            stop: false,
                            fails: 0,
                        });
                    }
                }
                this.files.forEach(file => {
                    if(!file.sended) this.createChunks(file);
                });
            },
            upload(file) {
                let formData = new FormData;
                formData.set('is_last', file.chunks.length === 1);
                formData.set('file', file.chunks[0], `${file.file.name}.part`);
                axios({
                    method: 'POST',
                    data: formData,
                    url: '/sendfile',
                    headers: {
                        'Content-Type': 'application/octet-stream'
                    }
                }).then(response => {
                    file.fails = 0;
                    let cf = file.chunks.shift();
                    file.uploaded += cf.size;
                    file.progress = Math.min(Math.floor((file.uploaded/file.file.size) * 100),100);

                    if(file.startTime === 0)
                        file.startTime = Date.now();
                    else {
                        file.speedUpload = Math.floor(file.uploaded / (Date.now() - file.startTime));
                    }

                    if(file.chunks.length > 0){
                        if(!file.stop) this.upload(file);
                    }else{
                        if(file.speedUpload === 0) file.speedUpload = Math.round(file.file.size / 1024);
                        file.sended = true;
                        this.$emit('fileUploaded', file);
                    }
                }).catch(error => {
                    if(!file.stop && file.fails < 5){
                        this.upload(file);
                    }
                    file.fails++;
                });
            },
            createChunks(file) {
                let chunks = Math.ceil(file.file.size / file.chunksize);
                for (let i = 0; i < chunks; i++) {
                    file.chunks.push(file.file.slice(i * file.chunksize, Math.min(i * file.chunksize + file.chunksize, file.file.size), file.file.type));
                }
                this.upload(file);
            },
            dragleave(){
                this.isDragging = false;
            },
            dragover(e){
                e.preventDefault();
                this.isDragging = true;
            },
            dropfile(e){
                e.preventDefault();
                this.$refs.uploader.files = e.dataTransfer.files;
                this.$refs.uploader.dispatchEvent(new Event('change'));
                this.isDragging = false;
            },
            removeUpload(file){
                if(!file.sended){
                    file.stop = true;
                    axios.delete(`/deletefilepart/${file.file.name}`).then(response => {
                        }).catch(error => {
                    });
                }
                this.files.splice(this.files.indexOf(file),1);
            },
            pauseUpload(file){
                file.stop = true;
                file.startTime = 0;
                file.speedUpload = 0;
            },
            resumeUpload(file){
                file.stop = false;
                this.upload(file);
            }
        },
        emits: ['fileUploaded'],
    }
</script>

<template>
    <div @dragover="dragover" @dragleave="dragleave" @drop="dropfile">
        <input type="file" @change="pickFiles" multiple class="hidden" id="fileuploader" name="fileuploader" ref="uploader">
        <label for="fileuploader" class="cursor-pointer">
            <p v-if="!isDragging">Drag files here or <u class="text-nephele-dark">Click here</u></p>
            <p v-if="isDragging">Drop files here</p>
        </label>
        <hr v-if="files.length>0" class="my-4">
        <div v-for="f in files" class="mb-2 flex w-full">
            
            <div class="flex items-center min-w-[290px] w-1/3">
                <span class="scale-150 pr-1 text-red-600 cursor-pointer select-none" @click="removeUpload(f)">&times;</span>
                <span v-if="!f.stop && !f.sended" class="scale-x-150 scale-y-50 pr-1 text-gray-600 cursor-pointer select-none" @click="pauseUpload(f)">||</span>
                <span v-if="f.stop && !f.sended" class="scale-150 pr-1 text-gray-600 cursor-pointer select-none" @click="resumeUpload(f)">&gt;</span>
                <progress :value="f.progress" max="100"></progress>
                <span class="px-2"> {{ f.speedUpload }} KB/s</span>
            </div>
            <p class="text-ellipsis overflow-hidden whitespace-nowrap">{{ f.file.name }}</p>
        </div>
    </div>
</template>