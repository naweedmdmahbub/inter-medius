<template>
    <section>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Product Name</label>
                            <input type="text" v-model="product_name" placeholder="Product Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Product SKU</label>
                            <input type="text" v-model="product_sku" placeholder="Product Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea v-model="description" id="" cols="30" rows="4" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                    </div>
                    <div class="card-body border">
                        <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"></vue-dropzone>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Variants</h6>
                    </div>
                    <div class="card-body">
                        <div class="row" v-for="(product_variant_item,index) in product_variant" :key="index">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Option</label>
                                    <select v-model="product_variant_item.option" class="form-control">
                                        <option v-for="variant in variants"
                                                :value="variant.id"
                                                :key="variant.id">
                                            {{ variant.title }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label v-if="product_variant.length != 1" @click="product_variant.splice(index,1); checkVariant"
                                           class="float-right text-primary"
                                           style="cursor: pointer;">Remove</label>
                                    <label v-else for="">.</label>
                                    <input-tag v-model="product_variant_item.tags" @input="checkVariant" class="form-control"></input-tag>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" v-if="product_variant.length < variants.length && product_variant.length < 3">
                        <button @click="newVariant" class="btn btn-primary">Add another option</button>
                    </div>

                    <div class="card-header text-uppercase">Preview</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>Variant</td>
                                    <td>Price</td>
                                    <td>Stock</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(variant_price, index) in product_variant_prices" :key="index">
                                    <td>{{ variant_price.title }}</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="variant_price.price">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="variant_price.stock">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button @click="saveProduct" type="submit" v-if="mode === 'create'" class="btn btn-lg btn-primary">Save</button>
        <button @click="saveProduct" type="submit" v-if="mode === 'edit'" class="btn btn-lg btn-primary">Update</button>
        <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
    </section>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import InputTag from 'vue-input-tag'

export default {
    components: {
        vueDropzone: vue2Dropzone,
        InputTag
    },
    props: {
        variants: {
            type: Array,
            required: true
        },
        mode: {
            required: true
        },
        responseData: {
        }
    },
    data() {
        return {
            product_id: null,
            product_name: '',
            product_sku: '',
            description: '',
            images: [],
            product_variant: [
                {
                    option: this.variants[0].id,
                    tags: []
                }
            ],
            product_variant_prices: [],
            del_variant_prices_ids: [],
            dropzoneOptions: {
                url: 'https://httpbin.org/post',
                thumbnailWidth: 150,
                maxFilesize: 0.5,
                headers: {"My-Awesome-Header": "header value"}
            }
        }
    },
    methods: {
        // it will push a new object into product variant
        newVariant() {
            let all_variants = this.variants.map(el => el.id)
            let selected_variants = this.product_variant.map(el => el.option);
            let available_variants = all_variants.filter(entry1 => !selected_variants.some(entry2 => entry1 == entry2))
            // console.log(available_variants)
            let tags

            this.product_variant.push({
                option: available_variants[0],
                tags: []
            })
            console.log('this.product_variant:', this.product_variant);
        },

        // check the variant and render all the combination
        checkVariant() {
            if(this.mode == 'create'){
                let tags = [];
                this.product_variant_prices = [];
                this.product_variant.filter((item) => {
                    tags.push(item.tags);
                })
                console.log('tags in checkVariant', tags, this);
                this.getCombn(tags).forEach(item => {
                    console.log('tag:', item);
                    this.product_variant_prices.push({
                        title: item,
                        price: 0,
                        stock: 0
                    })
                })
            } else {
                let tags = [];
                this.product_variant.filter((item) => {
                    tags.push(item.tags);
                })
                
                this.del_variant_prices_ids = this.product_variant_prices.map(a => a.id);
                let temp_product_variant_prices = [];
                this.getCombn(tags).forEach(item => {
                    console.log('tag:', item);
                    var pro = this.product_variant_prices.find(p => p.title == item);
                    temp_product_variant_prices.push(pro);
                    let index = this.del_variant_prices_ids.indexOf(pro.id);
                    this.del_variant_prices_ids.splice(index, 1);
                });
                let temp_ids = temp_product_variant_prices.map(a => a.id);
                
                this.product_variant_prices = temp_product_variant_prices;
                console.log('checkVariant:', this.product_variant_prices, tags);
                // console.log('temp_product_variant_prices:', temp_product_variant_prices);
                // console.log('temp_ids:', temp_ids);
                console.log('del_variant_prices_ids:', this.del_variant_prices_ids);
            }
        },

        // combination algorithm
        getCombn(arr, pre) {
            pre = pre || '';
            if (!arr.length) {
                return pre;
            }
            let self = this;
            // console.log('getCombn:', arr, pre,self)
            let ans = arr[0].reduce(function (ans, value) {
                // console.log('ans:', ans);
                return ans.concat(self.getCombn(arr.slice(1), pre + value + '/'));
            }, []);
            return ans;
        },

        // store product into database
        saveProduct() {
            let product = {
                id: this.product_id,
                title: this.product_name,
                sku: this.product_sku,
                description: this.description,
                product_image: this.images,
                product_variant: this.product_variant,
                product_variant_prices: this.product_variant_prices,
                del_variant_prices_ids: this.del_variant_prices_ids
            }

            if(this.mode == 'create'){
                axios.post('/product', product).then(response => {
                            console.log(response.data);
                        }).catch(error => {
                            console.log(error);
                        })
            } else {
                axios.put('/product/'+product.id, product).then(response => {
                            console.log(response.data);
                        }).catch(error => {
                            console.log(error);
                        })
            }

            console.log(product);
        },


        calculateResponse(){
            this.product_name = this.responseData.title;
            this.product_id = this.responseData.id;
            this.product_sku = this.responseData.sku;
            this.description = this.responseData.description;
            this.product_variant = [];
            
            this.variants.forEach(variant_element =>{
                var obj = {
                    option: variant_element.id,
                    tags: []
                }
                this.product_variant.push(obj);
            });
            console.log('product_variant after assigning options:' ,this.product_variant);
            this.responseData.product_variants.forEach(element => {
                var item =  this.product_variant.find( p => p.option == element.variant_id);
                // console.log('element',  element, item);
                item.tags.push(element.variant);
            });
            this.product_variant_prices = this.responseData.product_variant_prices;
            console.log('product_variant_prices in calculateResponse:', this.product_variant_prices);
        }
    },
    async mounted() {
        if(this.mode == 'edit'){
            await this.calculateResponse();
        }
        console.log('Form Component mounted. Mode: ', this.mode);
    }
}
</script>
