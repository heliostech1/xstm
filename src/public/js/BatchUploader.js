
var BatchUploader = function( configs  ) {
   // window[name] = updateFunction;
    this.init(configs);
};

var BatchUpdaterConst = {
    uploadUrl:  "../../fileUpload/upload",
    viewFileUrl:  "../fileUpload/view",
    documentImgUrl: "../images/document-icon.png",
    
    
    getThumbHtml: function(dbFileName) {
        if (AppUtil.isEmpty(dbFileName)) return "";
        
        var thumbSrc =  this.viewFileUrl + "?name=" + dbFileName + "&thumb=true";         
        var fileLink = this.viewFileUrl + "?name=" + dbFileName;
        
        var thumb = "<div><a style='text-decoration:none' href='" + fileLink +"' target='_blank' >" + 
            "<img class='imageThumbnail_img'  src='"+ thumbSrc + "' />" +       
            "</a></div>";     
        return thumb;
    }
}

BatchUploader.prototype = {
        
    index: 1,
    uploaderName : null,
    containerId: null,
    uploader: null,
    mode: "edit",
    uploadStatus: "",
    tags : {},    
    uploadUrl: BatchUpdaterConst.uploadUrl,
    viewFileUrl:  BatchUpdaterConst.viewFileUrl,
    documentImgUrl: BatchUpdaterConst.documentImgUrl,
    enableInfo: false,
    
    init: function(config) {

        this.containerId = config.containerId;      
        this.uploaderName = config.uploaderName;      
        this.mode = (config.mode)? config.mode: this.mode;    
        this.enableInfo = (config.enableInfo)? config.enableInfo: this.enableInfo; 
        this.sourcePage =  (config.sourcePage)? config.sourcePage: ""; 
        this.categoryInputId =  (config.categoryInputId)? config.categoryInputId: "";       
        this.isVdoFile = (config.isVdoFile)? config.isVdoFile: false;  
        
        //this.uploadUrl = config.uploadUrl;
        
        if (this.mode == "edit") {
           this.initalizeUploader();
        }
    },
    
    initalizeUploader: function() {
        
        var html = "";
        var index = this.index;
        var containerId = "uploadContainer_adder_" + this.uploaderName;
        var imageDropId = "uploadImageDrop_adder_" + this.uploaderName;
        var fileNameId = "uploadFileName_adder_" + this.uploaderName;
        var pickFileId = "uploadPickFile_adder_" + this.uploaderName;
        var chooseFileId = "uploadChooseFile_adder_" + this.uploaderName;
        var sourcePage = this.sourcePage;
        var categoryInputId = this.categoryInputId;
        var isVdoFile = this.isVdoFile;
        
        html += "<div style='float:left; padding-right:5px; text-align:center' id='" + containerId + "' >";
        html +=   "<div id='" + imageDropId + "'  class='imageThumbnail'>";  
        html +=     "<div style='font-size:50px;color:#AAA;height:60px' >วาง</div>";       
        html +=     "<div >หรือ</div>";               
        html +=     "<div ><a href='#non' id='" + pickFileId + "'  >เพิ่มไฟล์</a></div>";    //  | <a href='#non' id='" + chooseFileId + "'  >ไฟล์เก่า</a>
        html +=   "</div>";
        html +=   "<div>&nbsp;</div>";          
        html += "</div>";
                                                               
                                
        //html += "<div style='clear:both'></div>";
        
        $('#'+ this.containerId ).append(html);
        
        
         $('#'+ chooseFileId ).click(function() {
             
            // window.open("../uploadFile/pickUploadFile", "pickFileWIndow");
             AppUtil.openResizableWindow("../uploadFile/pickUploadFile", "pickFileWIndow",1000, 520); 

         });
        
        /* http://plupload.com/docs/Uploader */
         var mineTypes = [
                    {title : "Image files", extensions : "jpg,gif,png,jpeg"},
                    {title : "Pdf files", extensions : "pdf" },
                    {title : "txt files", extensions : "txt" }
                ];
        
        if (isVdoFile) {
            mineTypes = [
                      {title : "Vdo files", extensions : "mp4" },
                   ];
        }
        
        
        var self = this;
        this.uploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : pickFileId, // you can pass in id...
            drop_element: imageDropId,
            container: "plUploadContainer", // ... or DOM Element itself
            url : this.uploadUrl,
            flash_swf_url : '../../js/plupload/Moxie.swf',
            silverlight_xap_url : '../../js/plupload/Moxie.xap',
            multi_selection: false,
            unique_names: true,
            
            filters : {
                max_file_size : '50mb',
                mime_types: mineTypes,
                prevent_duplicates : false
            },  
            multipart_params : {
                "category_id" : "" ,                
                "source_page" : sourcePage,
            },  
            /*
            resize : { // Enable resizng of images on client-side. Applies to image/jpeg and image/png only.
                width : 1000, 
                height : 1000, 
                quality : 100,
                //crop: false // crop to exact dimensions
            },
            */
            init: {
                PostInit: function() {
                    //alert("OK");
                },

                FilesAdded: function(up, files) {
                    //console.debug("FilesAdded ==============")
                    //console.debug(files);
                    
                    if ( AppUtil.isNotEmpty(categoryInputId) ) {
                        var categoryId = $('#'+categoryInputId).val();
                       // console.log("CATEGORY INPUT:" + categoryInputId + " ,VALUE:" + categoryId);
                        if (AppUtil.isEmpty(categoryId)) {
                            alert("กรุณาระบุประเภทสินค้าก่อนเพิ่มรูป");
                            return false;
                        }
                        self.uploader.settings.multipart_params["category_id"] = categoryId;
                    }
                    
                    self.uploadStatus = "uploading";
                    
                    //var stockFiles = myUploader.files;
                    //for ( var i = 0; i < stockFiles.length - 1; i++) {
                    //    myUploader.removeFile(stockFiles[i]);
                    //}
                    
                    for ( var i = 0; i < files.length; i++) {
                        self.addItemFromFile(files[i]);
                        //myUploader.removeFile(files[i]);
                    }
                    
                    //console.debug(myUploader.files);
                    
                    self.uploader.start();
                    
                },
                
                FileUploaded:  function(up, file, res) {
                    //console.debug(res);
                    //setTimeout(function() { myUploader.uploadStatus = "finish"; }, 5000);

                    var json = jQuery.parseJSON(res.response);
                    
                    var error = (json && json.error) ? json.error: null;
                    var dbFileName = (json && json.fileName)? json.fileName: null;
 
                    if (error) {
                        alert("เกิดความผิดพลาด : " + error.message);
                        return;
                    }                    

                    self.insertThumbImage(file.id, dbFileName);

                },
                
                UploadComplete:  function(up, files) {
                    self.uploadStatus = "finish";
                },
                
                UploadProgress: function(up, file) {
                    //console.debug("PROGRESS ============");
                   // console.debug(file);
                    //console.debug(up);

                    var fileNameId = "uploadFileName_" + self.uploaderName + file.id;
                    var el = document.getElementById(fileNameId);
                    if (el) {
                       el.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                    }

                },

                Error: function(up, err) {
                    //self.uploadStatus = "error";         
                    //console.debug(err);   
                    var fileName = AppUtil.isNotEmpty(err.file)? " '" + err.file.name + "' ": "";
                    alert("เกิดความผิดพลาด : " + fileName + err.message);
                }
            }
        });

        this.uploader.init(); 
    },
    
    /**
      file => { id:"o_1agrm95uq1rci1rlg1g1a62j1mpnb"
      lastModifiedDate:Fri May 03 2013 13:09:28 GMT+0700 (SE Asia Standard Time)
      loaded:0,name:"hot.jpg",origSize:1037
      percent:0,size:1037,status:1,type:"image/jpeg" }
    */
    
    addItemFromFile: function(file) {
        var fileId = file.id;
        var fileSize = plupload.formatSize(file.size);
        var fileName = file.name;
        var caption = this.getCaptionName(fileName);
        
        this.addItemHtml(fileId, fileSize, fileName, caption, "");
       
    },
    
    addItemHtml: function(fileId, fileSize, fileName, caption, dbFileName, tagDatas) {

        //console.log(fileId);
        
        var html = "";
        var mode = this.mode;
        var shortFileName = this.getShortFileName(fileName);
        
        var uploaderName = this.uploaderName;
        var containerId = "uploadContainer_" + uploaderName + fileId;
        var imageContId = "uploadImageCont_" + uploaderName + fileId;
        var fileNameId = "uploadFileName_" + uploaderName + fileId;
        var deleteFileId = "uploadDeleteFile_" + uploaderName + fileId;
        var dbFileNameId = "uploadDbFileName_" + uploaderName + fileId;
        var captionId = "uploadCaption_" + uploaderName + fileId;
        var tagId = "uploadTag_" + uploaderName + fileId;
        var tagContainerId = "cont_uploadTag_" + uploaderName + fileId;
        var tagClass = "uploadTag";
        
        
        html += "<div style='float:left; padding-right:5px;' class='batchUploaderItem' id='" + containerId + "' >";
        html +=   "<div id='" + imageContId + "'  class='imageThumbnail'></div>";   
        html +=   "<div style='padding-left:4px'>";    
        
        if (mode == "edit") {
          // html +=   "<input type='text' class='batchUploaderCaption imageTextInput' id='" + captionId + "'  value='"+caption+"'/>"; 
        } 
        else {
          // html +=   "<div  class='imageDescription'>"+caption+"</div>"
        }

        if (mode == "edit") {
            var fileSize = (AppUtil.isNotEmpty(fileSize))? "("+fileSize+")": "";
            var fileNameHtml = " <span style='font-size:11px'>" + shortFileName + fileSize + "</span>";
            var sep = (AppUtil.isNotEmpty(fileSize))? " | ":"";
            
            html +=  '<div id="' + fileNameId + '" class="imageDescription" >' + fileNameHtml + ' <b></b> ' + sep;
            html +=  "<a href='#non' id='" + deleteFileId + "'  >ลบ</a>";    
            
            if (this.enableInfo) {
            //    html +=  " | <a href='#non' class='" + tagClass + "' id='" + tagId + "'  >ข้อมูลไฟล์</a>";
            }
            html +=  "</div>";                
        }
        
        html +=   "<input type='hidden' class='batchUploaderFileName' id='" + dbFileNameId + "' value='"+dbFileName+"' />";    
        html +=   "<div id='" + tagContainerId + "' style='padding-top:2px' ></div>";        
        html +=   "</div>";            
        html += "</div>";
                                
        $('#'+this.containerId).append(html);

        var uploader = this.uploader;
        
        $("#"+deleteFileId).click(function() {   
               
           $("#" + containerId).remove();
           uploader.removeFile(fileId);        
               
           //console.debug(uploader.files);
        }); 
        
        if (AppUtil.isNotEmpty(dbFileName)) {
            this.insertThumbImage(fileId, dbFileName);
        }
        
        if (AppUtil.isNotEmpty(tagDatas)) {
            this.setTagDatas(tagId, AppUtil.arrayToString(tagDatas) );
        }
        
    },
        
    getTagDatas : function(id) {
        return this.tags[id];        
    },
    
    setTagDatas: function(id, $datas) {
        this.tags[id] = $datas;        
        this.updateTagDatasHtml(id);
    },
    
    getTagDatasHtml: function(id) {
        var datas = AppUtil.stringToArray( this.getTagDatas(id) );
        var html = "";
        
        for (var i = 0; i < datas.length; i++) {
            html += "<div style='color:#333'>- " + datas[i] + "</div>";
        }
        return html;
    },
    
    updateTagDatasHtml : function(id) {
        var html = this.getTagDatasHtml(id);
        $("#cont_" + id).html(html);
    },
    
    clearAllItem: function() {
        $('#'+this.containerId).children('.batchUploaderItem').each(function () {
            $(this).remove();
        });        
        
        if (this.uploader) {
            var files = this.uploader.files;
            for ( var i = 0; i < files.length; i++) {
                this.uploader.removeFile(files[i]);
            }
        }
        
    },
    
    insertThumbImage: function (fileId, dbFileName) {
        //console.log("FILE ID :"  + fileId + " ,NAME: " + dbFileName);
        
        var id = "uploadImageCont_" + this.uploaderName + fileId;
        var dbFileNameId = "uploadDbFileName_" + this.uploaderName + fileId;
        
        $('#'+ id).html("");
        $('#'+ dbFileNameId).val("");
        
        if (AppUtil.isEmpty(dbFileName)) {
            return;
        }      
        
        var thumbSrc =  this.viewFileUrl + "?name=" + dbFileName + "&thumb=true"; // this.documentImgUrl; // ImageFileUtil.uploadDirPath + dbFileName ;            
        var fileLink = this.viewFileUrl + "?name=" + dbFileName;

        if (!AppUtil.isImageFile(dbFileName)) {
             thumbSrc = this.documentImgUrl;
        }
        
        var thumb = "<div><a style='text-decoration:none' href='" + fileLink +"' target='_blank' >" + 
            "<img class='imageThumbnail_img'  src='"+ thumbSrc + "' />" +       
            "</a></div>";
    
        //--------------------------------
       
        if (AppUtil.isVdoFile(dbFileName) ) {
             var vdoSrc = fileLink + "#t-0.5";
             thumb = "<div><a style='text-decoration:none' href='" + fileLink +"' target='_blank' >" + 
                '<video width="120" height="100"  preload="metadata">' + 
                  '<source src="' + vdoSrc +'" type="video/mp4">' + 
                 ' </video>' + 
             "</a></div>";
        } 


        $('#'+ id).html(thumb); 
        $('#'+ dbFileNameId).val(dbFileName);
    },     
    
    getShortFileName: function(string) {
        //var string = "this is a string";
        var limit = 15;
        var length = string.length;
        if (length > limit) {
            return  string.substring(0, 5) + ".." + string.substring(length-8, length);
        }
        return string;
    },
    
    getCaptionName: function(string) {
        if (AppUtil.isEmpty(string)) return "";
        
        string = string.replace(/\.[^/.]+$/, "");    
        var limit = 25;
        var length = string.length;
        if (length > limit) {
            return  string.substring(0, 22) + ".." ;
        }
        return string;
    },
    
    isUploading: function() {
        if (this.uploadStatus == "uploading") {
            return true;
        }
        return false; 
        
    },
    
    //=======================================================
    
    addDataFromServer: function(fileDatas) { 
        this.clearAllItem();
        
        if (AppUtil.isEmpty(fileDatas)) return;
        
        for (var i=0; i < fileDatas.length; i++) {
           var data = fileDatas[i];
           var name = data['fileName'];
           var caption = data['caption'];
           var tags = data['tags'];           
           var fileId = name.replace(/\./g, "");
           
           this.addItemHtml(fileId, "", "", caption, name, tags);
        }
    },
    
    addDataStringFromServer: function(fileNameString) { 
        this.clearAllItem();
        
        if (AppUtil.isEmpty(fileNameString)) return;
        
        var fileNames = AppUtil.stringToArray(fileNameString);
        
        for (var i=0; i < fileNames.length; i++) {
           var name = fileNames[i].trim();
           var caption = "";
           var tags = "";           
           var fileId = name.replace(/\./g, "");
           
           this.addItemHtml(fileId, "", "", caption, name, tags);
        }
    },
    
    getDataForSubmit: function() {
        var output = [];       
        var self = this;
        
        $('#'+this.containerId).children('.batchUploaderItem').each(function () {
            var fileEl = $( this ).find( ".batchUploaderFileName" );
            var captionEl = $( this ).find( ".batchUploaderCaption" );
            var tagId = $( this ).find( ".uploadTag" ).prop("id");
            var tagDatas = self.getTagDatas(tagId);
            
            if (fileEl.length > 0) {
               var fileName = fileEl[0].value;
               var caption = ( captionEl.length > 0)? captionEl[0].value: "";
               
               var object = {
                  "fileName": fileName,
                  "caption": caption,
                  "tags": AppUtil.arrayToString(tagDatas)  
               }
               output.push( object  );
            }
        });

        //console.debug(output);
        return output;
    } ,

    getDataStringForSubmit: function() {
        var output = [];       

        $('#'+this.containerId).children('.batchUploaderItem').each(function () {
            var fileEl = $( this ).find( ".batchUploaderFileName" );

            if (fileEl.length > 0) {
               var fileName = fileEl[0].value;
               output.push( fileName  );
            }
        });

        //console.debug(output);
        return AppUtil.arrayToString(output);
    } ,
    
    setViewMode: function() {
        this.mode = "view";
        var containerId = "uploadContainer_adder_" + this.uploaderName;
        $('#'+containerId).hide();
    },
    
    
    callbackPickFile: function(file) {
        if (AppUtil.isEmpty(file) || AppUtil.isEmpty(file.name)) return;
        
         //console.log(file);  
        
        var fileSize = plupload.formatSize(file.size);
        var fileName = file.name;
        var fileId = fileName.split('.').join("");
        var caption = "";
        
        var containerId = "uploadContainer_" + this.uploaderName + fileId;

        if ( $("#" + containerId).length == 0) { // new
           this.addItemHtml(fileId, fileSize, fileName, caption, "");
            this.insertThumbImage(fileId, fileName);
        }


      
    },
    
    debug: function() {
    
    }       
};



