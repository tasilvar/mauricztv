const USE_ONLY_CSS_ZOOM = false;
const TEXT_LAYER_MODE = 0;
const MAX_IMAGE_SIZE = 1024 * 1024;
const CMAP_URL = '';
const CMAP_PACKED = true;

pdfjsLib.GlobalWorkerOptions.workerSrc = pdf_viewer.url_js_pdf_worker;

const DEFAULT_URL = pdf_viewer.url_pdf;
const DEFAULT_SCALE_DELTA = 1.1;
const MIN_SCALE = 0.25;
const MAX_SCALE = 10.0;
const DEFAULT_SCALE_VALUE = "auto";

const PDFViewerApplication = {
    pdfLoadingTask: null,
    pdfDocument: null,
    pdfViewer: null,
    pdfHistory: null,
    pdfLinkService: null,
    eventBus: null,

    open(params) {
        if (this.pdfLoadingTask) {
            return this.close().then(
                function () {
                    return this.open(params);
                }.bind(this)
            );
        }

        const url = params.url;
        const self = this;

        const loadingTask = pdfjsLib.getDocument({
            url,
            maxImageSize: MAX_IMAGE_SIZE,
            cMapUrl: CMAP_URL,
            cMapPacked: CMAP_PACKED,
        });
        this.pdfLoadingTask = loadingTask;

        loadingTask.onProgress = function (progressData) {
            self.progress(progressData.loaded / progressData.total);
        };

        return loadingTask.promise.then(
            function (pdfDocument) {
                self.pdfDocument = pdfDocument;
                self.pdfViewer.setDocument(pdfDocument);
                self.pdfLinkService.setDocument(pdfDocument);
                self.pdfHistory.initialize({
                    fingerprint: pdfDocument.fingerprints[0],
                });

                self.loadingBar.hide();
                self.setTitleUsingMetadata(pdfDocument);
            },
            function (exception) {
                const message = exception && exception.message;
                const l10n = self.l10n;
                let loadingErrorMessage;

                if (exception instanceof pdfjsLib.InvalidPDFException) {
                    loadingErrorMessage = l10n.get(
                        "invalid_file_error",
                        null,
                        "Invalid or corrupted PDF file."
                    );
                } else if (exception instanceof pdfjsLib.MissingPDFException) {
                    // special message for missing PDFs
                    loadingErrorMessage = l10n.get(
                        "missing_file_error",
                        null,
                        "Missing PDF file."
                    );
                } else if (exception instanceof pdfjsLib.UnexpectedResponseException) {
                    loadingErrorMessage = l10n.get(
                        "unexpected_response_error",
                        null,
                        "Unexpected server response."
                    );
                } else {
                    loadingErrorMessage = l10n.get(
                        "loading_error",
                        null,
                        "An error occurred while loading the PDF."
                    );
                }

                loadingErrorMessage.then(function (msg) {
                    self.error(msg, { message });
                });
                self.loadingBar.hide();
            }
        );
    },

    close() {
        const errorWrapper = document.getElementById("errorWrapper");
        errorWrapper.hidden = true;

        if (!this.pdfLoadingTask) {
            return Promise.resolve();
        }

        const promise = this.pdfLoadingTask.destroy();
        this.pdfLoadingTask = null;

        if (this.pdfDocument) {
            this.pdfDocument = null;

            this.pdfViewer.setDocument(null);
            this.pdfLinkService.setDocument(null, null);

            if (this.pdfHistory) {
                this.pdfHistory.reset();
            }
        }

        return promise;
    },

    get loadingBar() {
        const bar = document.getElementById("loadingBar");
        return pdfjsLib.shadow(
            this,
            "loadingBar",
            new pdfjsViewer.ProgressBar(bar)
        );
    },

    setTitleUsingUrl: function pdfViewSetTitleUsingUrl(url) {
        this.url = url;
        let title = pdfjsLib.getFilenameFromUrl(url) || url;
        try {
            title = decodeURIComponent(title);
        } catch (e) {
        }
    },

    setTitleUsingMetadata(pdfDocument) {
        const self = this;
        pdfDocument.getMetadata().then(function (data) {
            const info = data.info,
                metadata = data.metadata;
            self.documentInfo = info;
            self.metadata = metadata;

            let pdfTitle;
            if (metadata && metadata.has("dc:title")) {
                const title = metadata.get("dc:title");
                if (title !== "Untitled") {
                    pdfTitle = title;
                }
            }

            if (!pdfTitle && info && info.Title) {
                pdfTitle = info.Title;
            }
        });
    },

    setTitle: function pdfViewSetTitle(title) {
        document.title = title;
        document.getElementById("title").textContent = title;
    },

    error: function pdfViewError(message, moreInfo) {
        const l10n = this.l10n;
        const moreInfoText = [
            l10n.get(
                "error_version_info",
                { version: pdfjsLib.version || "?", build: pdfjsLib.build || "?" },
                "PDF.js v{{version}} (build: {{build}})"
            ),
        ];

        if (moreInfo) {
            moreInfoText.push(
                l10n.get(
                    "error_message",
                    { message: moreInfo.message },
                    "Message: {{message}}"
                )
            );
            if (moreInfo.stack) {
                moreInfoText.push(
                    l10n.get("error_stack", { stack: moreInfo.stack }, "Stack: {{stack}}")
                );
            } else {
                if (moreInfo.filename) {
                    moreInfoText.push(
                        l10n.get(
                            "error_file",
                            { file: moreInfo.filename },
                            "File: {{file}}"
                        )
                    );
                }
                if (moreInfo.lineNumber) {
                    moreInfoText.push(
                        l10n.get(
                            "error_line",
                            { line: moreInfo.lineNumber },
                            "Line: {{line}}"
                        )
                    );
                }
            }
        }

        const errorWrapper = document.getElementById("errorWrapper");
        errorWrapper.hidden = false;

        const errorMessage = document.getElementById("errorMessage");
        errorMessage.textContent = message;

        const closeButton = document.getElementById("errorClose");
        closeButton.onclick = function () {
            errorWrapper.hidden = true;
        };

        const errorMoreInfo = document.getElementById("errorMoreInfo");
        const moreInfoButton = document.getElementById("errorShowMore");
        const lessInfoButton = document.getElementById("errorShowLess");
        moreInfoButton.onclick = function () {
            errorMoreInfo.hidden = false;
            moreInfoButton.hidden = true;
            lessInfoButton.hidden = false;
            errorMoreInfo.style.height = errorMoreInfo.scrollHeight + "px";
        };
        lessInfoButton.onclick = function () {
            errorMoreInfo.hidden = true;
            moreInfoButton.hidden = false;
            lessInfoButton.hidden = true;
        };
        moreInfoButton.hidden = false;
        lessInfoButton.hidden = true;
        Promise.all(moreInfoText).then(function (parts) {
            errorMoreInfo.value = parts.join("\n");
        });
    },

    progress: function pdfViewProgress(level) {
        const percent = Math.round(level * 100);
        // Updating the bar if value increases.
        if (percent > this.loadingBar.percent || isNaN(percent)) {
            this.loadingBar.percent = percent;
        }
    },

    get pagesCount() {
        return this.pdfDocument.numPages;
    },

    get page() {
        return this.pdfViewer.currentPageNumber;
    },

    set page(val) {
        this.pdfViewer.currentPageNumber = val;
    },

    zoomIn: function pdfViewZoomIn(ticks) {
        let newScale = this.pdfViewer.currentScale;
        do {
            newScale = (newScale * DEFAULT_SCALE_DELTA).toFixed(2);
            newScale = Math.ceil(newScale * 10) / 10;
            newScale = Math.min(MAX_SCALE, newScale);
        } while (--ticks && newScale < MAX_SCALE);
        this.pdfViewer.currentScaleValue = newScale;
    },

    zoomOut: function pdfViewZoomOut(ticks) {
        let newScale = this.pdfViewer.currentScale;
        do {
            newScale = (newScale / DEFAULT_SCALE_DELTA).toFixed(2);
            newScale = Math.floor(newScale * 10) / 10;
            newScale = Math.max(MIN_SCALE, newScale);
        } while (--ticks && newScale > MIN_SCALE);
        this.pdfViewer.currentScaleValue = newScale;
    },

    initUI: function pdfViewInitUI() {
        const eventBus = new pdfjsViewer.EventBus();
        this.eventBus = eventBus;

        const linkService = new pdfjsViewer.PDFLinkService({
            eventBus,
        });
        this.pdfLinkService = linkService;

        this.l10n = pdfjsViewer.NullL10n;

        const container = document.getElementById("viewerContainer");
        const pdfViewer = new pdfjsViewer.PDFViewer({
            container,
            eventBus,
            linkService,
            l10n: this.l10n,
            useOnlyCssZoom: USE_ONLY_CSS_ZOOM,
            textLayerMode: TEXT_LAYER_MODE,
        });
        this.pdfViewer = pdfViewer;
        linkService.setViewer(pdfViewer);

        this.pdfHistory = new pdfjsViewer.PDFHistory({
            eventBus,
            linkService,
        });
        linkService.setHistory(this.pdfHistory);

        document.getElementById("previous").addEventListener("click", function () {
            PDFViewerApplication.page--;
        });

        document.getElementById("next").addEventListener("click", function () {
            PDFViewerApplication.page++;
        });

        document.getElementById("zoomIn").addEventListener("click", function () {
            PDFViewerApplication.zoomIn();
        });

        document.getElementById("zoomOut").addEventListener("click", function () {
            PDFViewerApplication.zoomOut();
        });

        document
            .getElementById("pageNumber")
            .addEventListener("click", function () {
                this.select();
            });

        document
            .getElementById("pageNumber")
            .addEventListener("change", function () {
                PDFViewerApplication.page = this.value | 0;

                if (this.value !== PDFViewerApplication.page.toString()) {
                    this.value = PDFViewerApplication.page;
                }
            });

        eventBus.on("pagesinit", function () {
            pdfViewer.currentScaleValue = DEFAULT_SCALE_VALUE;
        });

        eventBus.on(
            "pagechanging",
            function (evt) {
                const page = evt.pageNumber;
                const numPages = PDFViewerApplication.pagesCount;

                document.getElementById("pageNumber").value = page;
                document.getElementById("previous").disabled = page <= 1;
                document.getElementById("next").disabled = page >= numPages;
            },
            true
        );
    },
};

window.PDFViewerApplication = PDFViewerApplication;

document.addEventListener(
    "DOMContentLoaded",
    function () {
        PDFViewerApplication.initUI();
    },
    true
);

const animationStarted = new Promise(function (resolve) {
    window.requestAnimationFrame(resolve);
});

animationStarted.then(function () {
    PDFViewerApplication.open({
        url: DEFAULT_URL,
    });
});