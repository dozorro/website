<?php

namespace App\Http\Controllers;
use App\Classes\User;
use Response;
use Dotenv;
use Validator;
use Storage;
use Input;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class HelpersController extends BaseController
{
    public function upload()
    {
        if(User::isNGO() && config('filesystems.disks.localstorage.root'))
        {
            //$folderId=User::ngo()->google_drive_folder;

            //Dotenv::setEnvironmentVariable('GOOGLE_DRIVE_FOLDER_ID', $folderId);

            $input=Input::all();

        	$rules=array(
        	    'file' => 'mimes:pdf,doc,docx,xls,xlsx,jpeg|max:10240',
        	);

            $validation = Validator::make($input, $rules);

        	if ($validation->fails())
        	{
            	$error_label=$validation->errors()->first();

                $comment='Не корректный файл. Разрешены к загрузке файлы pdf, doc, xls, jpeg (не больше 10мб)';

        		return Response::json([
        		    'error'=>$comment
                ], 400);
        	}

            $file=Input::file('file');
            $filename = str_random(32).'.'.$file->getClientOriginalExtension();

            if(Storage::disk('localstorage')->has($filename)) {
                $filename = str_random(33).'.'.$file->getClientOriginalExtension();
            }

	        try{
            	//$upload_success=Storage::disk('google')->put($file->getClientOriginalName(), file_get_contents($file->getRealPath()));
                $upload_success=Storage::disk('localstorage')->put($filename, file_get_contents($file->getRealPath()));
    	    }catch(\Google_Service_Exception $e){
		        return Response::json([
                    'error'=>json_decode($e->getMessage())->error->message
                ], 400);
	        }

            //$fil=Storage::disk('localstorage')->get($file->getClientOriginalName());

            /*
            usort($file_list, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });

            $file=head($file_list);

            unset($file['path']);
            unset($file['type']);
            unset($file['mimetype']);
            unset($file['timestamp']);
            */

            if( $upload_success ) {
            	return Response::json([
            	    'success'=>true,
            	    'file'=>$filename
                ], 200);
            } else {
            	return Response::json('error', 400);
            }
        }

        return Response::json('error', 400);
    }

    public function newDownload($filename)
    {
        try{
            $file=Storage::disk('localstorage')->get($filename);
        }catch(FileNotFoundException $e){
            abort(404);
        }

        //$tmp_filename=storage_path('app').'/'.Input::get('filename');
        //file_put_contents($tmp_filename, $file);
        //$mime=File::mimeType($tmp_filename);

        return Response::make($file, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }

    public function download()
    {
        try{
            $file=Storage::disk('google')->get(Input::get('code'));
        }catch(FileNotFoundException $e){
            abort(404);
        }

        //$tmp_filename=storage_path('app').'/'.Input::get('filename');
        //file_put_contents($tmp_filename, $file);
        //$mime=File::mimeType($tmp_filename);

        return Response::make($file, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.Input::get('filename').'"'
        ]);
    }
}
