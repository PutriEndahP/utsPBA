<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function upload(Request $request)
    {
        $base64_string = $request->input('image');

    	$id_usr = Auth::user()->id;
    	$image_name = 'uploadImage/'.$id_usr;

    	if (!file_exists($image_name)) {
    	 if (!mkdir($image_name)) {
    	    $m=array('msg' => "REJECTED, cant create folder");
    	    echo json_encode($m);
    	    return;}
    	}

    	// $fi = new FilesystemIterator($image_name, FilesystemIterator::SKIP_DOTS);
    	// $fileCount = iterator_count($fi)+1;
    	$files = File::files(public_path($image_name));
        // dd($files);

    	  // $filecount = 1;
    	  
    	  if ($files !== false) {
    	      $filecount = count($files);
    	  } 



        if($filecount==2){
            echo json_encode(array('msg' => "Upload Image Selesai, Data Tersimpan"));
            return;
        }


    	// $data = explode(',', $base64_string);
    	$fullName = $id_usr."_".$filecount."_". date("YmdHis") .".png";
    	// $ifp = fopen(public_path("uploadSignature/".$id_usr), "wb");
    	// fwrite($ifp, base64_decode($data[1]));
    	// fclose($ifp);
    	$ifp = $base64_string->move($image_name,$fullName);
    	if (!$ifp){
    	    $m=array('msg' => "REJECTED, ".$fullName."not saved");
    	    echo json_encode($m);
    	    return;}

    	$command = escapeshellcmd("python ".public_path("code/checkImage.py")." ". public_path("uploadImage/".$id_usr."/".$fullName));
    	$output = shell_exec($command);
        // echo json_encode($output);


    }


	public function uploadv2()
	{
		$base64_string = $_POST['image'];
		$username = $_POST['idUser'];
		$password = $_POST["password"];
		$image_name = "C:\\xampp\\htdocs\\uts\\".$username;

		if (!file_exists($image_name)) {
			if (!mkdir($image_name)) {
				$m = array('msg' => "REJECTED, cant create folder");
				echo json_encode($m);
				return;
			}
		}

		$fi = new FilesystemIterator($image_name, FilesystemIterator::SKIP_DOTS);
		$fileCount = iterator_count($fi)+1;
		$data = explode(',', $base64_string);
		$fullName = $image_name."\\X_".$fileCount."_". date("YmdHis") .".png";
		$ifp = fopen($fullName, "wb");
		fwrite($ifp, base64_decode($data[1]));
		fclose($ifp);
		if (!$ifp) {
			$m = array('masg' => "REJECTED, ".$fullName."not saved" );
			echo json_encode($m);
			return;
		}

		$command = escapeshellcmd("python checkFace.py".$fullName);
		$output = shell_exec($command);

		$fi = new FilesystemIterator($image_name, FilesystemIterator::SKIP_DOTS);
		$fileCount = iterator_count($fi);
		$m = array('msg' => $output."total(".$fileCount.")");
		echo json_encode($m);
	}
}
