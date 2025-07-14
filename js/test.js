import { escape, whenReady } from './dom';
import { DataTable, dumbFilterCallback } from './data_tables';
import { tagsToHtml } from "./utils";
import { globalSetup } from './main';
import { SearchableTable } from './search';

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