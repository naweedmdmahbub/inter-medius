<template>
    <div>
        <form-component :mode="mode" :variants="variants" />
        <!-- <form-component :mode="mode" :variants="variants" :product="product" /> -->
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
        }
    },
    data() {
        return {
            // product: {
            //     id: null,
            //     sku: '',
            //     title: '',
            //     description: '',
            //     product_variants: [{
            //         id: null,
            //         product_id: null,
            //         variant_id: null,
            //         variant: '',
            //     }],
            //     product_variants_prices: [{
            //         id: null,
            //         product_id: null,
            //         product_variant_one: null,
            //         product_variant_two: null,
            //         product_variant_three: null,
            //         price: 0,
            //         stock: 0,
            //     }],
            //     deletedOrderDetailIDs: []
            // },
            // variants: [{
            //     id: null,
            //     title: '',
            //     description: '',
            // }],
            mode: 'create'
        }
    },
    methods: {

    },
    mounted() {
        console.log('Create Component mounted.')
    }
}
</script>
