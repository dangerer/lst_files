document.addEventListener('DOMContentLoaded', () => {
    // Get ?ttahash=value from current URL
    debugger
    const urlParams = new URLSearchParams(window.location.search);
    const ttahash = urlParams.get('ttahash');

    if (ttahash) {
        // Loop through all <a> tags on the page
        document.querySelectorAll('a[href]').forEach(link => {
            // Replace placeholder if present
            if (link.href.includes('ttahash=###SYS_AUTHCODE###')) {
                link.href = link.href.replace('ttahash=###SYS_AUTHCODE###', 'ttahash=' + encodeURIComponent(ttahash));
            }
        });
    }
});
