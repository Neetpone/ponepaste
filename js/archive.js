import { escape, whenReady } from './dom';
import { DataTable } from './data_tables';
import { globalSetup } from './main';

whenReady(() => {
    globalSetup();

    const urlParams = new URLSearchParams(window.location.search);
    const myParam = urlParams.get('q');
    const apiUrl = myParam !== null ? '/api/ajax_pastes.php?q=' + myParam : '/api/ajax_pastes.php';

    const table = new DataTable(document.getElementById('archive'), {
        ajaxCallback: (resolve) => {
            fetch(apiUrl)
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
});