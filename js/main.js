import { $$, escape } from './dom';
import { TagsInput } from "./tag_input";
import { DataTable } from "./data_tables";

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
