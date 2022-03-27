import { escape } from "./dom";

const tagsToHtml = (tags) => {

    return tags.map(tagData => {
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
};

export { tagsToHtml };
