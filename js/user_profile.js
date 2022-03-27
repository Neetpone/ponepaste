import { escape, whenReady } from './dom';
import { DataTable, dumbFilterCallback } from './data_tables';
import { tagsToHtml } from "./utils";
import { globalSetup } from './main';

const getUserInfo = () => {
    const elem = document.getElementById('js-data-holder');

    if (!elem) {
        return { userId: null, csrfToken: null };
    }

    return { userId: elem.dataset.userId, csrfToken: elem.dataset.csrfToken };
};

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
    const myPastesElem = document.getElementById('archive');
    const table = new DataTable(myPastesElem, {
        ajaxCallback: (resolve) => {
            resolve({
                data: Array.prototype.map.call(myPastesElem.querySelectorAll('tbody > tr'), parsePasteInfo)
            });
        },
        rowCallback: (rowData) => {
            const userData = getUserInfo();

            const deleteElem = true ? `<td class="td-center">
                                         <form action="/${rowData.id}" method="POST">
                                            <input type="hidden" name="delete" value="delete" />
                                            <input type="hidden" name="csrf_token" value="${userData.csrfToken}" />
                                            <input type="submit" value="Delete" />
                                         </form>
                                       </td>` : '';
            const pasteCreatedAt = new Date(rowData.created_at).toLocaleString();

            return `<tr>
                        <td><a href="/${rowData.id}">${escape(rowData.title)}</a></td>
                        <td class="td-center">${pasteCreatedAt}</td>
                        <td class="td-center">${rowData.visibility}</td>
                        <td class="td-center">${rowData.views || 0}</td>
                        <td>${tagsToHtml(rowData.tags)}</td>
                        ${deleteElem}
                    </tr>`;
        },
        filterCallback: dumbFilterCallback,
        preFilter: myParam
    });
    table.attach();

    const myFavesElem = document.getElementById('favs');

    if (!myFavesElem) {
        return;
    }

    const faveTable = new DataTable(myFavesElem, {
        ajaxCallback: (resolve) => {
            resolve({
                data: Array.prototype.map.call(myFavesElem.querySelectorAll('tbody > tr'), parsePasteInfo)
            });
        },
        rowCallback: (rowData) => {
            const recentUpdate = rowData.recently_updated ?
                `<i class='far fa-check-square fa-lg' aria-hidden='true'></i>` :
                `<i class='far fa-minus-square fa-lg' aria-hidden='true'></i>`;
            const pasteFavedAt = new Date(rowData.favourited_at).toLocaleString();

            //                         <td><a href="/user/${escape(rowData.author)}">${escape(rowData.author)}</a></td>
            return `<tr>
                        <td><a href="/${rowData.id}">${escape(rowData.title)}</a></td>
                        <td class="td-center">${pasteFavedAt}</td>
                        <td class="td-center">${recentUpdate}</td>
                        <td>${tagsToHtml(rowData.tags)}</td>
                    </tr>`;
        },
        filterCallback: dumbFilterCallback
    });
    faveTable.attach();
});