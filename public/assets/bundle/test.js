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

const clearEl = function(el) {
    while (el.firstChild) {
        el.removeChild(el.firstChild);
    }
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

class SimplePaginator {
    constructor(element) {
        this.element = element;
    }

    attach(pageCallback) {
        this.element.addEventListener('click', evt => {
            if (evt.target && evt.target.classList.contains('paginator__button')) {
                pageCallback(+evt.target.dataset.page);
                evt.preventDefault();
            }
            console.log('clicked', evt.target);
        });

        console.log('attached');
    }

    update(totalRecords, perPage, currentPage) {
        clearEl(this.element);

        /* First and last page in existence */
        const firstPage = 0;
        const lastPage = Math.floor(totalRecords / perPage); // ish?
        const numPagesToShow = 2;

        if (lastPage === firstPage) {
            return;
        }

        /* First and last page the main paginator will show */
        const firstPageShow = (currentPage - numPagesToShow) < firstPage ? firstPage : (currentPage - numPagesToShow);
        const lastPageShow = (currentPage + numPagesToShow) > lastPage ? lastPage : (currentPage + numPagesToShow);

        /* Whether to show the first and last pages in existence at the ends of the paginator */
        const showFirstPage = (Math.abs(firstPage - currentPage)) > (numPagesToShow);
        const showLastPage = (Math.abs(lastPage - currentPage)) > (numPagesToShow);


        const prevButtonDisabled = currentPage === firstPage ? 'disabled' : '';

        /* Previous button */
        this.element.appendChild(makeEl(
            `<button class="paginator__button previous" ${prevButtonDisabled} data-page="${currentPage - 1}">Previous</button>`
        ));

        /* First page button */
        if (showFirstPage) {
            this.element.appendChild(makeEl(
                `<button class="paginator__button" data-page="${firstPage}">${firstPage}</button>`
            ));
            this.element.appendChild(makeEl(`<span class="ellipsis">…</span>`));
        }

        /* "window" buttons */
        for (let i = firstPageShow; i <= lastPageShow; i++) {
            const selected = (i === currentPage ? 'paginator__button--selected' : '');
            this.element.appendChild(makeEl(
                `<button class="paginator__button ${selected}" data-page="${i}">${i}</button>`
            ));
        }

        /* Last page button */
        if (showLastPage) {
            this.element.appendChild(makeEl(`<span class="ellipsis">…</span>`));
            this.element.appendChild(makeEl(
                `<button class="paginator__button" data-page="${lastPage}">${lastPage}</button>`
            ));
        }

        const nextButtonDisabled = currentPage === lastPage ? 'disabled' : '';
        /* Next button */
        this.element.appendChild(makeEl(
            `<button class="paginator__button next" ${nextButtonDisabled} data-page="${currentPage + 1}">Next</button>`
        ));
    }
}

const tagsToHtml = (tags, searchEndpoint = '/archive') => {
    return tags.map(tagData => {
        let tagColorClass;
        const tagLower = tagData.name.toLowerCase();
        if (tagLower === 'nsfw' || tagLower === 'explicit') {
            tagColorClass = 'is-danger';
        } else if (tagLower === 'safe') {
            tagColorClass = 'is-success';
        } else if (tagLower.charAt(0) === '/' && tagLower.charAt(tagLower.length - 1) === '/') {
            tagColorClass = 'is-primary';
        } else {
            tagColorClass = 'is-info';
        }

        return `<a href="${searchEndpoint}?q=${tagData.slug}">
                            <span class="tag ${tagColorClass}">${escape(tagData.name)}</span>
                        </a>`;
    }).join('');
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

class SearchableTable {
    constructor(element, options) {
        this.element = element;
        this.container = element.parentElement;
        this.options = options;

        this.ajaxCallback = options.ajaxCallback;
        this.data = [];

        this.totalRecords = -1;
        this.perPage = 20;
        this.currentPage = 0;

        this.paginator = new SimplePaginator(this.container.querySelector('.paginator'));

        this.sortField = 'created_at';
        this.sortDir = true;
        this.query = options.preFilter || '';

        this.loader = this.element.querySelector('.loading');
    }

    attach() {
        this.filterField = this.container.querySelector('input[type=search]');
        this.container.querySelector('form').addEventListener('submit', evt => {
            evt.preventDefault();
            this._updateFilter(this.filterField.value);
        });

        if (this.filterField) {
            if (this.options.preFilter) {
                this.filterField.value = this.options.preFilter;
            }
        }

        this.perPageField = this.container.querySelector('select[name=per_page]');

        if (this.perPageField) {
            this.perPageField.addEventListener('change', evt => {
                this.perPage = Number(evt.target.value);
                this._updatePage(0);
            });
        }

        const header = this.element.querySelector('tr.paginator__sort');

        if (header) {
            header.addEventListener('click', evt => {
                const target = evt.target;

                if (!target.dataset.sortField) {
                    return;
                }

                if (this.sortField) {
                    const elem = this.element.querySelector(`th[data-sort-field=${this.sortField}]`);
                    elem.classList.remove('paginator__sort--down');
                    elem.classList.remove('paginator__sort--up');
                }

                this._updateSort(target.dataset.sortField, !this.sortDir);

                target.classList.add(this.sortDir ? 'paginator__sort--up' : 'paginator__sort--down');
            });
        }

        this.paginator.attach(this._updatePage.bind(this));
        this._loadEntries();
    }

    /* Load the requested data from the server, and when done, update the DOM. */
    _loadEntries() {
        this._markLoading(true);
        // clearEl(bodyElement);
        // this.paginator.update(0, 0, 0);

        fetch(`/api/search.php?q=${this.query}&page=${this.currentPage}&per_page=${this.perPage}&sf=${this.sortField}&sd=${this.sortDir ? 'desc' : 'asc'}`).then(response => response.json()).then(data => {
            this.element.classList.remove('hidden');
            this.totalRecords = data.total_records;

            const bodyElement = this.element.querySelector('tbody');
            clearEl(bodyElement);

            if (data.error) {
                const notFound = makeEl(`<tr><td colspan="${this.element.querySelectorAll('th').length}"><b>Search error:</b> ${data.error}</td></tr>`);
                bodyElement.appendChild(notFound);
                this._markLoading(false);
                return;
            }

            const numResults = data.pastes.length;
    
            if (numResults === 0) {
                const notFound = makeEl(`<tr><td colspan="${this.element.querySelectorAll('th').length}">No results found</td></tr>`);
                bodyElement.appendChild(notFound);
                this._markLoading(false);
                return;
            }
    
            for (let i = 0; i < numResults; i++) {
                const rowElem = makeEl(this.options.rowCallback(data.pastes[i]));
                rowElem.classList.add(i % 2 === 0 ? 'odd' : 'even');
    
                bodyElement.appendChild(rowElem);
            }

            this.paginator.update(this.totalRecords, this.perPage, this.currentPage);
            this._markLoading(false);
        });
    }

    _updatePage(n) {
        this.currentPage = n;
        // this.paginator.update(this.totalRecords, this.perPage, this.currentPage);
        this._loadEntries();
    }

    _updateFilter(query) {
        this.query = query;
        this._updatePage(0);
    }

    _updateSort(field, direction) {
        this.sortField = field;
        this.sortDir = direction;
        this._updatePage(0);
    }

    _markLoading(loading) {
        if (loading) {
            this.loader.classList.remove('is-hidden');
        } else {
            this.loader.classList.add('is-hidden');
        }
    }
}

whenReady(() => {
    globalSetup();

    const urlParams = new URLSearchParams(window.location.search);
    const myParam = urlParams.get('q');

    const search = new SearchableTable(document.getElementById('search'), {
        rowCallback: (rowData) => {
            return `<tr>
                        <td><a href="/${rowData.id}">${escape(rowData.title)}</a></td>
                        <td><a href="/user/${escape(rowData.author)}">${escape(rowData.author)}</a></td>
                        <td>${escape(rowData.updated_at)}</td>
                        <td>${tagsToHtml(rowData.tags, '/test')}</td>
                    </tr>`;
        },
        preFilter: myParam
    });
    search.attach();
});
