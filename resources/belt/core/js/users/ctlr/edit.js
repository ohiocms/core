import shared from 'belt/core/js/users/ctlr/shared';

// helpers
import Form from 'belt/core/js/users/form';

// templates make a change
import tabs_html from 'belt/core/js/users/templates/tabs.html';
import edit_html from 'belt/core/js/users/templates/edit.html';
import form_html from 'belt/core/js/users/templates/form.html';

export default {
    mixins: [shared],
    data() {
        return {
            form: new Form(),
            morphable_type: 'users',
            morphable_id: this.$route.params.id,
        }
    },
    mounted() {
        this.form.show(this.morphable_id);
    },
    components: {
        tabs: {template: tabs_html},
        edit: {
            data() {
                return {
                    form: this.$parent.form,
                }
            },
            template: form_html,
        },
    },
    template: edit_html,
}