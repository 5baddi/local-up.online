function calculateTextLength(event, textElementSelector, extraText) {
    let textEl = jQuery(textElementSelector);
    if (typeof textEl === "undefined" || typeof event === "undefined") {
        return;
    }

    textEl.text(`${event.target.value.length || 0}${extraText || ''}`);
}

function debounce(callback, wait) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(function () { callback.apply(this, args); }, wait);
    };
}