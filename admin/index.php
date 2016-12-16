---
layout: admin
description: ""
header-img: "img/home-bg.jpg"
title: Admin
---
<script src="https://cdn.staticfile.org/jquery/3.1.1/jquery.min.js"></script>
<script src="{{ "/js/layer.js" | prepend: site.baseurl }}"></script>

<?php 
if(!isset($_SESSION["username"])) {
?>

<script type="text/javascript">
layer.open({
    type: 1,
    content: '<div style="padding:20px">登录名：<input formType="text" id="username"/> <br/>&nbsp;&nbsp;&nbsp;密码：<input formType="password" lable="密码" id="password"/></div>',
    title: "登录",
    closeBtn: false,
    btn: ["登录"],
    shade: 0.8,
    yes: function(index, layero) {
        console.log(layero);
        username = $(layero).find("#username").val();
        password = $(layero).find("#password").val();
        if(username.trim().length > 0 && password.trim().length > 0) {
            login(username, password);
        }
    }
});

function login(username, password) {
    var index = layer.load(1);
    $.post("login.php", {"username":username, "password":password},
        function callback(data, status) {
            layer.close(index);
            if(status) {
                var responseObject = $.parseJSON(data);
                layer.msg(responseObject.msg);
                if(responseObject.ok) {
                    location.reload(true);
                }
            } else {
                layer.msg("网络请求失败");
            }
        });
}

</script>

<?php 
exit;
}
?>
<!-- Page Header -->
<header class="intro-header" style="background-image: url('{{ site.baseurl }}/{% if page.header-img %}{{ page.header-img }}{% else %}{{ site.header-img }}{% endif %}')">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="site-heading" id="tag-heading">
                    <h1>{% if page.title %}{{ page.title }}{% else %}{{ site.title }}{% endif %}</h1>
                    <span class="subheading">{{ page.description }}</span>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- <div style="background-color:blue;padding:20px">登录名：<input formType="text" /> <br/>&nbsp;&nbsp;&nbsp;密码：<input formType="password" lable="密码"/></div> -->


<a href="#" title="新建" class="fa fa-plus" style="margin-left:20px;" id="blog_new">新建</a>
<a href="#"  id="jumper" style="visibility:hidden">#</a>

<!-- Main Content -->
<div class="container">
    <div class="row">
        <div>
            {% for post in site.posts %}
            <div class="post-preview" id="post-preview-{{ forloop.index }}">
                <a href="{{ post.url | prepend: site.baseurl }}">
                    <h2 class="post-title">
                        {{ post.title }}
                    </h2>
                    {% if post.subtitle %}
                    <h3 class="post-subtitle">
                        {{ post.subtitle }}
                    </h3>
                    {% endif %}
                    <div class="post-content-preview">
                        {{ post.content | strip_html | truncate:200 }}
                    </div>
                </a>
                <p class="post-meta">
                    {% if post.author %}{{ post.author }}{% else %}{{ site.title }}{% endif %} . {{ post.date | date: "%B %-d, %Y" }}
                    <a href="{{ '/admin/editor.php?editpath=' | prepend: site.baseurl  | append: post.path}}" title="编辑" class="fa fa-edit" style="margin-left:20px;" id="blog_edit">编辑</a>
                    <a href="#{{ post.path }}" filename="{{ post.path }}" title="删除" class="fa fa-trash-o" style="margin-left:20px;" id="blog_del_{{ forloop.index }}" forindex="{{ forloop.index }}" >删除</a>
                </p>
                <hr>
            </div> 
            
            {% endfor %}
        </div>

    </div>
</div>


<script type="text/javascript">
$("#blog_new").click(function() {
    var d = new Date();
    var defaultName = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate()+"-";
    layer.prompt(
        {title: '输入文件名', formType: 0, value: defaultName}, 
        function(value, index){
            layer.close(index);
            if(value.length == 0 || value == defaultName) {
                layer.msg("这可不行");
            } else {
                 createNewBlog(value);
             }
        }); 
});

$("a[id^='blog_del']").click(function(event) {
    var name = $(this).attr("filename");
    console.log("name="+name);
    deleteBlog(name, $(this).attr("forindex"));
});

function createNewBlog(name) {
    var index = layer.load(1);
    if(!name.endsWith(".md") && !name.endsWith(".markdown") && !name.endsWith(".html")) {
        name += ".md";
    }

    name = "_posts/"+name;

    $.get("action.php?action=newfile&filename="+name,
        function callback(data, status) {
            if(status) {
                var responseObject = $.parseJSON(data);
                layer.msg(responseObject.msg);
                if(responseObject.ok) {
                    // $("#jumper").attr("href", "{{ '/admin/vue.php?editpath=' | prepend: site.baseurl }}"+responseObject.name);
                    // $("#jumper").click();
                    // document.getElementById("jumper").click();
                    
                    setTimeout(function() { location.reload(true); },  1000);
                }
            } else {
                layer.msg("网络请求失败");
            }
            layer.close(index);
        });
}

function deleteBlog(name, viewIndex) {
    var index = layer.load(1);
    $.get("action.php?action=delete&filename="+name,
        function callback(data, status) {
            if(status) {
                var responseObject = $.parseJSON(data);
                layer.msg(responseObject.msg);
                $("#post-preview-"+viewIndex).remove();
            } else {
                layer.msg("网络请求失败");
            }
            layer.close(index);
        });
}
</script>

