<?php
namespace App\Http\Controllers\FileUpload;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App;
use App\Http\Libraries\DropdownMgr;
use Log;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\FileMgr;
use App\Http\Models\Watermark\Watermark;
use App\Http\Models\Watermark\WatermarkConfig;

class FileUploadController extends MyBaseController
{

    public function __construct()
    {
        parent::__construct();
    }
    
    /* default ขนาดไฟล์อัปโหลด จำกัดที่ 2M */
    public function upload(Request $request)
    {
     
        $accountId = $this->getLoginAccountId();
        
        if (empty($accountId)) {
            return $this->returnError("ไม่พบข้อมูลบัญชี");        
        }  

        $file = $request->file('file');
        $sourcePage = $request->input('source_page');
        $categoryId = $request->input('category_id');     
        $watermarkInfo = $this->getWatermarkBySourcePage($sourcePage, $categoryId);
   
        if (empty($file)) {
            return $this->returnError("ไม่พบข้อมูลไฟล์");
        }
        
        //DataHelper::debug($file->getRealPath());
        
        if ($file->getSize() == false) {
            return $this->returnError("ไม่พบขนาดไฟล์");
        }
        
        if ($file->getSize() > 50*1000000) {
            return $this->returnError("ขนาดไฟล์ใหญ่เกินกำหนด (50 Mb)"); 
        }
        

        $result = FileMgr::upload($file, $accountId, $watermarkInfo);
        
        if ($result === false) {
            $error = FileMgr::errorsPlainText();
            $error = (empty($error))? "ไม่สามารถอัปโหลด": $error;
            return $this->returnError( $error );
        }
        
        return $this->returnSuccess($result); 
    }
    
    public function getWatermarkBySourcePage($page, $categoryId) {
        if ($page == "master_product") {
            $watermarkId = WatermarkConfig::getWatermarkIdForCategory($categoryId);            
            return Watermark::getWatermarkInfoForPrint($watermarkId);
        }
        return "";
    }
    
    public function returnSuccess($fileName) {
        die('{"result" : null, "id" : "id" ,"fileName": "'.$fileName.'" }');
    }
    
    public function returnError($error) {
        $error =  addslashes($error);
        die('{"error" : {"code": 102, "message": "'.$error.'" }, "id" : "id"}');
    }
    
    
    public function manageUploadFile(Request $request) {
        $this->data['xxxx'] = "xxxx";
        
        return $this->openView('uploadFile.manageUploadFile', $this->data);
    }
    
    public function pickUploadFile(Request $request) {
        $this->data['xxxx'] = "xxxx";
        
        return $this->openView('uploadFile.pickUploadFile', $this->data);
    }
    
    //==================================================            
    
    /* default ขนาดไฟล์อัปโหลด จำกัดที่ 2M */
    public function view(Request $request)
    {
        $name = $request->input("name");        
        $isThumb = $request->input("thumb");  
        $fast = $request->input("fast");  
        
        $parentDirName = $this->getLoginAccountId();

        $filePath = "";
        
       // $test1 = FileMgr::getPredictThumbFilePath($name);
       // $test2 = FileMgr::getTargetThumbFilePath($name);
       // myDebug($test1." /// ".$test2);
        
        if ($isThumb == "true" && $fast == "true") {
            $filePath = FileMgr::getPredictThumbFilePath($name);
        }
        else if ($isThumb == "true") {
            $filePath = FileMgr::getTargetThumbFilePath($name);
        }
        else {
            $filePath = FileMgr::getTargetFilePath($name, $parentDirName);
            if (empty($filePath)) {
                 $filePath = FileMgr::getTargetFilePath($name, Rdb::$ACCOUNT_SYSADMIN);
            }
        }        

        if ($isThumb && empty($filePath)) {
             $defaultThumb = public_path()."/images/document-icon.png";
             return $this->viewAsImage($defaultThumb);
        }
                
        if (empty($filePath)) {
            return response("ไม่พบข้อมูลไฟล์");
        }
        
        //myDebug($filePath);
        
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $ext = DataHelper::toLowerCase($ext);
        
        if (FileMgr::isImageExt($ext)) {
            return $this->viewAsImage($filePath);
        }
        else if (in_array($ext, array("txt"))) {
            return $this->viewAsTxt($filePath);
        }        
        else if (in_array($ext, array("pdf"))) {
            return $this->viewAsPdf($filePath);            
        }
        else if (in_array($ext, array("mp4"))) {
            return $this->viewAsMp4($filePath);            
        }
        
        return  $this->viewAsImage($filePath);
    }

    private function viewAsImageV2($file) {
        header('Content-Type: image/jpeg');
        @readfile($file);
        exit;
    }
    

    private function viewAsImage($file) {
        $fp = fopen($file, 'rb');
        
        // send the right headers
        header("Content-Type: image/png");
        header("Content-Length: " . filesize($file));
        
        // dump the picture and stop the script
        fpassthru($fp);
        exit;
    }
    
    private function viewAsTxt($file) {
        header('Content-type: text/plain');
        header('Content-Disposition: inline; filename="' . basename($file) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        @readfile($file);
        exit;
    }
    
    private function viewAsPdf($file) {
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($file) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        @readfile($file);
        exit;
    }
    
    private function viewAsMp4($file) {
        header("Content-Type: video/mp4");
        header('Content-Length: ' . filesize($file));
        @readfile($file);
        exit; 
    }

        
    private function viewAsEtc($file) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

}

