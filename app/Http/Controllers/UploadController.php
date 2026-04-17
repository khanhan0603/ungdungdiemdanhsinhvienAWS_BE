<?php

namespace App\Http\Controllers;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UploadController extends Controller
{
    //Đảm bảo rằng studentId được gửi lên khi gọi hàm này, và tên file sẽ là students/{studentId}.jpg
    public function getMultiUploadUrl(Request $request)
    {
            $request->validate([
            'students' => 'required|array',
            'students.*' => 'required|string'
        ]);

        $s3 = new S3Client([
            'region'  => env('AWS_DEFAULT_REGION'),
            'version' => '2006-03-01',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

        $result = [];

        foreach ($request->students as $studentId) {

            $fileName = $studentId . '.jpg';

            $cmd = $s3->getCommand('PutObject', [
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $fileName,
                'ContentType' => 'image/jpeg'
            ]);

            $presigned = $s3->createPresignedRequest($cmd, '+10 minutes');

            $result[] = [
                'studentId' => $studentId,
                'fileKey'   => $fileName,
                'uploadUrl' => (string) $presigned->getUri()
            ];
        }

        return response()->json($result);
    }

    public function getStudentsWithImageStatus()
    {
        $students = DB::table('sinh_viens')->get();

        $s3 = new S3Client([
            'region'  => env('AWS_DEFAULT_REGION'),
            'version' => '2006-03-01',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

        $bucket = env('AWS_BUCKET');

        foreach ($students as $sv) {

            $fileName = $sv->masv . '.jpg';

            try {
                $s3->headObject([
                    'Bucket' => $bucket,
                    'Key'    => $fileName
                ]);

                $sv->hasImage = true;
            }
            catch (AwsException $e) {
                $sv->hasImage = false;
            }
        }

        return response()->json($students);
    }

}
