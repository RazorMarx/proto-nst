<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FileUploaderMultiple from '@/Components/FileUploaderMultiple.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, reactive } from 'vue';
import axios from 'axios';


const data = reactive({
    files: [],
});

onMounted(() =>
    {
        getMyFiles();
    }
);

function getMyFiles() {
    axios.get('/files').then(response => {
        data.files = response.data;
    }).catch(error => {
        console.log(error);
    });
}

function deleteFile(file) {
    axios.delete(`/deletefile/${file.name}`).then(response => {
        getMyFiles();
    }).catch(error => {
        console.log(error);
    });
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Your Files</h2>
        </template>

        <div class="py-2 sm:py-12 h-full ">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-2">
                    <FileUploaderMultiple @fileUploaded="getMyFiles"/>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-for="f in data.files">
                        <div class="p-4 text-gray-900 flex flex-col sm:flex-row justify-between w-full border-b">
                            <div class="text-ellipsis whitespace-nowrap overflow-hidden">{{ f.name }}</div>
                            <div class="min-w-[180px] flex justify-end">
                                <a :href="f.url_file" class="mr-2" target="_blank">Show</a>
                                <a :href="f.url" class="mr-2">Download</a>
                                <button @click="deleteFile(f)" class="text-red-600">Delete</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="data.files.length<=0">
                        <div class="p-4 text-gray-900 flex justify-between">
                            <div>No files uploaded yet</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
