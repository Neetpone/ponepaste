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

const escape = function(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
};

class TagsInput {
    constructor(element, options = {}) {
        this.element = element;
        this.tags = [];
        this.options = options;

        this.maxTags = options.maxTags || 10;
        this.inputNode = null;
        this.containerNode = null;
    }

    attach() {
        this.element.style.display = 'none';

        this.containerNode = makeEl('<div class="tags-input"></div>');
        this.inputNode = makeEl('<input class="input" type="text" placeholder="10 tags maximum" value="" />');
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
        }
    }

    _handleContainerClick(evt) {
        if (evt.target && evt.target.classList.contains('delete')) {
            this.deleteTagNode(evt.target.closest('.tag'));
            this.updateHiddenInputValue();
        }
    }
}

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

        /* First and last page the main paginator will show */
        const firstPageShow = (currentPage - firstPage) < numPagesToShow ? firstPage : ((currentPage - numPagesToShow < 0) ? currentPage : currentPage - numPagesToShow);
        const lastPageShow = (firstPageShow + numPagesToShow) > lastPage ? lastPage : (firstPageShow + numPagesToShow + numPagesToShow);

        /* Whether to show the first and last pages in existence at the ends of the paginator */
        const showFirstPage = (Math.abs(firstPage - currentPage)) > (numPagesToShow);
        const showLastPage = (Math.abs(lastPage - currentPage)) > (numPagesToShow);


        const prevButtonDisabled = currentPage === firstPage ? 'disabled' : '';

        /* Previous button */
        this.element.appendChild(makeEl(
            `<a class="paginator__button previous ${prevButtonDisabled}" data-page="${currentPage - 1}">Previous</a>`
        ));

        /* First page button */
        if (showFirstPage) {
            this.element.appendChild(makeEl(
                `<a class="paginator__button" data-page="${firstPage}">${firstPage}</a>`
            ));
            this.element.appendChild(makeEl(`<span class="ellipsis">…</span>`));
        }

        /* "window" buttons */
        for (let i = firstPageShow; i <= lastPageShow; i++) {
            const selected = (i === currentPage ? 'paginator__button--selected' : '');
            this.element.appendChild(makeEl(
                `<a class="paginator__button ${selected}" data-page="${i}">${i}</a>`
            ));
        }

        /* Last page button */
        if (showLastPage) {
            this.element.appendChild(makeEl(`<span class="ellipsis">…</span>`));
            this.element.appendChild(makeEl(
                `<a class="paginator__button" data-page="${lastPage}">${lastPage}</a>`
            ));
        }

        const nextButtonDisabled = currentPage === lastPage ? 'disabled' : '';
        /* Next button */
        this.element.appendChild(makeEl(
            `<a class="paginator__button next ${nextButtonDisabled}" data-page="${currentPage + 1}">Next</a>`
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

        this.totalRecords = -1;
        this.perPage = 20;
        this.currentPage = 0;

        this.paginator = new SimplePaginator(this.container.querySelector('.paginator'));

    }

    attach() {
        this.paginator.attach(this._updatePage.bind(this));
        this._loadEntries();
    }

    /* Load the requested data from the server, and when done, update the DOM. */
    _loadEntries() {
        new Promise(this.ajaxCallback)
            .then(this._updateEntries.bind(this));
    }

    /* Update the DOM to reflect the current state of the data we have loaded */
    _updateEntries(data) {
        this.data = data.data;
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
        this._updateEntries({data: this.data});
    }

    _updateSort(field, direction) {

    }
}

const setupSite = function() {
    Array.prototype.forEach.call($$('.js-tag-input'), (el) => {
        new TagsInput(el).attach();
    });

    if (document.querySelector('#archive')) {
        const table = new DataTable(document.querySelector('#archive'), {
            ajaxCallback: (resolve) => {
                fetch('/api/ajax_pastes.php')
                    .then(r => r.json())
                    .then(resolve);
            },
            rowCallback: (rowData) => {
                const tags = rowData.tags.map((tagData) => {
                    let tagColorClass;
                    if (tagData.name.indexOf('nsfw') !== -1) {
                        tagColorClass = 'is-danger';
                    } else if (tagData.name.indexOf('safe') !== -1) {
                        tagColorClass = 'is-success';
                    } else if (tagData.name.indexOf('/') !== -1) {
                        tagColorClass = 'is-primary';
                    } else {
                        tagColorClass = 'is-info';
                    }

                    return `<a href="/tags/${tagData.slug}">
                                <span class="tag ${tagColorClass}">${escape(tagData.name)}</span>
                            </a>`;
                }).join('');

                return `<tr>
                            <td><a href="/${rowData.id}">${escape(rowData.title)}</a></td>
                            <td><a href="/user/${escape(rowData.author)}">${escape(rowData.author)}</a></td>
                            <td>${tags}</td>
                        </tr>`;
            }
        });
        table.attach();
    }
};

if (document.readyState !== 'loading') {
    setupSite();
} else {
    document.addEventListener('DOMContentLoaded', setupSite);
}
