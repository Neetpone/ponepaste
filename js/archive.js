import { escape, whenReady } from './dom';
import { DataTable, dumbFilterCallback } from './data_tables';
import { tagsToHtml } from "./utils";
import { globalSetup } from './main';

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