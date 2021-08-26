import { $$ } from './dom';
import { TagsInput } from "./tag_input";

const setupSite = function() {
    Array.prototype.forEach.call($$('.js-tag-input'), (el) => {
        new TagsInput(el).attach();
    });
};

if (document.readyState !== 'loading') {
    setupSite();
} else {
    document.addEventListener('DOMContentLoaded', setupSite);
}
