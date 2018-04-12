import filter from 'belt/core/js/inputs/filter-base';
import shared from 'belt/core/js/teams/inputs/shared';
import html from 'belt/core/js/teams/inputs/filter/template.html';

export default {
    mixins: [filter, shared],
    mounted() {
        this.team_id = this.table.query.team_id;
    },
    watch: {
        'table.query.team_id': function (team_id) {
            if (team_id) {
                this.team_id = team_id;
            }
        }
    },
    methods: {
        change() {
            delete this.table.query.team_id;
            if (this.team_id) {
                this.table.updateQuery({team_id: this.team_id});
            }
            this.$emit('filter-team-update');
        },
    },
    template: html,
}