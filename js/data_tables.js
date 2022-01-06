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
        const lastPage = Math.floor(totalRecords / perPage); // ish?
        const numPagesToShow = 2;

        /* First and last page the main paginator will show */
        const firstPageShow = (currentPage - firstPage) < numPagesToShow ? firstPage : ((currentPage - numPagesToShow < 0) ? currentPage : currentPage - numPagesToShow);
        const lastPageShow = (firstPageShow + numPagesToShow) > lastPage ? lastPage : (firstPageShow + numPagesToShow + numPagesToShow);

        /* Whether to show the first and last pages in existence at the ends of the paginator */
        const showFirstPage = (Math.abs(firstPage - currentPage)) > (numPagesToShow);
        const showLastPage = (Math.abs(lastPage - currentPage)) > (numPagesToShow);


        const prevButtonDisabled = currentPage === firstPage ? 'disabled' : ''

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

        const nextButtonDisabled = currentPage === lastPage ? 'disabled' : ''
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
        this.unfilteredData = [];

        this.totalRecords = -1;
        this.perPage = 20;
        this.currentPage = 0;

        this.paginator = new SimplePaginator(this.container.querySelector('.paginator'));

        this.filterCallback = options.filterCallback;
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

        this.paginator.attach(this._updatePage.bind(this));
        this._loadEntries();
    }

    /* Load the requested data from the server, and when done, update the DOM. */
    _loadEntries() {
        new Promise(this.ajaxCallback)
            .then(data => {
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

        this._updatePage(0)
        this._updateEntries(data);
    }

    _updateSort(field, direction) {

    }
}

export { DataTable };
