<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\uploadFile;

class Upload extends Controller
{

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        
        // dd($request->id_documento);
        $uploadId = array();
        if ( $files =  $request->file('file')) {
            foreach ($request->file('file') as $key => $file) {
                $name = time() . $key . $file->getClientOriginalName();
                $nameFile = $file->getClientOriginalName();
                $filename = $file->move('files', $name);
                $uploadId[] = uploadFile::create([
                    'file' => $name,
                    'file_name' =>$nameFile,
                    'evento_id' => $request->id_documento])->id;
            }
        }
        return response()->json($uploadId, 200);
    }

    public function update(Request $request, Upload $upload)
    {
        //
    }

    public function show(Upload $upload)
    {
        //
    }

    public function destroy(Upload $upload)
    {
        if (!(empty($upload->file))) {
            if (file_exists(public_path() . '/files/' . $upload->file)) {
                unlink(public_path() . '/files/' . $upload->file);
            }
            uploadFile::where('id', $upload->id)->delete();
        }
        return response()->json(null, 204);
    }


    public function uploadfiles(Request $request){

      
        $modelUpload = uploadFile::find($request->id_file);

        $modelUpload->evento_id = $request->id_evento;
        $modelUpload->file_name = $request->name_file;

        $modelUpload->save();

    }

    public function FileList($id){
        
            $files = DB::table('uploads')
                            ->select('file_name as name','file as url')
                            ->where('evento_id',$id)
                            ->get();
            return response()->json($files, 200);
    }

    public function getNameFiles(Request $data){
        $info = uploadFile::where('file_name',$data->correlativoD)->select('file_name as name')->get();

        return response()->json($info,200);
    }

}
