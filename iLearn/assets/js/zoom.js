document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".zoomable").forEach(media => {

        media.style.pointerEvents = "auto"; // force clickable

        media.addEventListener("dblclick", () => {

            console.log("Zoom triggered"); // debug

            const overlay = document.createElement("div");
            overlay.className = "zoom-overlay";

            const clone = media.cloneNode(true);
            clone.controls = true;

            const download = document.createElement("a");
            download.className = "zoom-download";
            download.textContent = "⬇ Download";
            download.href = media.src;
            download.download = "";

            overlay.appendChild(clone);
            overlay.appendChild(download);
            document.body.appendChild(overlay);

            overlay.addEventListener("click", e => {
                if (e.target === overlay) overlay.remove();
            });
        });

    });

});
