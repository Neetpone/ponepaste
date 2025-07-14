import { escape } from "./dom";

const tagsToHtml = (tags, searchEndpoint = '/archive') => {
    return tags.map(tagData => {
        let tagColorClass;
        const tagLower = tagData.name.toLowerCase();
        if (tagLower === 'nsfw' || tagLower === 'explicit') {
            tagColorClass = 'is-danger';
        } else if (tagLower === 'safe') {
            tagColorClass = 'is-success';
        } else if (tagLower.charAt(0) === '/' && tagLower.charAt(tagLower.length - 1) === '/') {
            tagColorClass = 'is-primary';
        } else {
            tagColorClass = 'is-info';
        }

        return `<a href="${searchEndpoint}?q=${tagData.slug}">
                            <span class="tag ${tagColorClass}">${escape(tagData.name)}</span>
                        </a>`;
    }).join('');
};

export { tagsToHtml };
