const $ = function(selector) {
    return document.querySelector(selector);
};

const $$ = function(selector) {
    return document.querySelectorAll(selector) || [];
};

const makeEl = function(html) {
    const template = document.createElement('template');

    template.innerHTML = html.trim();

    return template.content.firstChild;
};

const toggleEl = function(el) {
    if (el.classList.contains('is-hidden')) {
        el.classList.remove('is-hidden');
    } else {
        el.classList.add('is-hidden');
    }
};

const escape = function(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
};

const whenReady = function(funcp) {
    if (document.readyState !== 'loading') {
        funcp();
    } else {
        document.addEventListener('DOMContentLoaded', funcp);
    }
};

class TagsInput {
    constructor(element, options = {}) {
        this.element = element;
        this.tags = [];
        this.options = options;

        this.maxTags = options.maxTags || 32;
        this.inputNode = null;
        this.containerNode = null;
    }

    attach() {
        this.element.style.display = 'none';

        this.containerNode = makeEl('<div class="tags-input"></div>');
        this.inputNode = makeEl('<input class="input" type="text" placeholder="Type a comma to separate each tag..." value="" />');
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

            this.inputNode.value = '';
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

const decompress = base64string => {
    const bytes = Uint8Array.from(atob(base64string), c => c.charCodeAt(0));
    const cs = new DecompressionStream('gzip');
    const writer = cs.writable.getWriter();
    writer.write(bytes);
    writer.close();
    return new Response(cs.readable).arrayBuffer().then(function (arrayBuffer) {
        return new TextDecoder().decode(arrayBuffer);
    });
};

const setupSignupModal = () => {
    const signupButton = $('[data-target~="#signin"],[data-target~="#signup"]');

    if (signupButton) {
        signupButton.href = 'javascript:void(0)';

        signupButton.addEventListener('click', () => {
            $('.modal').classList.add('is-active');
        });

        $('.modal-button-close').addEventListener('click', () => {
            $('.modal').classList.remove('is-active');
        });
    }
};

const globalSetup = () => {
    Array.prototype.forEach.call($$('.js-tag-input'), (el) => {
        new TagsInput(el).attach();
    });

    setupSignupModal();

    const embedButton = $('.panel-tools .embed-tool');

    if (embedButton){
        embedButton.addEventListener('click', (evt) => {
            if (evt.target && evt.target.closest('.panel-tools')) {
                toggleEl(evt.target.closest('.panel-tools').querySelector('.panel-embed'));
            }
        });
    }

    const expandButton = $('.expand-tool');

    if (expandButton) {
        expandButton.addEventListener('click', (evt) => {
            if (evt.target && evt.target.closest('.panel')) {
                const panel = evt.target.closest('.panel');

                if (panel.classList.contains('panel-fullsize')) {
                    panel.classList.remove('panel-fullsize');
                } else {
                    panel.classList.add('panel-fullsize');
                }
            }
        });
    }

    // Notifications
    (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
        const $notification = $delete.parentNode;

        $delete.addEventListener('click', () => {
            $notification.parentNode.removeChild($notification);
        });
    });

    // Hamburger menu
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
    if ($navbarBurgers.length > 0) {
        $navbarBurgers.forEach(el => {
            el.addEventListener('click', () => {
                const target = el.dataset.target;
                const $target = document.getElementById(target);
                el.classList.toggle('is-active');
                $target.classList.toggle('is-active');
            });
        });
    }

    // CAPTCHA refresh
    const captchaContainer = $('.captcha_container');

    if (captchaContainer) {
        const refreshElement = captchaContainer.querySelector('a');
        const imageElement = captchaContainer.querySelector('img');

        if (refreshElement && imageElement) {
            refreshElement.addEventListener('click', () => {
                let src = imageElement.src;

                if (src.indexOf('&refresh') !== -1) {
                    // yeah, it's kinda cancerous. fuck off.
                    src = src.split('&rand=')[0];
                } else {
                    src += '&refresh';
                }

                imageElement.src = src + '&rand=' + Math.random();
            });
        }
    }

    const hiddenElements = $$('.js-hidden');

    if (hiddenElements) {
        Array.prototype.forEach.call(hiddenElements, (elem) => {
            toggleEl(elem);
        });
    }

    // Used for encoding email to try to avoid spam.
    const encodedElements = $$('[data-encoded-text]');

    if (encodedElements) {
        [...encodedElements].forEach(elem => {
           decompress(elem.dataset.encodedText).then(data => {
               setTimeout(() => elem[elem.dataset.encodedAttr] = data, 1500);
           });
        });
    }
};

whenReady(globalSetup);
