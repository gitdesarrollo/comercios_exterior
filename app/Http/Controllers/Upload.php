<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\uploadFile;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\DB;

class Upload extends Controller
{

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        
        // dd($request->correlativo);

        if($request->count > 0){

            $uploadId = array();
            if ( $files =  $request->file('file')) {
                foreach ($request->file('file') as $key => $file) {
                    // $name = time() . $key . $file->getClientOriginalName();
                    $name = $request->correlativo . 'temp'. '.'. $file->getClientOriginalExtension();
                    $nameFile = $file->getClientOriginalName();
                    $filename = $file->move('files', $name);
                    $uploadId[] = uploadFile::create([
                        'file' => $name,
                        'file_name' => $request->correlativo,
                        'evento_id' => $request->id_documento])->id;
                }

                $file1 = public_path() . '/files/' . $request->correlativo . '.pdf';
                $file2 = public_path() . '/files/' . $name;
                $newName = $request->correlativo . '.pdf';
                $this->mergePDF($file1,$file2,$newName,$name);
                return response()->json($uploadId,200);
            }

        }else{
            $uploadId = array();
            if ( $files =  $request->file('file')) {
                foreach ($request->file('file') as $key => $file) {
                    $name = $request->correlativo . '.'. $file->getClientOriginalExtension();
                    $nameFile = $file->getClientOriginalName();
                    $filename = $file->move('files', $name);
                    $uploadId[] = uploadFile::create([
                        'file' => $name,
                        'file_name' => $request->correlativo,
                        'evento_id' => $request->id_documento])->id;
                }
            }
            return response()->json($uploadId, 200);
        }
    }


    public function mergePDF($file1,$file2,$correlativo,$name){

        
        $merger = new Merger;

        $documento = [$file1, $file2];

        foreach($documento as $documento){
            $merger->addFile($documento);
        }

        
        $createNewMerger = $merger->merge();

        $newName = public_path() . '/files/' . $correlativo;
        // $newName = $file1;
        

        $bytes = file_put_contents($newName,$createNewMerger);

        if($bytes !== false){

            
                // if (file_exists($file1)) {
                //     unlink($file1);
                //     // uploadFile::where('id', $upload->id)->delete();
                // }

                if (file_exists($file2)) {
                    unlink($file2);
                    uploadFile::where('file', $name)->delete();
                }
            

            return response()->json($bytes,200);
        }

        

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
        return response()->json(null, 200);
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

        try {
            DB::beginTransaction();
                $info = uploadFile::where('file_name',$data->correlativoD)->select('file_name as name')->get();
             
            DB::commit();
            return response()->json($info,200);
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

}
