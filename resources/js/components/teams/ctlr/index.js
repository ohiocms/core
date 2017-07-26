// helpers
import Table from 'belt/core/js/components/teams/table';

// templates make a change
import heading_html from 'belt/core/js/templates/heading.html';
import index_html from 'belt/core/js/components/teams/templates/index.html';

export default {

    components: {
        heading: {template: heading_html},
        index: {
            data() {
                return {
                    table: new Table({router: this.$router}),
                }
            },
            mounted() {
                this.table.updateQueryFromRouter();
                this.table.index();
            },
            template: index_html,
        },
    },

    template: `
        <div>
            <heading>
                <span slot="title">Team Manager</span>
            </heading>
            <section class="content">
                <index></index>
            </section>
        </div>
        `
}