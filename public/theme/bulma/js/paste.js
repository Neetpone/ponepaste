function selectText(e) {
    if (document.selection) {
        var t = document.body.createTextRange();
        t.moveToElementText(document.getElementById(e));
        t.select();
    } else if (window.getSelection) {
        var t = document.createRange();
        t.selectNode(document.getElementById(e));
        window.getSelection().addRange(t);
    }
}

function setSelectionRange(e, t, n) {
    if (e.setSelectionRange) {
        e.focus();
        e.setSelectionRange(t, n);
    } else if (e.createTextRange) {
        var r = e.createTextRange();
        r.collapse(true);
        r.moveEnd("character", n);
        r.moveStart("character", t);
        r.select();
    }
}

function replaceSelection(e, t) {
    if (e.setSelectionRange) {
        var n = e.selectionStart;
        var r = e.selectionEnd;
        e.value = e.value.substring(0, n) + t + e.value.substring(r);
        if (n !== r) {
            setSelectionRange(e, n, n + t.length);
        } else {
            setSelectionRange(e, n + t.length, n + t.length);
        }
    } else if (document.selection) {
        var i = document.selection.createRange();
        if (i.parentElement() === e) {
            var s = i.text === "";
            i.text = t;
            if (!s) {
                i.moveStart("character", -t.length);
                i.select();
            }
        }
    }
}

function catchTab(e, t) {
    let c;
    if (navigator.userAgent.match("Gecko")) {
        c = t.which;
    } else {
        c = t.keyCode;
    }
    if (c === 9) {
        const n = e.scrollTop;
        replaceSelection(e, String.fromCharCode(9));
        stopEvent(t);
        e.scrollTop = n;
        return false;
    }
}

function stopEvent(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    e.stopped = true;
}

var js = {
    text: {
        lines: function (e) {
            return this.getLines(e).length;
        },
        getLines: function (e) {
            return e.split("\n");
        },
    },
    textElement: {
        value: function (e) {
            return e.value.replace(/\r/g, "");
        },
        caretPosition: function (e) {
            var t = {};
            if (document.selection) {
                var n = document.selection.createRange();
                var r = document.body.createTextRange();
                r.moveToElementText(e);
                var i;
                for (i = 0; r.compareEndPoints("StartToStart", n) < 0; i++) {
                    r.moveStart("character", 1);
                }
                t.start = i;
                t.end = i + n.text.replace(/\r/g, "").length;
            } else if (e.selectionStart || e.selectionStart === 0) {
                t.start = e.selectionStart;
                t.end = e.selectionEnd;
            }
            return t;
        },
        setCaretPosition: function (e, t) {
            e.focus();
            if (e.setSelectionRange) {
                e.setSelectionRange(t.start, t.end);
            } else if (e.createTextRange) {
                var n = e.createTextRange();
                n.moveStart("character", t.start);
                n.moveEnd("character", t.end);
                n.select();
            }
        },
    },
};

function highlight(e) {
    const t = js.textElement.caretPosition(e);
    if (!t.start && !t.end) return;
    const n = js.text.getLines(js.textElement.value(e));
    let r = 0,
        i = 0;
    let s = "";
    let o = false;
    let u = 0;
    for (const a in n) {
        i = r + n[a].length;
        if (t.start >= r && t.start <= i) o = true;
        if (o) {
            const f = n[a].substr(0, 11) === "!highlight!";
            if (!u) {
                if (f) u = 1;
                else u = 2;
            }
            if (u === 1 && f) n[a] = n[a].substr(11, n[a].length - 11);
            else if (u === 2 && !f) s += "!highlight!";
        }
        s = s + n[a] + "\n";
        if (t.end >= r && t.end <= i) o = false;
        r = i + 1;
    }
    e.value = s.substring(0, s.length - 1);
    const l = t.start + (u === 1 ? -11 : 11);
    js.textElement.setCaretPosition(e, {
        start: l,
        end: l,
    });
}

function togglev() {
    if (
        document.getElementsByTagName("ol")[0].style.listStyle.substr(0, 4) ===
        "none"
    ) {
        document.getElementsByTagName("ol")[0].style.listStyle = "decimal";
    } else {
        document.getElementsByTagName("ol")[0].style.listStyle = "none";
    }
}
