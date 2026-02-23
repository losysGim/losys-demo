<?php use Losys\CustomerApi\Client\BackendErrorResponseException;
use Losys\CustomerApi\Client\LosysBackendException;
use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu; require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php'; ?>
<html lang="en">
<head>
    <title>Demo Website</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="content">
    <div class="menu"><?php echo (new Menu())->render(); ?></div>

    <div>
        <h1>PDF-Generation</h1>
        <p>
            this page demonstrates how to generate PDF-files from your
            projects listed at <a href="https://www.referenz-verwaltung.ch">referenz-verwaltung.ch</a>.
        </p><p>
            In this example datasheets of your first 10 projects are listed in the PDF.
        </p>

        <div>
            <?php
            switch ($_REQUEST['job'] ?? '')
            {
                case '':
                    echo '<a href="?job=start">Start generating a new PDF</a>';
                    break;

                case 'start':
                    /*
                     * this starts a new PDF-generation
                     *
                     * you can use the same filter-attributes as in json_listing.php here.
                     * just prefix all field-names with 'filter_', e.g.
                     * 'filter_yearFrom' => 2020
                     * or
                     * 'filter_projectIds' => [4711]
                     * or
                     * 'filter_cantons' => ['ZH']
                     *
                     * additionally you can input PDF_generation parameters:
                     * 'orientation'   can be 'portrait' or 'landscape'
                     * 'printlayout'   can be 'datasheet' or 'datalist'
                     *                 or the name of a company-template
                     *                 hint:
                     *                   use /api/v1/company/{companyId}/pdf_templates
                     *                   to get a list of your custom company-templates
                     * 'mode'          can be 'intern' or 'extern'
                     * 'companycover'  can be the company-ID if you want the
                     *                 PDF to be prefixed with a company-cover-sheet
                     * 'usercover'     can be an employee-ID if you want the
                     *                 PDF to be prefixed with a user-cover-sheet
                     * 'noprojects'    can be '1' if you want the PDF to not
                     *                 contain any projects (mostly useful in
                     *                 combination with 'companycover' or 'usercover')
                     */
                    $client = new LosysClient();

                    try {
                        $data = $client->callApi('api/customer/project/pdf/async', ['filter_limit' => 10, 'printlayout' => 'datasheet']);

                        $message =
                            is_array($data)
                            && array_key_exists('message', $data)
                            && ($msg = $data['message'])
                                ? $msg
                                : (string)$data;

                        if (!($jobId = (
                        is_array($data)
                        && array_key_exists('job_uuid', $data)
                        && ($id = $data['job_uuid'])
                            ? $id
                            : null)))
                            throw new InvalidArgumentException('the API-answer did not contain a Job-Id?!');

                        echo
                            "<p>PDF-generation was started (message from the API: \"{$message}\"). "
                            . "Please wait a few seconds, then click <a href=\"?job=" . rawurlencode($jobId) . "\">Download PDF</a>"
                            . '</p>';
                    } catch (LosysBackendException $e) {
                        echo "<p class='error'>Could not start PDF-generation (probably your input-parameters are invalid): {$e->getMessage()}</p>";
                    }
                    break;

                default:
                    // check if the PDF is already finished
                    $client = new LosysClient();
                    try
                    {
                        $data = $client->callApi("api/customer/project/pdf/{$_REQUEST['job']}/status");

                        if (is_array($data)
                            && array_key_exists('pdf_url', $data)
                            && ($url = $data['pdf_url']))
                        {
                            echo
                                "<p>Click <a href=\"{$url}\" target=\"_blank\">Download</a>"
                                . ' to download your PDF-file</p>';

                        } else {
                            $message =
                                is_array($data)
                                && array_key_exists('message', $data)
                                && ($msg = $data['message'])
                                ? $msg
                                : (string)$data;

                            echo
                                "<p class='error'>PDF-generation is still in progress (\"{$message}\"). "
                                . "Please wait a few seconds, then click <a href=\"?job=" . rawurlencode($_REQUEST['job']) . "\">Retry</a>"
                                . '</p>';
                        }
                    } catch (BackendErrorResponseException $e) {
                        echo "<p class='error'>PDF-generation failed: {$e->getMessage()}</p>";
                    }
                    break;
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
