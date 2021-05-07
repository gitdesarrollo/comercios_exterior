<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\uploadFile;
use App\Model\backups;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Driver\TcpdiDriver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Zipper;

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
                            return response()->json('23', 200);
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
            $path_file_store = public_path() . '/files/';
            $path_file = public_path() . '/files/' . $request->name_file;
            if (file_exists($path_file)) {
                try {
                    unlink($path_file);
                } catch (\Throwable $th) {
                    return response()->json(['error'    =>  true, 'reason'    => 'unlink'],200);
                }
                if ( $files =  $request->file('file')) {
                    foreach ($request->file('file') as $key => $file) {
                        if($file->getClientOriginalExtension() == 'pdf'){
                            $filename = $request->name_file;
                            $file->move($path_file_store, $filename);
                            return response()->json($file,200);
                        }else{
                            return response()->json(false, 200);
                        }
                    }
                }
                else{
                    return response()->json(['error'    =>  true, 'reason'    => 'noFile'], 200);
                }
            }else{
                return response()->json(['error'    =>  true, 'reason'    => 'doesNotExist'], 200);
            }
    }

    public function donwloadFile(Request $request){
        try {
            $path_file = public_path() . '/files/' . $request->file;
            $headers= array(
                'Content-Type: application/pdf',
            );
            return response()->download($path_file, $request->file, $headers);
        } catch (\Throwable $th) {
            return response()->json(['error'    =>  true, 'reason'    => 'unlink'],200);
        }
    }

    public function donwloadFolder(Request $request){
        try {
            $path_file = public_path() . '/files/respaldos/' . $request->file;
            $headers= array(
                'Content-Type: application/pdf',
            );
            return response()->download($path_file, $request->file, $headers);
        } catch (\Throwable $th) {
            return response()->json(['error'    =>  true, 'reason'    => 'unlink'],200);
        }
    }

    public function backupFile(Request $request){
        try {
            $name_folder = str_replace(' ', '_',$request->data[0]['DIRECCION']);
            $random = $name_folder .Str::random(7);
            $path_folder = public_path() . '/files/' .$random . '/';
            mkdir($path_folder, 0777 , true);
            $path_file = public_path() . '/files/';
            $path_file_zip = $path_file . 'respaldos/' . $random . '.zip';
            $user_id = Auth::user()->id;
            try {
                foreach ($request->data as $files => $value) {
                    copy($path_file . $value['NOMBRE_ARCHIVO'],$path_folder . $value['DIRECCION'] . '-' . $value['NOMBRE_ARCHIVO']);
                };
                $directory = glob($path_folder . '*');
                Zipper::make($path_file_zip)->add($directory)->close();
                $backups = new backups;
                $backups->name = $random . '.zip';
                $backups->folder = $path_file_zip;
                $backups->user_id = $user_id;
                $backups->save();
                $this->rmDir_rf($path_folder);
                return response()->json(['error'    =>  false, 'reason'    => 'success'],200);
            } catch (\Throwable $th) {
                $directory = glob($path_folder . '*');
                Zipper::make($path_file_zip)->add($directory)->close();
                $backups = new backups;
                $backups->name = $random . '.zip';
                $backups->folder = $path_file_zip;
                $backups->user_id = $user_id;
                $backups->save();
                $this->rmDir_rf($path_folder);
                return response()->json(['error'    =>  true, 'reason'    => 'failedToOpenStream'],200);
            }
        } catch (\Throwable $th) {
            return response()->json(['error'    =>  true, 'reason'    => 'undefinedOffset'],200);
        }
    }

    public function rmDir_rf($carpeta){
        foreach(glob($carpeta . "/*") as $archivos_carpeta){             
            if (is_dir($archivos_carpeta)){
              rmDir_rf($archivos_carpeta);
            } else {
            unlink($archivos_carpeta);
            }
          }
          rmdir($carpeta);
    }

    public function getBackupFolder(){
        $is_admin = Auth::user()->admin;
        $user_id = Auth::user()->id;

        if($is_admin === 1){
            $folder = backups::select('id as code','name','folder','created_at')
                ->get();
            }else{
                $folder = backups::select('id as code','name','folder','created_at')
                    ->where(['user_id' => $user_id])
                    ->get();
        }

        return response()->json($folder,200);
    }

}
