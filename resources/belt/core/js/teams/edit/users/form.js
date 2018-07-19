import BaseForm from 'belt/core/js/helpers/form';
import BaseService from 'belt/core/js/helpers/service';

class Form extends BaseForm {

    /**
     * Create a new Form instance.
     *
     * @param {object} options
     */
    constructor(options = {}) {
        super(options);

        let baseUrl = `/api/v1/teams/${this.entity_id}/users/`;

        this.service = new BaseService({baseUrl: baseUrl});
        this.routeEditName = 'teams.users';
        this.setData({
            id: '',
        });
    }

}

export default Form;