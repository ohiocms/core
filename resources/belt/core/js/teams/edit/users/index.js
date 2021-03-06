import edit from 'belt/core/js/teams/edit/shared';
import TeamForm from 'belt/core/js/teams/form';
import Form from 'belt/core/js/teams/edit/users/form';
import Table from 'belt/core/js/teams/edit/users/table';
import html from 'belt/core/js/teams/edit/users/template.html';

export default {
    mixins: [edit],
    components: {
        edit: {
            props: {
                entity_type: {
                    default: function () {
                        return this.$parent.entity_type;
                    }
                },
                entity_id: {
                    default: function () {
                        return this.$parent.entity_id;
                    }
                },
            },
            data() {
                return {
                    detached: new Table({
                        entity_type: this.entity_type,
                        entity_id: this.entity_id,
                        query: {not: 1},
                    }),
                    table: new Table({
                        entity_type: this.entity_type,
                        entity_id: this.entity_id,
                    }),
                    form: new Form({
                        entity_type: this.entity_type,
                        entity_id: this.entity_id,
                    }),
                    team: new TeamForm(),
                }
            },
            mounted() {
                this.table.index();
                this.team.show(this.$parent.entity_id);
            },
            methods: {
                attach(id) {
                    this.form.setData({id: id});
                    this.form.store()
                        .then(response => {
                            this.table.index();
                            this.detached.index();
                        })
                },
                clear() {
                    this.detached.query.q = '';
                },
            },
            template: html
        },
    },
}