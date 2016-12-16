---
---
<!DOCTYPE html>

<?php
$filename = $_GET["editpath"];
$filepath = "../../".$filename;
$readContent = file_get_contents($filepath);
?>

<html>
{% include head.html %}
<link rel="stylesheet" type="text/css" href="https://cdn.staticfile.org/simplemde/1.11.2/simplemde.min.css">
<script src="https://cdn.staticfile.org/simplemde/1.11.2/simplemde.min.js"></script>
<script src="https://cdn.staticfile.org/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/layer.js/1.9.3/layer.js"></script>
<script src="../js/BlogLocalStorage.js"></script>

<body ontouchstart="">
    <div class="editor-container">
		<textarea id="editor"><?php if(strlen($readContent) > 0) {echo $readContent;} ?></textarea>
	</div>
</body>

</html>

<script type="text/javascript">
var filename = "<?php echo $filename ?>";
console.log("filename="+filename);

var autosave = false;
var localCachedValue = "";

// 重写autosave，首次加载时不读取localstorage
SimpleMDE.prototype.autosave = function() {
	if(!simpleMDE.options.autosave.enabled) {
		console.log("SimpleMDE: localStorage autosave is not enabled");
		return;
	}

	if(!isLocalStorageAvailable()) {
		console.log("SimpleMDE: localStorage not available, cannot autosave");
		return;
	}

	var simplemde = this;

	if(this.options.autosave.uniqueId == undefined || this.options.autosave.uniqueId == "") {
		console.log("SimpleMDE: You must set a uniqueId to use the autosave feature");
		return;
	}

	if(simplemde.element.form != null && simplemde.element.form != undefined) {
		simplemde.element.form.addEventListener("submit", function() {
			localStorage.removeItem("smde_" + simplemde.options.autosave.uniqueId);
		});
	}

	if(this.options.autosave.loaded !== true) {
		var cacheItem = localStorage.getItem("smde_" + this.options.autosave.uniqueId);
		if(typeof cacheItem == "string" && cacheItem != "") {
			localCachedValue = cacheItem;
		}

		this.options.autosave.loaded = true;
	} else {
		localCachedValue = simplemde.value();
		localStorage.setItem("smde_" + this.options.autosave.uniqueId, localCachedValue);
	}

	var el = $(".autosave");
	if(el != null && el != undefined && el != "") {
		var d = new Date();
		var hh = d.getHours();
		var m = d.getMinutes();
		var s = d.getSeconds();
		var dd = "am";
		var h = hh;
		if(h >= 12) {
			h = hh - 12;
			dd = "pm";
		}
		if(h == 0) {
			h = 12;
		}
		m = m < 10 ? "0" + m : m;

		el.html("Autosaved: " + h + ":" + m + ":" + s +"  "+ dd);
	}

	this.autosaveTimeoutId = setTimeout(function() {
		simplemde.autosave();
	}, this.options.autosave.delay || 10000);
}

// 初始化md editor，toolbar新增了一些button
var simpleMDE = new SimpleMDE({
        element: document.getElementById("editor"),
        spellChecker: false,
        autosave: {
            enabled: autosave,
            unique_id: filename,
            delay:1000,
        },
        autoDownloadFontAwesome: false,
        toolbar: [
	        "bold",
	        "italic",
	        "strikethrough",
	        "heading",
	        "|",
	        "quote",
	        "unordered-list",
	        "ordered-list",
	        "|",
	        "link",
	        "image",
	        "table",
	        "|",
	        "guide",
	        {
	            name: "metadata",
	            action: function customFunction(editor){
                	insertMetaData(editor)
            	},
	            className: "fa fa-file-code-o",
	            title: "Insert Meta-Data",
	        },
	        "|",
	        "side-by-side",
	        "preview",
    		{
	            name: "savetofile",
	            action: function customFunction(editor){
                	toggleAutoSave(editor)
            	},
	            className: "fa fa-save",
	            title: "Insert Meta-Data",
	        },
	        {
	            name: "readlocalStorage",
	            action: function customFunction(editor){
                	readFromLocalStorage(editor)
            	},
	            className: "fa fa-repeat",
	            title: "read from localStorage",
	        },
	        "|",
	        {
	            name: "publish",
	            action: function customFunction(editor){
                	publishBlog(editor)
            	},
	            className: "fa fa-send publish_btn",
	            title: "Publish to Blog",
	        },
    	],
    	insertTexts: {
    		yaml: [""+
    			"---\n"+
    			"layout:     post\n"+
    			"title:      \"\"\n"+
    			"subtitle:   \"\"\n"+
    			"author:     \"jianhua\"\n"+
    			"header-img: \"img/post-bg-2015.jpg\"\n"+
    			"tags:\n"+
    			"    - 生活\n\n"+
    			"---\n"
    			,""]
        },
});

function insertMetaData(editor) {
	var cm = editor.codemirror;
	var stat = editor.getState(cm);
	var options = editor.options;
	console.log(options.insertTexts.yaml)
	replaceSelection(cm, options.insertTexts.yaml);
}

function toggleAutoSave(editor) {
	autosave != autosave;
	if(autosave) {
		simpleMDE.options.autosave.enabled = true;
		simpleMDE.autosave();
		layer.msg('开始本地自动保存');
	} else {
		simpleMDE.options.autosave.enabled = false;
		layer.msg('已停止本地自动保存');
	}
}

function publishBlog(editor) {
	var index = layer.load(1);

	var publishContent = editor.value();
	$.post("action.php?action=publish", {"blog":publishContent, "filename":filename},
		function callback(data, status) {
			if(status) {
				var responseObject = $.parseJSON(data);
				layer.msg(responseObject.msg);
			} else {
				layer.msg("网络请求失败");
			}
			
			layer.close(index);
		});
}

function readFromLocalStorage(editor) {
	if(!autosave) {
		var cacheItem = localStorage.getItem("smde_" + editor.options.autosave.uniqueId);
		if(typeof cacheItem == "string" && cacheItem != "") {
			localCachedValue = cacheItem;
		}
	}

	if(localCachedValue.length > 0) {
		editor.value(localCachedValue);
		layer.msg('加载本地缓存成功，已替换文本内容');
	} else {
		layer.msg('没有本地缓存可用');
	}
}

function replaceSelection(cm, replaceText) {
	if(/editor-preview-active/.test(cm.getWrapperElement().lastChild.className))
		return;

	var text;
	var start = replaceText[0];
	var end = replaceText[1];
	var startPoint = cm.getCursor("start");
	var endPoint = cm.getCursor("end");
	text = cm.getSelection();
	cm.replaceSelection(start + text + end);

	startPoint.ch += start.length;
	if(startPoint !== endPoint) {
		endPoint.ch += start.length;
	}
	
	cm.setSelection(startPoint, endPoint);
	cm.focus();
}

function isLocalStorageAvailable() {
	if(typeof localStorage === "object") {
		try {
			localStorage.setItem("smde_localStorage", 1);
			localStorage.removeItem("smde_localStorage");
			return true;
		} catch(e) {
			console.log(e);
		}
	}
	return false;
}
</script>

<style type="text/css">

.publish_btn {
	font-size: 20px;
	background-color: rgba(224,224,224,0.8);
}

</style>

