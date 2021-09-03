import { makeEl, clearEl } from "./dom";


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
        const lastPage = Math.ceil(totalRecords / perPage); // ish?
        const numPagesToShow = 3;

        /* First and last page the main paginator will show */
        const firstPageShow = (currentPage - numPagesToShow < 0) ? currentPage : currentPage - numPagesToShow;
        const lastPageShow = (firstPageShow + numPagesToShow) > lastPage ? lastPage : (firstPageShow + numPagesToShow * 2);

        /* Whether to show the first and last pages in existence at the ends of the paginator */
        const showFirstPage = (Math.abs(firstPage - currentPage)) > (numPagesToShow * 2);
        const showLastPage = (Math.abs(lastPage - currentPage)) > (numPagesToShow * 2);


        /* Previous button */
        this.element.appendChild(makeEl(
            `<a class="paginator__button previous disabled" data-page="${currentPage - 1}">Previous</a>`
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
            this.element.appendChild(makeEl(
                `<a class="paginator__button" data-page="${i}">${i}</a>`
            ));
        }

        /* Last page button */
        if (showLastPage) {
            this.element.appendChild(makeEl(`<span class="ellipsis">…</span>`));
            this.element.appendChild(makeEl(
                `<a class="paginator__button" data-page="${lastPage}">${lastPage}</a>`
            ));
        }

        /* Next button */
        this.element.appendChild(makeEl(
            `<a class="paginator__button next" data-page="${currentPage + 1}">Next</a>`
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
        this.perPage = 10;
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
}

export { DataTable };
