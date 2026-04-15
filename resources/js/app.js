import './bootstrap';
import { bindApiForms, bindApiDeleteForms, hydrateMemberShowPage } from './helpers';

document.addEventListener('DOMContentLoaded', () => {
    bindApiForms();
    bindApiDeleteForms();

    document.querySelectorAll('[data-member-show-page]').forEach((container) => {
        hydrateMemberShowPage(container);
    });
});
