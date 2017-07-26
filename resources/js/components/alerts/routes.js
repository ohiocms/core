import index from 'belt/core/js/components/alerts/ctlr/index';
import create from 'belt/core/js/components/alerts/ctlr/create';
import edit  from 'belt/core/js/components/alerts/ctlr/edit';

export default [
    {path: '/alerts', component: index, canReuse: false, name: 'alerts'},
    {path: '/alerts/create', component: create, name: 'alerts.create'},
    {path: '/alerts/edit/:id', component: edit, name: 'alerts.edit'},
]