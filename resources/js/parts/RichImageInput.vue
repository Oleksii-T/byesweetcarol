
<template>
    <div class="rii-wrapper is-vue" :data-uuid="uuid">
        <input @change="fileUploaded" type="file" class="v-rii-filefile d-none" accept="image/*">
        <input type="hidden" class="rii-file" v-model="file.id_old">
        <input type="hidden" class="rii-file" v-model="file.id">
        <input type="hidden" class="rii-filealt" v-model="file.alt">
        <input type="hidden" class="rii-filetitle" v-model="file.title">
        <!--
        <div class="row">
            <div class="col-4">
                <div class="v-rii-box" @click="imageBoxClick">
                    <img v-if="file.url" :src="file.url">
                    <span v-if="!file.url">
                        <br>Drag files here,<br>or click to upload
                    </span>
                </div>
            </div>
            <div class="col-8">
                <div :class="{'rii-wrapper-multiple-inner': canDelete}">
                    <table class="rii-inputs">
                        <tr>
                            <td>
                                <label for="">Name:</label>
                            </td>
                            <td>
                                <input type="text" class="rii-input form-control rii-filename" :value="file.original_name" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="">Alt:</label>
                            </td>
                            <td>
                                <input type="text" class="rii-input form-control rii-filealt" v-model="file.alt">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="">Title:</label>
                            </td>
                            <td>
                                <input type="text" class="rii-input form-control rii-filetitle" v-model="file.title">
                            </td>
                        </tr>
                    </table>
                    <div v-if="canDelete" class="rii-wrapper-multiple-remove" @click="$emit('RichImageInputDeleted')">
                        <span>X</span>
                    </div>
                </div>
            </div>
        </div>
        -->
        <div class="rii-content">
            <div class="v-rii-box" @click="imageBoxClick">
                <img v-if="file.url" :src="file.url" class="rii-filepreview">
                <span v-if="file.original_name" class="rii-filename">{{file.original_name}}</span>
                <span v-else>Drag files here, or click to upload</span>
            </div>
            <div>
                <button type="button" class="rii-action-btn rii-action-browse" data-toggle="modal" data-target="#select-image">
                    <i class="fas fa-fw fa-folder-open"></i>
                </button>
                <button type="button" class="rii-action-btn rii-action-editnew d-none" data-toggle="modal" data-target="#edit-image-meta">
                    <i class="fas fa-fw fa-edit"></i>
                </button>
                <a v-if="file.id" :href="editUrl()" target="_blank" class="rii-action-btn">
                    <i class="fas fa-fw fa-edit"></i>
                </a>
                <a v-if="file.url" :href="file.url" target="_blank" class="rii-action-btn">
                    <i class="fas fa-fw fa-expand"></i>
                </a>
                <button v-if="canDelete" class="rii-action-btn rii-action-remove" @click="$emit('RichImageInputDeleted')">
                    <i class="fas fa-fw fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'canDelete'],
    inject: [
        'helpers'
    ],
    data() {
        return {
            file: {...this.value},
            uuid: null
        };
    },
    watch: {
        file: {
            handler: function(val) {
                this.$emit('fileChanged', {...this.file});
            },
            deep: true
        }
    },
    methods: {
        editUrl() {
            return '/admin/attachments/' + this.file.id + '/edit';
        },
        imageBoxClick(event) {
            let wraper = this.helpers.findParent(event.target, '.rii-wrapper');
            wraper.querySelector('.v-rii-filefile').click();
        },
        fileUploaded(event) {
            this.setFile(event.target.files[0]);
        },
        dropped(e) {
            e.preventDefault();
            this.setFile(e.dataTransfer.items[0].getAsFile());
        },
        setFile(file) {
            let alt = file.name.split('.');
            alt = alt.length==1 ? alt[0] : alt.slice(0, -1).join('.');
            alt = alt.replace(/-/g, ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');

            this.file.id = null;
            this.file.alt = alt;
            this.file.title = alt;
            this.file.file = file;
            this.file.original_name = file.name;
            this.file.url = URL.createObjectURL(file);
        }
    },
    mounted() {
        let _this = this;
        _this.uuid = this.helpers.uuidv4();
        this.$el.querySelector('.v-rii-box').addEventListener('dragover', (e) => e.preventDefault());
        this.$el.querySelector('.v-rii-box').addEventListener('drop', this.dropped);
        document.addEventListener("rii-img-selected", function (e) {
            if (e.detail.uuid != _this.uuid) {
                return;
            }
            let i = e.detail.image;
            _this.file.id = i.id;
            _this.file.alt = i.id;
            _this.file.title = i.title;
            _this.file.file = null;
            _this.file.original_name = i.name;
            _this.file.url = i.url;
        });
    },
    updated() {
        // console.log(`updated`, this.value); //! LOG
    },
    unmounted() {
        // console.log(`unmounted`, this.value); //! LOG
    }
};
</script>
