export default {
    props: ['value'],
    computed: {
        model: {
            get() {
                return this.value;
            },
            set(value) {
                return this.$emit('input', value)
            },
            deep: true
        }
    }
}
