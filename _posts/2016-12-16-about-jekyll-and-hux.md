---
layout:     post
title:      "折腾博客记"
subtitle:   " \"除了程序员，这种博客应该没人用\""
author:     "jianhua"
header-img: "img/post-bg-2015.jpg"
categories: jekyll
tags: [生存技能, jekyll]
---

> 本博客用 Jekyll 搭建，非常轻量，会用 git 是唯一的要求。我曾经用过 WordPress，你想想我的感动

## jekyll
[jekyll 中文网站](http://jekyllcn.com/)

Jekyll 是一个可快速搭建的博客框架，在 [Github](https://github.com/jekyll/jekyll) 上开源。哲学是 “专注于真正重要的事情：内容”，因此功能 “简陋” 得出奇：将 Markdown 格式的文本编译成 HTML。服务部署容易，将编译后的一堆 HTML 文件，传到服务器就行。

## 折腾
### 皮肤
Jekyll 只是个编译工具，像 “博客聚合/分页”、“博客阅读页” 等都没有，因此首先要折腾这些。

Jekyll 支持编译 [Liquid](http://www.cnblogs.com/lslvxy/p/3651936.html) 模板语言，提供了必要的变量可访问。开发起来简单，90% 的时间专注于前端开发，10% 的时间用于嵌入 Jekyll 的变量。像这样:

{% highlight html %}
{% raw %}
{% for post in paginator.posts %}
<div class="post-preview">
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
    </p>
</div>
<hr>
{% endfor %}
{% endraw %}
{% endhighlight %}
```

我几乎完全失去了前端能力，nav 这种在我看来算 “新标签”，你想想。平均写 5 行，google 一次语法。Google 的过程中发现有一些开发好的模板，其中比较好看的是 [Hux](https://huangxuan.me/)。

> 题外话是，搜索 “strlen(string)、string.length、string.len” 语法等具体的问题中，居然也能看到各类 JS 框架的争论。前端社区真神奇，这么多年，还保持高昂的热情，重复创造相同又不同的车轮。

Hux 这个博客里，不但做完了必要的博客页面(Index、阅读页、Tags)，还有 About、sidebar 个人简介、友情链接等精力过剩的产出物。

因此，fork 后，已经有了个高颜值起点的博客了。注意用[这个项目](https://github.com/Huxpro/huxblog-boilerplate)，而不是[项目首页](https://github.com/Huxpro/huxpro.github.io)，后者是项目作者目前在用的，拿下来了后得删除一堆他的博客和图片。


### 实践
#### 用 Git 管理
把项目放在 [Github](https://github.com/RealJianhua/huxblog-boilerplate) 上，但这样所有人都能轻易 clone 整个站点，不过我自己觉得无所谓。其他选择是 [git.oschina](git.oschina.com)，可以免费建私密项目。

在个人电脑里放一个分支，Markdown 写完后，上传到 github。服务端分支用 Git Hook 自动更新。这里有一个选择：

1. 电脑上用 Jekyll 编译好，直接上传最终的 HTML 文件 
2. 电脑不编译，上传 Markdown 文件，服务端更新后自己用 Jekyll 编译

我用的 [2]，可能是个随机的选择或有别的玄机。

#### Admin 模块管理
Jekyll 本身是用 ruby 开发，本想刁难一下自己的学习能力，用 ruby 来写 admin。后来还是用 php 算了。

这个模块没有数据库读写，只对服务器的 Markdown 文件增删改查。这里也有一个选择：

1. 文件增改删，直接在部署的目录进行，然后向 github 自动提交
2. 额外拉一个分支，供 admin 删改文件，向 github 提交。实际部署的分支，走 Hook 来更新

我用的 [2]，可能自动跳过了一些坑。

#### 编辑器
admin 模块的在线编辑器是 [Simplemde](https://github.com/NextStepWebs/simplemde-markdown-editor)，体验不错，自定义程度高。

本地编辑器，用 [MacDown](http://macdown.uranusjr.com/)，是开源项目，我觉得比 Mou 好。

#### 备份
善意的提醒，因为折腾不止的缘故，哪一天 git 命令敲错了，博客文件就没了。记得每天备份。

### 然而并不会经常更新