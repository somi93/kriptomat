import Vue from 'vue';
import Vuetify from 'vuetify/lib/framework';

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        dark: false,
        themes: {
            dark: false,
            light: {
                primary: '#009EFC',
                accent: '#31D2CE',
                secondary: '#333',
                error: '#c62d2d',
                success: '#007e33',
                priority: '#151441'
            }
        },
        options: {
            minifyTheme: function (css) {
                return process.env.NODE_ENV === 'production'
                    ? css.replace(/[\r\n|\r|\n]/g, '')
                    : css
            },
        }
    },
    treeShake: true,
});
