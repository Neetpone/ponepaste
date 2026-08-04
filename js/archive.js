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
                .then(data => {
                    // yes, this is a client-side filter; unlisted pastes are not considered "secure" or "private".
                    // the API returns them in the first place so scrapers can get the data they were gonna get anyway
                    // in a more efficient manner.
                    const publicPastes = data.data.filter(it => it.visibility === 0);

                    resolve({ data: publicPastes });
                });
        },
        rowCallback: (rowData) => {
            return `<tr>
                        <td><a href="/${rowData.id}">${escape(rowData.title)}</a></td>
                        <td><a href="/user/${escape(rowData.author)}">${escape(rowData.author)}</a></td>
                        <td>${escape(rowData.updated_at)}</td>
                        <td>${tagsToHtml(rowData.tags)}</td>
                    </tr>`;
        },
        filterCallback: dumbFilterCallback,
        preFilter: myParam
    });
    table.attach();
});