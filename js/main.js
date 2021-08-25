import  { TagsInput } from './tag_input';

class Meme {
    constructor() {
        alert('xss');
    }

    meme() {
        console.log('meme');
    }
}

const meme = new Meme();

meme.meme();

new TagsInput(null);