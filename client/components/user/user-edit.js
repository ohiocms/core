export default {
    components: {
        'form-user': require('./templates/form-user'),
        'form-roles': require('./templates/form-roles')
    },
    data() {
        return {
            userid: this.$route.params.id
        }
    },
    mounted() {
        console.log(this);
    },
    template: `
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit User</h3>
                    </div>
                    <form-user type="edit"></form-user>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Roles</h3>
                    </div>
                    <div class="box-body">
                        <form-roles></form-roles>
                    </div>
                </div>
            </div>
        </div>
    `
}