<template>
    <v-dialog
        v-model="dialog"
        :max-width="options.width"
        @keydown.esc="cancel"
        persistent
        v-bind:style="{ zIndex: options.zIndex }">
        <v-card class="confirm-box">
            <v-card-title :class="options.color">
                <span class="white--text subheading font-weight-medium">{{ title }}</span>
            </v-card-title>
            <v-card-text v-show="!!message" v-html="message"></v-card-text>
            <v-divider></v-divider>
            <v-card-actions class="py-0">
                <v-spacer></v-spacer>
                <v-btn color="grey" text @click.native="cancel" v-if="!onlyConfirm">No</v-btn>
                <v-btn color="primary darken-1" text @click.native="agree">{{ onlyConfirm ? "OK" : 'Yes' }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>

export default {
    props: {
        onlyConfirm: {
            type: Boolean,
            required: false,
            default: false
        }
    },
    data: () => ({
        dialog: false,
        resolve: null,
        reject: null,
        message: null,
        title: null,
        options: {
            color: 'error',
            width: 290,
            zIndex: 200
        }
    }),
    methods: {
        open(title, message, options) {
            this.dialog = true;
            this.title = title;
            this.message = message;
            this.options = Object.assign(this.options, options);
            return new Promise((resolve, reject) => {
                this.resolve = resolve;
                this.reject = reject
            })
        },
        agree() {
            this.resolve();
            this.dialog = false
        },
        cancel() {
            this.reject();
            this.dialog = false
        }
    }
}

</script>

<style>
.confirm-box .v-card__text {
    color: #222 !important;
    padding: 16px !important;
}
</style>
