import { makeEl, escape } from "./dom";

class TagsInput {
    constructor(element, options = {}) {
        this.element = element;
        this.tags = [];
        this.options = options

        this.maxTags = options.maxTags || 32;
        this.inputNode = null;
        this.containerNode = null;
    }

    attach() {
        this.element.style.display = 'none';

        this.containerNode = makeEl('<div class="tags-input"></div>');
        this.inputNode = makeEl('<input class="input" type="text" placeholder="32 tags maximum" value="" />');
        this.containerNode.appendChild(this.inputNode);

        this.element.parentNode.insertBefore(this.containerNode, this.element.nextSibling);

        /* Load existing tags from input */
        if (this.element.value) {
            for (const tag of this.element.value.split(',')) {
                this.addTag(tag);
            }
        }

        /* Handle addition and removal of tags via key-presses */
        this.containerNode.addEventListener('keydown', this._handleInputKeyUp.bind(this));

        /* Handle deletions by clicking the delete button */
        this.containerNode.addEventListener('click', this._handleContainerClick.bind(this));

        /* Handle clicks outside the input node to add the past tag */
        this.inputNode.addEventListener('focusout', (evt) => {
            if (this.inputNode.value) {
                this.addTag(this.inputNode.value);
                this.inputNode.value = "";
                this.updateHiddenInputValue();
            }
        });
    }

    detach() {
        this.tags.clear();
        this.containerNode.remove();
        this.element.style.display = 'inline-block';
    }

    updateHiddenInputValue() {
        this.element.value = this.tags.join(',');
    }

    deleteTagNode(node) {
        this.tags.splice(this.tags.indexOf(node.dataset.value.toLowerCase()), 1);
        node.remove();

        /* Below the limit? Make sure the input is enabled. */
        if (this.tags.length < this.maxTags) {
            this.inputNode.disabled = false;
        }
    }

    addTag(tagValue) {
        tagValue = tagValue.trim();

        /* Tag value is probably not empty and we don't already have the same tag. */
        if (tagValue !== '' && this.tags.indexOf(tagValue.toLowerCase()) === -1) {
            this.tags.push(tagValue.toLowerCase());

            this.inputNode.parentNode.insertBefore(
                makeEl('<span class="tag is-info" data-value="' + escape(tagValue) + '">' + escape(tagValue) + '<span class="delete is-small" /></span>'),
                this.inputNode
            );

            /* Too many tags, disable the input for now. */
            if (this.tags.length >= this.maxTags) {
                this.inputNode.disabled = true;
            }
        }
    }

    _handleInputKeyUp(evt) {
        let tagValue = this.inputNode.value;

        if (evt.key === 'Backspace' && tagValue === '') {
            // Remove the child
            if (this.inputNode.previousSibling) {
                this.deleteTagNode(this.inputNode.previousSibling);

                this.updateHiddenInputValue();
            }
        } else if (evt.key === ',') {
            this.addTag(tagValue);

            this.inputNode.value = ''
            this.updateHiddenInputValue();

            evt.preventDefault();
        } else if (evt.key !== 'Backspace' && tagValue.length > 255) { // This could be improved to check if it would actually result in a new char being typed...
            evt.preventDefault();
        }
    }

    _handleContainerClick(evt) {
        if (evt.target && evt.target.classList.contains('delete')) {
            this.deleteTagNode(evt.target.closest('.tag'));
            this.updateHiddenInputValue();
        }
    }
}

export { TagsInput };
