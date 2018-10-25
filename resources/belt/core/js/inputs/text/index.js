import shared from 'belt/core/js/inputs/shared';
import TranslationInputGroup from 'belt/core/js/translations/input/group';
import html from 'belt/core/js/inputs/text/template.html';

export default {
    mixins: [shared],
    components: {
        TranslationInputGroup,
    },
    template: html,
}