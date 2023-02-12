<template>
    <div>
        <form-component v-if="isMounted" :mode="mode" :variants="variants" :responseData="responseData" />
    </div>
</template>

<script>
import FormComponent from './Form.vue'
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import InputTag from 'vue-input-tag'

export default {
    components: {
        FormComponent
    },
    props: {
        variants: {
            type: Array,
            required: true
        },
        product: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            isMounted: false,
            responseData: null,
            mode: 'edit',
        }
    },
    methods: {},
    async mounted() {
        console.log('product:',this.product);
        await axios.get('/product/'+this.product.id).then(response => {
                        this.responseData = response.data.data;
                        // console.log(response.data);
                        console.log('responseData: ', this.responseData);
                    }).catch(error => {
                        console.log(error);
                    })
        console.log('Edit Component mounted.', this.isMounted);
        this.isMounted = true;
    }
}
</script>
