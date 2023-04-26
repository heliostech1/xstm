
ImageFileUtil = {
    
    uploadUrl: '../../fileUpload/upload',    
    viewFileUrl: "../fileUpload/view",
    nonImageSrcPath: "../images/document-icon.png", 
    

    createSimpleUploader: function(config) {
        var containerId = config.containerId;      
        var uploaderName = config.uploaderName;              
        var isDelete = config.delete;   
        var isView = (config.mode === "view")? true: false; 
        
        var finalConfig = { 
            browseId: uploaderName+"_browseId", 
            fileListId: uploaderName+"_fileListId", 
            dropId: uploaderName+"_dropId", 
            valueId: config.valueId,    
            fileType: (config.fileType)? config.fileType: ''              
        };
   
        if (isDelete) {
            finalConfig.deleteId = uploaderName+"_deleteId"; 
        }

        var html = "<div style='width:130px; vertical-align:top; text-align:center;'>";
        html +=  "<div id='" + finalConfig.dropId + "' class='imageThumbnail'></div>";       
        html +=  "<div id='" + finalConfig.fileListId + "' class='imageDescription'></div>"; 
        
        if (!isView) {
            if (isDelete) {
                html +=   "<div><a href='#non' id='" + finalConfig.browseId  + "'  >แนบไฟล์</a> | <a href='#non' id='" + finalConfig.deleteId  + "'  > ลบ </a></div>  "; 
            }
            else {
                html +=   "<div><a href='#non' id='" + finalConfig.browseId  + "'  >แนบไฟล์</a></div>  "; 
            }
        }
        
        html += "</div>";
        
       // html +=  "<div id='" + finalConfig.valueId + "' style='display:none'></div>"; 
        //html += "<div style='clear:both'></div>";
        
        $('#'+ containerId ).append(html);
        
        
        return this.createPlUploader(finalConfig);  
    },

    simpleClear: function (uploader) {
        $('#'+ uploader.myConfig.dropId).html("");
        $('#'+ uploader.myConfig.fileListId).html("");
    },
    
    simpleInsert: function (uploader, imageName) {
       this.simpleClear(uploader);
       var dropId = uploader.myConfig.dropId;
       
       ImageFileUtil.insertThumbImage(dropId, imageName);
    },
    
    
    createPlUploader: function(myConfig) {

        var browseId = myConfig.browseId;
        var fileListId = myConfig.fileListId;
        var dropId = myConfig.dropId;
        var valueId = myConfig.valueId;    
        var deleteId = myConfig.deleteId;
        var fileType = myConfig.fileType;
        
        //console.debug(myConfig);
        
        if (valueId) {
            ImageFileUtil.insertThumbImage(dropId, $("#"+valueId).val(), myConfig);
        }

         var mineTypes = [
                    {title : "Image files", extensions : "jpg,gif,png,jpeg"},
                    {title : "Pdf files", extensions : "pdf" },
                    {title : "txt files", extensions : "txt" }
                ];
                
        
        if (fileType == 'pdf') {
              mineTypes = [
                    {title : "Pdf files", extensions : "pdf" }
               ];
        }
        
        /* http://plupload.com/docs/Uploader */
        var myUploader = new plupload.Uploader({
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : browseId, // you can pass in id...
            drop_element: dropId,
            container: "plUploadContainer", // ... or DOM Element itself
            url : ImageFileUtil.uploadUrl,
            flash_swf_url : '../../js/plupload/Moxie.swf',
            silverlight_xap_url : '../../js/plupload/Moxie.xap',
            multi_selection: false,
            unique_names: true,
            
            filters : {
                max_file_size : '50mb',
                mime_types: mineTypes,
                prevent_duplicates : false
            },  
            
            
            /*
            resize : { // Enable resizng of images on client-side. Applies to image/jpeg and image/png only.
                width : 1000, 
                height : 1000, 
                quality : 100,
                //crop: false // crop to exact dimensions
            },
            */
            uploadStatus: "",
            
            init: {
                PostInit: function() {
                    //alert("OK");
                    document.getElementById( fileListId ).innerHTML = '';
                    
                    /*
                    document.getElementById( selectId ).onclick = function() {
                        myUploader.start();
                        return false;
                    };
                    */
                    
                },

                FilesAdded: function(up, files) {
                    //console.debug("==============")
                    //console.debug(myUploader.files);
                    
                    if (valueId) {
                        $("#"+valueId).val("");
                    }
                    
                    myUploader.uploadStatus = "uploading";
                    
                    var stockFiles = myUploader.files;
                    for ( var i = 0; i < stockFiles.length - 1; i++) {
                        myUploader.removeFile(stockFiles[i]);
                    }
                    
                    for ( var i = 0; i < files.length - 1; i++) {
                        myUploader.removeFile(files[i]);
                    }
                    
                    //console.debug(myUploader.files);
                    
                    myUploader.start();
                    
                    document.getElementById( fileListId ).innerHTML = "";
                    
                    plupload.each(files, function(file) {
                        //document.getElementById( fileListId ).innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                        document.getElementById( fileListId ).innerHTML = '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                        
                       
                    });
                },
                
                FileUploaded:  function(up, file, res) {
                    //console.debug(res);
                    
                    myUploader.uploadStatus = "finish";
                    //setTimeout(function() { myUploader.uploadStatus = "finish"; }, 5000);

                    var json = jQuery.parseJSON(res.response);
                    
                    var error = (json && json.error) ? json.error: null;
                    var fileName = (json && json.fileName)? json.fileName: null;
 
                    if (error) {
                        alert("เกิดความผิดพลาด : " + error.message);
                        return;
                    }                    

                    ImageFileUtil.insertThumbImage(dropId, fileName, myConfig);
                    if (valueId) {
                        $("#"+valueId).val(fileName);
                    }
                    
                },
                
                UploadProgress: function(up, file) {
                    //console.debug("PROGRESS ============");
                   // console.debug(file);
                    //console.debug(up);

                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                },

                Error: function(up, err) {
                    //console.debug(up);                    
                    alert("เกิดความผิดพลาด : " + err.message);
                }
            }
        });

        myUploader.init(); 
        myUploader.myConfig = myConfig;
        
        if (deleteId) {
            $("#"+deleteId).click(function() {    
                var files = myUploader.files;
                var fileSrc = $("#" + valueId ).val();
                
                if (AppUtil.isNotEmpty(fileSrc) ||  files.length > 0 ) {
                   // if (confirm("ยืนยันการลบไฟล์")) {
                        
                        for ( var i = 0; i < files.length ; i++) {
                            myUploader.removeFile(files[i]);
                        }
                        
                        $("#" + fileListId ).html("");
                        $("#" + valueId ).val("");
                        $("#" + dropId ).html("");          
                   // }
                }
                else {
                    //alert("ไม่พบไฟล์ที่ต้องการลบ");
                }
                                           
            });            
        }
        
        return myUploader;
    },
    
    clearImage: function (dropId, fileListId) {
        $('#'+ dropId).html("");
        $('#'+ fileListId).html("");
    },
    
    insertThumbImage: function (id, fileName, myConfig) {
        //console.log("FILE:" + fileName);
        $('#'+ id).html("");
        
        if (AppUtil.isEmpty(fileName)) {
            return;
        }
        
        var imageWidth = (myConfig && myConfig.imageWidth)? myConfig.imageWidth : null;
        var imageHeight = (myConfig && myConfig.imageHeight)? myConfig.imageHeight : null;

        var thumbSrc =  this.viewFileUrl + "?name=" + fileName + "&thumb=true";             
        var fullSrc = this.viewFileUrl + "?name=" + fileName;
        
        
        var isImage = this.isImageFile(fileName);
        var thumb;
        
        if (!isImage) {
            thumbSrc = this.nonImageSrcPath;
        } 
        
        if (imageWidth && imageHeight) {
            var style = "width: auto;  height: auto; max-width:"+imageWidth+"px; max-height:" +imageHeight +"px;";
            
            thumb = "<div  ><a style='text-decoration:none' href='" + fullSrc +"' target='_blank'  >" + 
            "<img class='imageThumbnail_img'  style='"+ style + "'  src='"+ fullSrc + "' />" +       
            "</a></div>";
        }
        else {
            thumb = "<div   ><a style='text-decoration:none' href='" + fullSrc +"' target='_blank'  >" + 
            "<img class='imageThumbnail_img'  src='"+ thumbSrc + "' />" +       
            "</a></div>";
        } 
       
        $('#'+ id).html(thumb); 
        
    },
    
    getImageLink: function(inputString) {
        if (AppUtil.isEmpty(inputString)) {
            return "";
        }
        
        var fileNames = AppUtil.stringToArray(inputString);
        var output = [];
        
        //console.log(fileNames);
        
        for ( var i = 0; i <= fileNames.length - 1; i++) {
            var thumbSrc = ImageFileUtil.viewFileUrl  + "?name=" + fileNames[i] ;            
            var fullSrc = thumbSrc.replace("_thumb", "");
            var  link = "<a href='" + fullSrc + "'   target='_blank'  >เรียกดู</a>";
            output.push(link);
        }

        return AppUtil.arrayToString(output);        
    },
    
    getImageNameLink: function(fileName) {
        if (AppUtil.isEmpty(fileName)) {
            return "";
        }
        
        var thumbSrc = ImageFileUtil.viewFileUrl  + "?name=" + fileName ;            
        var fullSrc = thumbSrc.replace("_thumb", "");
        var  link = "<a href='" + fullSrc + "'   target='_blank'  >" + fileName + "</a>";
        return link;        
    },
    
    isImageFile: function(fileName) {
        if (AppUtil.isEmpty(fileName)) return false;
        
        var fileExt = fileName.split('.').pop();
        var imageExtList = ["jpg","gif","png","jpeg"];
        
        return (imageExtList.indexOf(fileExt) > -1)? true: false;
        
    }
    
};



