import { escape, whenReady } from './dom';
import { DataTable, dumbFilterCallback } from './data_tables';
import { globalSetup } from './main';

const parsePasteInfo = (elem) => {
    if (!elem.dataset.pasteInfo) {
        return null;
    }

    return JSON.parse(elem.dataset.pasteInfo);
};

whenReady(() => {
    globalSetup();

    const urlParams = new URLSearchParams(window.location.search);
    const myParam = urlParams.get('q');
    const table = new DataTable(document.getElementById('archive'), {
        reverseRowCallback: parsePasteInfo,
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

                return `<a href="/archive?q=${tagData.slug}">
                            <span class="tag ${tagColorClass}">${escape(tagData.name)}</span>
                        </a>`;
            }).join('');

            return `<tr>
                        <td><a href="/${rowData.id}">${escape(rowData.title)}</a></td>
                        <td>${rowData.created_at}</td>
                        <td>${rowData.visibility}</td>
                        <td>${rowData.views || 0}</td>
                        <td>${tags}</td>
                    </tr>`;
        },
        filterCallback: dumbFilterCallback,
        preFilter: myParam
    });
    table.attach();

    const faveTable = new DataTable(document.getElementById('favs'), {
        reverseRowCallback: parsePasteInfo,
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

                return `<a href="/archive?q=${tagData.slug}">
                            <span class="tag ${tagColorClass}">${escape(tagData.name)}</span>
                        </a>`;
            }).join('');

            const recentUpdate = rowData.recently_updated ?
                `<i class='far fa-check-square fa-lg' aria-hidden='true'></i>` :
                `<i class='far fa-minus-square fa-lg' aria-hidden='true'></i>`;

            //                         <td><a href="/user/${escape(rowData.author)}">${escape(rowData.author)}</a></td>
            return `<tr>
                        <td><a href="/${rowData.id}">${escape(rowData.title)}</a></td>
                        <td>${rowData.favourited_at}</td>
                        <td>${recentUpdate}</td>
                        <td>${tags}</td>
                    </tr>`;
        },
        filterCallback: dumbFilterCallback,
        preFilter: myParam
    });
    faveTable.attach();
});