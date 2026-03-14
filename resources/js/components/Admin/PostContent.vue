<template>
    <div class="card" v-if="group_blocks">
        <div class="card-header row">
            <h5 class="m-0 col">Groups for {{ blocks.length }} blocks</h5>
            <div class="col">
                <button type="button" class="btn btn-success d-block float-right" @click="addBlockGroup()">+</button>
                <button type="button" class="btn btn-info d-block float-right mr-2" @click="subBlockGroup()">-</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div>
                            <input 
                                v-for="(blocks_in_group, bgi) in group_blocks" 
                                :key="bgi" 
                                type="number" 
                                class="form-control" 
                                v-model="group_blocks[bgi]" 
                                style="max-width:55px;display:inline-block;margin-right:5px;"
                            >
                        </div>
                        <span data-input="slug" class="input-error"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header row">
            <h5 class="m-0 col">Content Blocks</h5>
            <div class="col">
                <button type="button" class="btn btn-success d-block float-right" @click="addBlock()">Add Block</button>
                <button type="button" class="btn btn-info d-block float-right mr-2" @click="addPreset()">Add Preset</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div v-for="(block, bi) in blocks.sort((a,b) => a.order - b.order)" :key="bi" class="card card-secondary w-100">
                    <div class="card-header">
                        <div class=" row">
                            <div class="col">
                                <span style="font-size:1.5em">{{ bi+1 }}:</span>
                                <div class="tab-content" style="display:inline-block">
                                    <input v-model="block.name" class="form-control my-block-title" @input="blockNameChanged(block)" type="text" placeholder="Block name">
                                </div>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-success d-block mr-2 float-right" @click="addItem(bi)">Add Item</button>
                                <button v-if="blocks.length != 1" type="button" class="btn btn-warning mr-2 d-block float-right" @click="removeBlock(bi)">Remove</button>
                                <button v-if="bi != 0" type="button" class="btn btn-info d-block mr-2 float-right" @click="move(blocks, bi, 'up')">^</button>
                                <button v-if="blocks.length != 1 && blocks.length-1 != bi" type="button" class="btn btn-info d-block mr-2 float-right" @click="move(blocks, bi, 'down')">v</button>
                                <input type="text" class="form-control float-right mr-2 my-block-ident" v-model="block.ident" placeholder="Block anchor" @input="blockIdentChanged(block)">
                            </div>
                        </div>
                    </div>
                    <div class="card-body my-post-block">
                        <div class="row block-item-wrapper">
                            <template v-for="(item, ii) in block.items.sort((a,b) => a.order - b.order)" :key="ii">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="mb-2">
                                            {{ ii+1 }}:
                                            <select v-model="item.type" class="form-control w-auto d-inline item-type-select">
                                                <option v-for="(iType, iTypeKey) in dataprops.itemTypes" :key="iTypeKey" :value="iTypeKey">
                                                    {{iType}}
                                                </option>
                                            </select>
                                            <button v-if="item.type == 'image-gallery'" @click="addImageToSlider(item)" class="btn btn-default ml-2">
                                                Add image
                                            </button>
                                            <button v-if="item.type == 'cards'" @click="addCardToCards(item)" class="btn btn-default ml-2">
                                                Add card
                                            </button>
                                            <button v-if="block.items.length != 1" type="button" class="btn btn-warning remove-item float-right" @click="removeItem(bi, ii)">Remove</button>
                                            <button v-if="ii != 0" type="button" class="btn btn-info d-block mr-2 float-right" @click="move(block.items, ii, 'up')">^</button>
                                            <button v-if="block.items.length != 1 && block.items.length-1 != ii" type="button" class="btn btn-info d-block mr-2 float-right" @click="move(block.items, ii, 'down')">v</button>
                                        </div>
                                        <template v-if="['title-h2','title-h3','title-h4','title-h5'].includes(item.type)">
                                            <input v-model="item.value.value" class="form-control" type="text" placeholder="Title">
                                        </template>
                                        <template v-if="item.type == 'text'">
                                            <div>
                                                <SummernoteEditor v-model="item.value.value"/>
                                            </div>
                                        </template>
                                        <template v-else-if="['image', 'image-small'].includes(item.type)">
                                            <RichImageInput :value="item.value.file" @fileChanged="item.value.file = $event"/>
                                        </template>
                                        <template v-else-if="item.type == 'image-title'">
                                            <input v-model="item.value.title" class="form-control" type="text" placeholder="Title">
                                            <RichImageInput :value="item.value.file" @fileChanged="item.value.file = $event"/>
                                        </template>
                                        <template v-else-if="item.type == 'image-text'">
                                            <div>
                                                <SummernoteEditor v-model="item.value.text"/>
                                                <RichImageInput :value="item.value.file" @fileChanged="item.value.file = $event"/>
                                            </div>
                                        </template>
                                        <template v-else-if="item.type == 'image-gallery'">
                                            <div class="row">
                                                <div v-for="(image, iii) in item.value.images || []" :key="iii" class="col-6 mb-2">
                                                    <RichImageInput :value="item.value.images[iii]" @fileChanged="item.value.images[iii] = $event" canDelete="1" @RichImageInputDeleted="item.value.images.splice(iii, 1)"/>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else-if="item.type == 'cards'">
                                            <div class="row">
                                                <div v-for="(card, iii) in item.value.cards || []" :key="iii" class="col-6 mb-2">
                                                    <div class="form-group">        
                                                        <RichImageInput :value="item.value.cards[iii].image" @fileChanged="item.value.cards[iii].image = $event" canDelete="1" @RichImageInputDeleted="item.value.cards.splice(iii, 1)"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <input v-model="item.value.cards[iii].title" class="form-control" type="text" placeholder="Title">
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea v-model="item.value.cards[iii].text" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else-if="item.type == 'youtube'">
                                            <div>
                                                <input v-model="item.value.value" class="form-control" type="text" placeholder="<iframe width height src title frameborder allow referrerpolicy allowfullscreen></iframe>">
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <hr>
                            </template>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success d-block float-right" @click="addItem(bi)">Add Item</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-success d-block float-right" @click="addBlock()">Add Block</button>
            <button type="button" class="btn btn-info d-block float-right mr-2" @click="addPreset()">Add Preset</button>
        </div>
    </div>
    <div v-if="saveErrors" style="color:red;margin-bottom:15px">
        <ul v-for="(errors, field) in saveErrors" :key="field">
            <li v-for="(error, i) in errors" :key="i">
                {{ error }}
            </li>
        </ul>
    </div>
    <div class="pb-4">
        <button type="submit" class="btn btn-success min-w-100 mr-2" @click="save()">Save</button>
        <a href="/admin/posts" class="btn btn-outline-secondary text-dark min-w-100 mr-2">Cancel</a>
    </div>
</template>

<script>
export default {
    props: [
        'dataprops'
    ],
    inject: [
        'helpers',
        'alert'
    ],
    components: {

    },
    data: () => ({
        blocks: [],
        group_blocks: [],
        someThingWasChanged: -1,
        saveErrors: null
    }),
    watch: {
        blocks: {
            handler: function(val) {
                this.initLeaveConfirmation(val)
            },
            deep: true
        }
    },
    computed: {

    },
    methods: {
        check() {
            console.log('blocks:', [...this.blocks]);
        },
        readable(value) {
            return value.charAt(0).toUpperCase() + value.slice(1);
        },
        async addPreset() {
            const { value: preset } = await this.alert.fire({
                title: 'Select preset of blocks',
                input: 'select',
                inputOptions: {
                    'title+image+text': 'Title + Image + Text',
                    'title+text': 'Title + Text'
                },
                inputPlaceholder: 'Select a preset',
                showCancelButton: true,
                confirmButtonText: 'Select'
            });

            if (!preset) {
                return;
            }

            let maxOrder = this.getMaxOrder();
            if (preset == 'title+image+text') {
                this.blocks.push({
                    ident: '',
                    order: maxOrder+1,
                    name: '',
                    items: [
                        {
                            type: 'title-h2',
                            order: 1,
                            value: this.getDefaultValue()
                        },
                        {
                            type: 'image',
                            order: 2,
                            value: this.getDefaultValue()
                        },
                        {
                            type: 'text',
                            order: 3,
                            value: this.getDefaultValue()
                        }
                    ]
                });
            } else if (preset == 'title+text') {
                this.blocks.push({
                    ident: '',
                    order: maxOrder+1,
                    name: '',
                    items: [
                        {
                            type: 'title-h2',
                            order: 1,
                            value: this.getDefaultValue()
                        },
                        {
                            type: 'text',
                            order: 3,
                            value: this.getDefaultValue()
                        }
                    ]
                });
            }
        },
        move(elems, i, direction) {
            let next = direction == 'up' ? elems[i-1] : elems[i+1];
            let curr = elems[i];
            if (!next) {
                return;
            }
            let trgOrder = next.order;
            next.order = curr.order;
            curr.order = trgOrder;
            // $('.summernote').summernote();
        },
        addBlock() {
            let maxOrder = this.getMaxOrder();
            this.blocks.push({
                ident: '',
                name: '',
                items: [
                    {
                        type: 'title-h2',
                        order: 1,
                        value: this.getDefaultValue()
                    }
                ],
                order: maxOrder+1
            });
            this.recalculateGroupBlocks();
        },
        removeBlock(i) {
            this.blocks.splice(i, 1);
            this.recalculateGroupBlocks();
        },
        addItem(bi) {
            let maxOrder = this.getMaxOrder(this.blocks[bi].items);
            this.blocks[bi].items.push({
                type: 'title-h2',
                order: maxOrder+1,
                value: this.getDefaultValue()
            });
        },
        removeItem(bi, ii) {
            this.blocks[bi].items.splice(ii, 1);
        },
        save() {
            let app = this;
            this.helpers.showLoading();

            let formData = new FormData();
            this.helpers.objToFormData(formData, {blocks: this.blocks, group_blocks: this.group_blocks});

            axios.post(this.dataprops.submitUrl,
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            )
            .then(response => {
                app.helpers.showToast(response.data.message, response.data.success);
                window.onbeforeunload = null;
                window.location.reload();
            })
            .catch(error => {
                if (error.response.status == 422) {
                    app.helpers.showError('Validation error', error.response.data.message)
                    this.saveErrors = error.response.data.errors;
                } else {
                    app.helpers.showError()
                }
            });
        },
        addImageToSlider(item) {
            let def = {};
            if (!item.value.images) {
                item.value.images = [def];
            } else {
                item.value.images.push(def);
            }
        },
        addCardToCards(item) {
            console.log(`addCardToCards!`); //! LOG
            let def = {
                image: {},
                title: '',
                text: ''
            };
            if (!item.value.cards) {
                console.log(` make cards array`); //! LOG
                item.value.cards = [def];
            } else {
                console.log(` push card to cards array`); //! LOG
                item.value.cards.push(def);
            }
        },
        blockNameChanged(block) {
            if (block.id || block.doNotAutoSlug) {
                // do not autoslug when it is block editing
                return;
            }
            block.ident = this.helpers.slugify(block.name);
        },
        blockIdentChanged(block) {
            block.doNotAutoSlug = true;
        },
        addBlockGroup() {
            if (this.blocks.length < this.group_blocks.length + 1) {
                return;
            }
            this.group_blocks.push(0);
            this.recalculateGroupBlocks();
        },
        subBlockGroup() {
            if (this.group_blocks.length == 1) {
                return;
            }

            this.group_blocks.pop();
            this.recalculateGroupBlocks();
        },
        deleteSubItem(item, subItemIndex, nameOfSubItems) {
            item.value[nameOfSubItems].splice(subItemIndex, 1);
        },

        // helpers

        getDefaultValue() {
            return {
                file: {}
            };
        },
        getMaxOrder(items=null) {
            if (!items) {
                items = this.blocks;
            }

            return items.length
                ? Math.max(...items.map(o => o.order))
                : 0;
        },
        recalculateGroupBlocks() {
            let number = this.blocks.length;
            let length = this.group_blocks.length;

            // Calculate the base value for each element
            let baseValue = Math.floor(number / length);

            // Create an array with the base value
            let result = new Array(length).fill(baseValue);

            // Distribute the remainder among the elements
            let remainder = number % length;
            for (let i = 0; i < remainder; i++) {
                result[i] += 1;
            }

            this.group_blocks = result;
        },
        initLeaveConfirmation(val) {
            this.someThingWasChanged++;

            return;

            if (this.someThingWasChanged == 2) {
                window.onbeforeunload = function() {
                    return 'Are you sure you want to leave?';
                };
            }
        }
    },
    created() {
        this.blocks = this.dataprops.model.blocks;
        if (!this.blocks.length) {
            this.addBlock();
            this.group_blocks = [1];
        } else {
            this.group_blocks = this.dataprops.model.block_groups;
        }

        console.log('dataprops: ', this.dataprops);
    }
}
</script>
