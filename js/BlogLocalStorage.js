function BlogLocalStorage() {}
BlogLocalStorage.prototype.getItem = function(saveName, isDraft, callback) {
	console.log("read->"+saveName)
	// var xhr = new XMLHttpRequest();
 //    xhr.onload = function () {
 //        console.log("xhr.responseText");
 //        console.log(xhr.responseText)
 //    };
 //    try {
 //        xhr.open("get", saveName, true);
 //        xhr.send();
 //    }
 //    catch (ex) {
 //        console.log("error");
 //        console.log("ex.message");
 //    }    

 	var f = new File("1.txt","text/html");
 	var reader = new FileReader();
 	reader.onload = (function(file) {
            return function(e) {
                console.log("load end");
                console.log(this.result);
            };
        })(f);

 	reader.readAsText(f);
}

BlogLocalStorage.prototype.saveBlog = function(saveName, isDraft) {
	
}