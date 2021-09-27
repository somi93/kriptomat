import userService from "@/services/user";

export default {
    created() {
        this.$store.dispatch('user/getFavorites');
    },
    methods: {
        toggleFavorite(item) {
            const favorite = this.isFavorite(item);
            const params = {
                "user_id": 1,
                "name": item.name,
                "state": favorite ? 2 : 1
            }
            const promise = favorite ? userService.removeFavorite(favorite.id, params) : userService.addFavorite(params);
            promise.then(item => {
                const commit = favorite ? 'user/removeFavorite' : 'user/addFavorite'
                this.$store.commit(commit, item);
            })
        },
        isFavorite(item) {
            return this.favorites.find(favorite => favorite.name === item.name);
        }
    },
    computed: {
        favorites() {
            return this.$store.state.user.favorites;
        },
    }
}