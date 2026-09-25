<?php
ob_start();
session_start();

require_once 'common/header.php';
require_once 'common/db_method.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$pdfFileName = 'ANANTA PITCH DECK .pdf';
$pdfFilePath = __DIR__ . '/../../assets/' . $pdfFileName;

$pdfExists = file_exists($pdfFilePath);

if (
    isset($_GET['action']) &&
    $_GET['action'] === 'download' &&
    $pdfExists
) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $pdfFileName . '"');
    header('Content-Length: ' . filesize($pdfFilePath));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    readfile($pdfFilePath);
    exit();
}
?>

<style>
    .pitch-deck-page {
        background: #f5f5f5;
        min-height: 100vh;
        padding: 15px;
    }

    .pitch-deck-wrapper {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Only Download Button */
    .download-bar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 15px;
    }

    .download-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 20px;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff !important;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(22, 163, 74, .20);
    }

    .download-btn:hover {
        color: #fff !important;
        text-decoration: none !important;
    }

    /* PDF Pages */
    .pdf-pages {
        width: 100%;
    }

    .pdf-page {
        width: 100%;
        margin: 0 auto 18px;
        background: #fff;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .12);
        overflow: hidden;
    }

    .pdf-page canvas {
        display: block;
        width: 100%;
        height: auto;
    }

    .pdf-loading {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
        font-size: 15px;
    }

    .pdf-error {
        text-align: center;
        padding: 60px 20px;
        color: #dc2626;
        background: #fff;
    }

    /* Desktop */
    @media (min-width: 768px) {
        .pitch-deck-page {
            padding: 20px 25px 40px;
        }

        .pdf-page {
            margin-bottom: 22px;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {
        .pitch-deck-page {
            padding: 8px 5px 25px;
        }

        .download-bar {
            padding: 3px 5px;
            margin-bottom: 10px;
        }

        .download-btn {
            width: 100%;
            padding: 11px 15px;
            font-size: 13px;
        }

        .pdf-page {
            margin-bottom: 10px;
            box-shadow: 0 1px 7px rgba(0, 0, 0, .12);
        }
    }
</style>

<div class="content-wrapper">

    <div class="pitch-deck-page">

        <div class="pitch-deck-wrapper">

            <?php if ($pdfExists): ?>

                <!-- ONLY DOWNLOAD BUTTON -->
                <div class="download-bar">
                    <a
                        href="business_plan.php?action=download"
                        class="download-btn"
                    >
                        <i class="zmdi zmdi-download"></i>
                        Download PDF
                    </a>
                </div>

                <!-- FULL PDF -->
                <div id="pdfPages" class="pdf-pages">

                    <div class="pdf-loading">
                        Loading PDF...
                    </div>

                </div>

            <?php else: ?>

                <div class="pdf-error">
                    PDF file not found.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php if ($pdfExists): ?>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script>

    const pdfUrl = <?php echo json_encode('/assets/' . rawurlencode($pdfFileName)); ?>;

    const pdfPagesContainer = document.getElementById('pdfPages');

    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    async function loadPDF() {

        try {

            const pdf = await pdfjsLib.getDocument(pdfUrl).promise;

            pdfPagesContainer.innerHTML = '';

            for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {

                const page = await pdf.getPage(pageNumber);

                const pageWrapper = document.createElement('div');
                pageWrapper.className = 'pdf-page';

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                pageWrapper.appendChild(canvas);
                pdfPagesContainer.appendChild(pageWrapper);

                /*
                 * Render PDF according to available screen width.
                 * This makes it responsive on both mobile and desktop.
                 */
                const containerWidth =
                    pdfPagesContainer.clientWidth;

                const originalViewport =
                    page.getViewport({ scale: 1 });

                const scale =
                    containerWidth / originalViewport.width;

                const viewport =
                    page.getViewport({ scale: scale });

                const devicePixelRatio =
                    window.devicePixelRatio || 1;

                canvas.width =
                    Math.floor(viewport.width * devicePixelRatio);

                canvas.height =
                    Math.floor(viewport.height * devicePixelRatio);

                canvas.style.width =
                    Math.floor(viewport.width) + 'px';

                canvas.style.height =
                    Math.floor(viewport.height) + 'px';

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport,
                    transform: [
                        devicePixelRatio,
                        0,
                        0,
                        devicePixelRatio,
                        0,
                        0
                    ]
                };

                await page.render(renderContext).promise;
            }

        } catch (error) {

            console.error('PDF loading error:', error);

            pdfPagesContainer.innerHTML = `
                <div class="pdf-error">
                    Unable to load the PDF.
                </div>
            `;
        }
    }

    loadPDF();

</script>

<?php endif; ?>

<?php include 'common/footer.php'; ?>