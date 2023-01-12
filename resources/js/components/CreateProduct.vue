<template>
    <div>
        <form-component mood="create" :variants="variants" />
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
        }
    },
    methods: {

    },
    mounted() {
        console.log('Component mounted.')
    }
}
</script>
