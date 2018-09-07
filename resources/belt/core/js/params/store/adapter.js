import Form from 'belt/core/js/params/form';
import store from 'belt/core/js/params/store';

export default {
    data() {
        return {
            paramsLoading: false,
        }
    },
    created() {
        if (!this.$store.state[this.paramStoreKey]) {
            this.$store.registerModule(this.paramStoreKey, store);
        }
        this.$store.dispatch(this.paramStoreKey + '/set', {entity_type: this.paramable_type, entity_id: this.paramable_id});
        this.paramsLoad();
    },
    computed: {
        params() {
            return this.$store.getters[this.paramStoreKey + '/data'];
        },
        paramConfigs() {
            return this.$store.getters[this.paramStoreKey + '/config'];
        },
        paramStoreKey() {
            return 'params/' + this.paramable_type + this.paramable_id;
        },
    },
    methods: {
        paramsLoad() {
            this.paramsLoading = true;
            this.$store.dispatch(this.paramStoreKey + '/load')
                .then(() => {
                    this.paramsLoading = false;
                });
        },
    }
}