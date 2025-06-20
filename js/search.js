import { clearEl, makeEl } from "./dom";
import { SimplePaginator } from './data_tables';

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
    }

    attach() {
        this.filterField = this.container.querySelector('input.search');
        if (this.filterField) {
            this.filterField.addEventListener('keyup', evt => {
                if (evt.target && evt.keyCode === 13) { // Enter
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
                    const elem = this.element.querySelector(`th[data-sort-field=${this.sortField}]`)
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
        fetch(`/api/search.php?q=${this.query}&page=${this.currentPage}&per_page=${this.perPage}&sf=${this.sortField}&sd=${this.sortDir ? 'desc' : 'asc'}`).then(response => response.json()).then(data => {
            this.element.classList.remove('hidden');
            this.totalRecords = data.total_records;

            const bodyElement = this.element.querySelector('tbody');
            clearEl(bodyElement);
            const numResults = data.pastes.length;
    
            if (numResults === 0) {
                const notFound = makeEl(`<tr><td colspan="${this.element.querySelectorAll('th').length}">No results found</td></tr>`);
                bodyElement.appendChild(notFound);
                return;
            }
    
            for (let i = 0; i < numResults; i++) {
                const rowElem = makeEl(this.options.rowCallback(data.pastes[i]));
                rowElem.classList.add(i % 2 === 0 ? 'odd' : 'even');
    
                bodyElement.appendChild(rowElem);
            }

            this.paginator.update(this.totalRecords, this.perPage, this.currentPage);
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
};

export { SearchableTable };