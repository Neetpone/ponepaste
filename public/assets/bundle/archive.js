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
            }
        });
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

class DataTable {
    constructor(element, options) {
        this.element = element;
        this.container = element.parentElement;
        this.options = options;

        this.ajaxCallback = options.ajaxCallback;
        this.data = [];
        this.unfilteredData = [];

        this.totalRecords = -1;
        this.perPage = 20;
        this.currentPage = 0;

        this.paginator = new SimplePaginator(this.container.querySelector('.paginator'));

        this.filterCallback = options.filterCallback;
        this.sortField = null;
        this.sortDir = true;
    }

    attach() {
        this.filterField = this.container.querySelector('input.search');
        if (this.filterField && this.filterCallback) {
            this.filterField.addEventListener('keyup', evt => {
               if (evt.target) {
                   this._updateFilter(evt.target.value);
               }
            });

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
        new Promise(this.ajaxCallback)
            .then(data => {
                this.element.classList.remove('hidden');
                this.unfilteredData = data.data;
                this._updateFilter(this.options.preFilter);
            });
    }

    /* Update the DOM to reflect the current state of the data we have loaded */
    _updateEntries(data) {
        this.data = data;
        this.totalRecords = this.data.length;

        const bodyElement = this.element.querySelector('tbody');
        clearEl(bodyElement);

        const firstIndex = (this.perPage * this.currentPage);
        const lastIndex = (firstIndex + this.perPage) > this.totalRecords ? this.totalRecords : (firstIndex + this.perPage);


        for (let i = firstIndex; i < lastIndex; i++) {
            const rowElem = makeEl(this.options.rowCallback(this.data[i]));
            rowElem.classList.add(i % 2 === 0 ? 'odd' : 'even');

            bodyElement.appendChild(rowElem);
        }

        this.paginator.update(this.totalRecords, this.perPage, this.currentPage);
    }

    _updatePage(n) {
        this.currentPage = n;
        this.paginator.update(this.totalRecords, this.perPage, this.currentPage);
        this._updateEntries(this.data);
    }

    _updateFilter(query) {
        /* clearing the query */
        if (query === null || query === '') {
            this._updateEntries(this.unfilteredData);
            return;
        }

        let data = [];
        for (const datum of this.unfilteredData) {
            if (this.filterCallback(datum, query)) {
                data.push(datum);
            }
        }

        this._updatePage(0);
        this._updateEntries(data);
    }

    _updateSort(field, direction) {
        this.sortField = field;
        this.sortDir = direction;

        let newEntries = [...this.data].sort((a, b) => {
            let sorter = 0;

            if (a[field] > b[field]) {
                sorter = 1;
            } else if (a[field] < b[field]) {
                sorter = -1;
            }

            if (!direction) {
                sorter = -sorter;
            }

            return sorter;
        });

        this._updatePage(0);
        this._updateEntries(newEntries);
    }
}

const dumbFilterCallback = (datum, query) => {
    if (!query) {
        return true;
    }

    const queryLower = query.toLowerCase();

    if (queryLower === 'untagged' && datum.tags.length === 0) {
        return true;
    }

    if (datum.title.toLowerCase().indexOf(queryLower) !== -1) {
        return true;
    }

    if (datum.author.toLowerCase().indexOf(queryLower) !== -1) {
        return true;
    }

    /* this is inefficient */
    for (const tag of datum.tags) {
        if (tag.name.toLowerCase().indexOf(queryLower) !== -1) {
            return true;
        }
    }

    return false;
};

const tagsToHtml = (tags) => {
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

        return `<a href="/archive?q=${tagData.slug}">
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

    const preloader = $('.preloader');
    const main = $('main');

    if (preloader && main) {
        preloader.remove();
        main.id = '';
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
};

whenReady(() => {
    globalSetup();

    const urlParams = new URLSearchParams(window.location.search);
    const myParam = urlParams.get('q');
    const apiUrl = /* myParam !== null ? '/api/ajax_pastes.php?q=' + myParam : */ '/api/ajax_pastes.php';

    const table = new DataTable(document.getElementById('archive'), {
        ajaxCallback: (resolve) => {
            fetch(apiUrl)
                .then(r => r.json())
                .then(resolve);
        },
        rowCallback: (rowData) => {
            return `<tr>
                        <td><a href="/${rowData.id}">${escape(rowData.title)}</a></td>
                        <td><a href="/user/${escape(rowData.author)}">${escape(rowData.author)}</a></td>
                        <td>${tagsToHtml(rowData.tags)}</td>
                    </tr>`;
        },
        filterCallback: dumbFilterCallback,
        preFilter: myParam
    });
    table.attach();
});
