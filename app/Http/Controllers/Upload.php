<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\uploadFile;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Driver\TcpdiDriver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Upload extends Controller
{

    public function __construct(){
        ini_set('max_execution_time', 3500);
        $this->middleware('auth');
    }

    public function index()
    {
        //
    }

    public function uploadFilesByExist(Request $request){
        
        foreach ($request->file('file') as $key => $file) {
            $extension =  $file->getClientOriginalExtension();
        }
        $path = public_path() . '/files/' . $request->correlativo . '.' . $extension;
        $random = Str::random(7);
        $uploadId = [];
        if (file_exists($path)) {
            if ( $files =  $request->file('file')) {
                foreach ($request->file('file') as $key => $file) {
                    if($file->getClientOriginalExtension() == 'pdf'){
                        $name = $random . '.'. $file->getClientOriginalExtension();
                        $nameFile = $file->getClientOriginalName();
                        $filename = $file->move('files', $name);
                        
                        $file2 = public_path() . '/files/' . $name;
                        $newName = $request->correlativo . '.pdf';
                        $merge = $this->mergePDF($path,$file2,$newName);
                        $merge = json_decode(json_encode($merge));
                        $format = $request->type;

                       
                        if($merge->original != false){
                            array_push($uploadId, 
                            [
                                [
                                    "file"      =>     $newName,
                                    "formato"   =>     $format  
                                ]
                            ]);
    
                          
                            return response()->json($uploadId,200);
                        }else{
                            return response()->json(false, 200);
                        }
                        
                    }else{
                        return response()->json(false, 200);
                    }

                }

                
            }
        }else{
            $uploadId = array();
            if ( $files =  $request->file('file')) {
                foreach ($request->file('file') as $key => $file) {
                    if($file->getClientOriginalExtension() == 'pdf'){
                        $name = $request->correlativo . '.'. $file->getClientOriginalExtension();
                        $nameFile = $file->getClientOriginalName();
                        $filename = $file->move('files', $name);
    
                        $upload = new uploadFile;
                        $upload->file = $name;
                        $upload->evento_id = $request->id_documento;
                        $upload->file_name = $request->correlativo;
                        $upload->formato = $request->type;
                        $upload->save();
                        $file = $upload->file;
                        $format = $upload->formato;
    
                        array_push($uploadId, [
                            [
                                "file"      =>      $file,
                                "format"    =>      $format
                            ]
                        ]);
                    }else{
                        return response()->json(false, 200);
                    }

                }
            }
            return response()->json($uploadId, 200);
        }
    }

    public function store(Request $request)
    {
        
        $bandera = "";
        if($request->count > 0){
            $uploadId = [];
            if ( $files =  $request->file('file')) {
                foreach ($request->file('file') as $key => $file) {
                    if($file->getClientOriginalExtension() == 'pdf'){
                        $name = $request->correlativo . 'temp'. '.'. $file->getClientOriginalExtension();
                        $nameFile = $file->getClientOriginalName();
                        $filename = $file->move('files', $name);
                        $upload = new uploadFile;
                        $upload->file = $name;
                        $upload->evento_id = $request->id_documento;
                        $upload->file_name = $request->correlativo;
                        $upload->formato = $request->type;
                        $upload->save();
                        $file = $upload->file;
                        $format = $upload->formato;
                    }else{
                        return response()->json(false, 200);
                    }

                }

                $file1 = public_path() . '/files/' . $request->correlativo . '.pdf';
                $nameF =  $request->correlativo . '.pdf';
                $file2 = public_path() . '/files/' . $name;
                $newName = $request->correlativo . '.pdf';
                $this->mergePDF($file1,$file2,$newName,$name);
                array_push($uploadId, [
                    [
                        "file"      =>      $nameF,
                        "format"    =>      $format
                    ]
                ]);
                // $bandera =  $id;
                return response()->json($uploadId,200);
            }

        }else{
            $uploadId = array();
            if ( $files =  $request->file('file')) {
                foreach ($request->file('file') as $key => $file) {
                    if($file->getClientOriginalExtension() == 'pdf'){
                        $name = $request->correlativo . '.'. $file->getClientOriginalExtension();
                        $nameFile = $file->getClientOriginalName();
                        $filename = $file->move('files', $name);
    
                        $upload = new uploadFile;
                        $upload->file = $name;
                        $upload->evento_id = $request->id_documento;
                        $upload->file_name = $request->correlativo;
                        $upload->formato = $request->type;
                        $upload->save();
                        $file = $upload->file;
                        $format = $upload->formato;
    
                        array_push($uploadId, [
                            [
                                "file"      =>      $file,
                                "format"    =>      $format
                            ]
                        ]);
                    }else{
                        return response()->json(false, 200);
                    }

                }
            }
            return response()->json($uploadId, 200);
        }
        return response()->json($bandera, 200);
    }

    public function storeWord(Request $request)
    {

            $random = Str::random(7);
            $uploadId = array();
            if ($files = $request->file('file')) {
                
                // dd($files->getClientOriginalExtension() == 'docx');
                foreach ($request->file('file') as $key => $file) {
                    if(($file->getClientOriginalExtension() == 'doc') || ($file->getClientOriginalExtension() == 'docx')){
                        $name = $request->correlativo . '-' . $random . '.'. $file->getClientOriginalExtension();
                        $nameFile = $file->getClientOriginalName();
                        $filename = $file->move('files', $name);
                        $upload = new uploadFile;
                        $upload->file = $name;
                        $upload->evento_id = $request->id_documento;
                        $upload->file_name = $request->correlativo . '-' . $random;
                        $upload->formato = $request->type;
                        $upload->save();
                        $file = $upload->file;
                        $format = $upload->formato;
    
                        array_push($uploadId, [
                            [
                                "file"      =>      $file,
                                "format"    =>      $format
                            ]
                        ]);
                    }else{
                        return response()->json(false, 200);
                    }
                }
            }
            return response()->json($uploadId, 200);
    }


    public function mergePDF($file1,$file2,$correlativo){
    // public function mergePDF($file1,$file2,$correlativo,$name){


        try {
            $merger = new Merger();
            // $merger = new Merger(new TcpdiDriver);
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
                        // uploadFile::where('file', $name)->delete();
                    }
    
                return response()->json($bytes,200);
            }
        } catch (\Throwable $th) {
            return response()->json(false,200);
        }



    }

    public function deleteWord(Request $request){
        // $file2 = public_path() . '/files/' . $name;
        // $path = public_path() . '/files/' . $request->files;
        // dd($request->files);
        if (file_exists($path)) {
            unlink($$request->files);
            uploadFile::where(['id' => $request->id])->update(['estatus' => 5]);
            return response()->json(true,200);
        }else{
            return response()->json(false,200);
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
                $info = uploadFile::where('evento_id',$data->correlativoD)->select('file_name as name')->get();
//                $info = uploadFile::where('file_name',$data->correlativoD)->select('file_name as name')->get();

            DB::commit();
            return response()->json($info,200);
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
    public function getFilesByName(Request $data){

        try {
            DB::beginTransaction();
                // $info = uploadFile::where('file_name',$data->correlativoD)->select('file_name as name')->get();
               $info = uploadFile::where('file_name',$data->correlativoD)->select('file_name as name')->get();

            DB::commit();
            return response()->json($info,200);
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    public function getFileWord(Request $request){
       
        try {
            DB::beginTransaction();
                $data = uploadFile::where(['evento_id' => $request->documento,'formato' => 'word'])->selectRaw('id,file as file, concat("./../files/",file) as url,created_at as fecha')->get();
            DB::commit();
            return response()->json($data,200);
        } catch (\Throwable $th) {
            return response()->json(false,200);
            DB::rollBack();
        }

    }

    public function detalleFile(Request $request){
        try {
            DB::beginTransaction();

            $file = uploadFile::select('upload_files.file as name', 'upload_files.file_name as name_file', 'upload_files.created_at', 'estado_documentos.descripcion as estado')
                ->join('estado_documentos','upload_files.estatus','=','estado_documentos.id')
                ->where(['upload_files.id' => $request->code])
                ->get();

            DB::commit();

            return response()->json($file,200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(false,200);
        }
    }

    public function changeFileByCode(Request $request){

        // foreach ($request->file('file') as $key => $file) {
        //     $extension =  $file->getClientOriginalExtension();
        // }

        return $request;
        // if (file_exists($file2)) {
        //     unlink($file2);
        //     // uploadFile::where('file', $name)->delete();
        // }
    }

}
