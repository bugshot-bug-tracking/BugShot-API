<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    /**
     * Download the desktop client.
     *
     * @return Response
     */
    /**
     * @OA\Get(
     *	path="/downloads/desktop-client",
     *	tags={"Client Download"},
     *	summary="Download the desktop client. (Not Working In Swagger.)",
     *	operationId="downloadClientDesktop",
     *	@OA\Response(
     *		response=200,
     *		description="Success",
     *		@OA\Schema(
     * 			type="string",
     * 			format="binary",
     *		)
     *	),
     *	@OA\Response(
     *		response=400,
     *		description="Bad Request"
     *	),
     *	@OA\Response(
     *		response=404,
     *		description="Not Found"
     *	),
     *)
     *
     **/
    public function downloadDesktopClient()
    {
        // File to fetch from FTP server
        $file_path = 'Bugshot/Installer/BugShotSetup.msi';

        // Fetch the file from the FTP server
        $contents = Storage::disk('ftp')->get($file_path);

        // Distribute the file to the user
        return response($contents)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="BugShotSetup.msi"');
        // return response()->streamDownload(function () use ($contents) {
        //     echo $contents;
        // }, $file_path);
        // return Storage::download($attachment->url, $attachment->designation);
    }
}
