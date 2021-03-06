﻿//---事件句并------------------------------
function uploadStart(file){
	//$("#list").html("");	
}
function fileQueueError(file, errorCode, message)
{
	try {
		var imageName = "error.gif";
		var errorName = "";
		

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			imageName = "zerobyte.gif";
			break;
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			imageName = "toobig.gif";
			alert(file.name+"文件大小超出限制");
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
		case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
			alert("你添加的文件数量超过了限制！");
			break;
		default:
			alert("出错了，错误代码"+errorCode);
			errorName = "出错了，错误代码"+errorCode;
			break;
		}
		if(errorName!=""){
			return;
		}		

	} catch (ex) {
		this.debug(ex);
	}
}
function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesQueued > 0) {			
			this.startUpload();
		}		
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadProgress(file, bytesLoaded) {

	try {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);

		var progress = new FileProgress(file,  this.customSettings.upload_target);
		progress.setProgress(percent);		
		if (percent === 100) {
			progress.setStatus("文件上传成功...");			
			progress.toggleCancel(false, this);
		} else {
			progress.setStatus("上传中...");
			progress.toggleCancel(true, this);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file,  this.customSettings.upload_target);
		
		if (serverData) {	
			serverData = eval("("+serverData+")");
			if(serverData.ask){
				_loadImage(serverData)
			}else{
				progress.setStatus(serverData.message);
			}	

			progress.setStatus(msg);
			progress.toggleCancel(false);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function _loadImage(serverData) {
	var pa_id = serverData.pa_id;
	var src = serverData.src;
	var imgWrap = $("<div class='imgWrap' title='"+src+"'></div>");		
//	var fancybox = 	$("<a class='fancybox' href='"+serverData.url+"' data-fancybox-group='gallery' ></a>");		
//	var img = $("<img src='"+serverData.thumb+"'/>");		
//	fancybox.append(img);
	var input = $("<input type='hidden' name='pa_id[]' value='"+pa_id+"'>");
	var img = new Image();
	img.src = src;
	var wrapWidth = 140;
	var wrapHeight = 140;
	var marginLeft = 0;
	var marginTop = 0;
	var width_ = height_ = 0;
	img.onload = function () {
		var width  = this.width;
		var height = this.height;

		var  scale_org = wrapWidth/wrapHeight;

		if (wrapWidth / width > wrapHeight / height)
		{
			height_ = wrapHeight;
			width_ = width  * wrapHeight/height;
		} else
		{
			width_ = wrapWidth;
			height_ = height * wrapWidth/width;
		}
		marginLeft = (wrapWidth-width_)/2+1;
		marginTop = (wrapHeight-height_)/2+1;
		//alert(height_);
		img.style.width=width_+"px";
		img.style.height=height_+"px";
		img.style.marginLeft=marginLeft+"px";
		img.style.marginTop=marginTop+"px";					
		imgWrap.append(img);
	};

	img.onerror = function () {
		this.onload = this.onerror = null;
	};

	
	
	imgWrap.append(input);
	
	//alert(imgWrap.html());
	$("#pic_wrapper").append(imgWrap);	
	
}

function uploadComplete(file) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgress(file,  this.customSettings.upload_target);
			progress.setComplete();
			progress.setStatus("所有文件上传完成...");
			progress.toggleCancel(false);
			
			var type = this.settings.post_params.type;
			
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	var imageName =  "error.gif";
	var progress;
	try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Cancelled");
				progress.toggleCancel(false);
			}
			catch (ex1) {
				this.debug(ex1);
			}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("Stopped");
				progress.toggleCancel(true);
			}
			catch (ex2) {
				this.debug(ex2);
			}
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			imageName = "uploadlimit.gif";
			break;
		default:
			alert(message);
			break;
		}
		alert("文件上传错误");

	} catch (ex3) {
		this.debug(ex3);
	}

}



/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(file, targetID) {
	this.fileProgressID = "divFileProgress";

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(file.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(targetID).appendChild(this.fileProgressWrapper);

	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressElement.className = "progressContainer blue";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfuploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfuploadInstance.cancelUpload(fileID);
			return false;
		};
	}
};
