# gitHook

> 当进行开发的环境在本地，而运行的环境要在服务端时，每一次提交代码都需要在服务端pull一次。而利用git的hooks功能，能够让我们省去这一步，下面我就以github的webhooks为例，实现服务端的代码自动同步部署。

## 了解 git 的 hooks

### 关于 git 钩子

Git 能在特定的重要动作发生时触发自定义脚本。 有两组这样的钩子：客户端的和服务器端的。 客户端钩子由诸如提交和合并这样的操作所调用，而服务器端钩子作用于诸如接收被推送的提交这样的联网操作。 你可以随心所欲地运用这些钩子。  

### 如何使用钩子

钩子都被存储在 Git 目录下的 ==hooks== 子目录中。 也即绝大部分项目中的 ==.git/hooks== 。 当你用 ==git init== 初始化一个新版本库时，Git 默认会在这个目录中放置一些示例脚本。这些脚本除了本身可以被调用外，它们还透露了被触发时所传入的参数。 所有的示例都是 shell 脚本，其中一些还混杂了 Perl 代码，不过，任何正确命名的可执行脚本都可以正常使用 —— 你可以用 Ruby 或 Python，或其它语言编写它们。 这些示例的名字都是以 ==.sample== 结尾，如果你想启用它们，得先移除这个后缀。

把一个正确命名且可执行的文件放入 Git 目录下的 hooks 子目录中，即可激活该钩子脚本。 这样一来，它就能被 Git 调用。 接下来，我们会讲解常用的钩子脚本类型。

具体使用可以参考官方文档：[Git Hookes](https://git-scm.com/book/en/v2/Customizing-Git-Git-Hooks)

## 了解 webhooks

钩子功能（callback），是帮助用户 push 了代码后，自动回调一个你设定的 http 地址。 这是一个通用的解决方案，用户可以自己根据不同的需求，来编写自己的脚本程序（比如发邮件，自动部署等）；目前，webhooks 支持多种触发方式，支持复选。

webhooks 的请求方式为POST请求，有两种数据格式可以选择，JSON 和 web 的 form参数，可以自行选择是否使用密码来确定请求。（注意：该密码是明文)

不同托管平台的POST数据格式都不太一样，不过也不会有太大影响，只是解析数据的时候注意就行了，下面是git的 `Push` 操作回调的部分 `json` 数据：

```
{
  "ref": "refs/heads/master",
  "before": "b111263b14230e2276922a3d1f6e585c771db75e",
  "after": "3611f77d00bc8f6294e5cb39731010db1478ca20",
  "created": false,
  "deleted": false,
  "forced": false,
  "base_ref": null,
  "compare": "https://github.com/AndyYoungCN/githook/compare/b111263b1423...3611f77d00bc",
  "commits": [
    {
      "id": "3611f77d00bc8f6294e5cb39731010db1478ca20",
      "tree_id": "221e39b970eba9b71b0b20f1061fa0b9c78f43e0",
      "distinct": true,
      "message": "修改日志级别",
      "timestamp": "2019-04-26T16:17:48+08:00",
      "url": "https://github.com/AndyYoungCN/githook/commit/3611f77d00bc8f6294e5cb39731010db1478ca20",
      "author": {
        "name": "andyoung",
        "email": "1218853253@qq.com",
        "username": "AndyYoungCN"
      },
      "committer": {
        "name": "andyoung",
        "email": "1218853253@qq.com",
        "username": "AndyYoungCN"
      },
      "added": [

      ],
      "removed": [

      ],
      "modified": [
        "README.md",
        "pull.php"
      ]
    }
  ],
  "head_commit": {
    "id": "3611f77d00bc8f6294e5cb39731010db1478ca20",
    "tree_id": "221e39b970eba9b71b0b20f1061fa0b9c78f43e0",
    "distinct": true,
    "message": "修改日志级别",
    "timestamp": "2019-04-26T16:17:48+08:00",
    "url": "https://github.com/AndyYoungCN/githook/commit/3611f77d00bc8f6294e5cb39731010db1478ca20",
    "author": {
      "name": "andyoung",
      "email": "1218853253@qq.com",
      "username": "AndyYoungCN"
    },
    "committer": {
      "name": "andyoung",
      "email": "1218853253@qq.com",
      "username": "AndyYoungCN"
    },
    "added": [

    ],
    "removed": [

    ],
    "modified": [
      "README.md",
      "pull.php"
    ]
  },
  "repository": {
    "id": 183569523,
    "node_id": "MDEwOlJlcG9zaXRvcnkxODM1Njk1MjM=",
    "name": "githook",
    "full_name": "AndyYoungCN/githook",
    "private": false,
    "owner": {
      "name": "AndyYoungCN",
      "email": "1218853253@qq.com",
      "login": "AndyYoungCN",
      "id": 13751605,
      "node_id": "MDQ6VXNlcjEzNzUxNjA1",
      "avatar_url": "https://avatars0.githubusercontent.com/u/13751605?v=4",
      "gravatar_id": "",
      "url": "https://api.github.com/users/AndyYoungCN",
      "html_url": "https://github.com/AndyYoungCN",
      "followers_url": "https://api.github.com/users/AndyYoungCN/followers",
      "following_url": "https://api.github.com/users/AndyYoungCN/following{/other_user}",
      "gists_url": "https://api.github.com/users/AndyYoungCN/gists{/gist_id}",
      "starred_url": "https://api.github.com/users/AndyYoungCN/starred{/owner}{/repo}",
      "subscriptions_url": "https://api.github.com/users/AndyYoungCN/subscriptions",
      "organizations_url": "https://api.github.com/users/AndyYoungCN/orgs",
      "repos_url": "https://api.github.com/users/AndyYoungCN/repos",
      "events_url": "https://api.github.com/users/AndyYoungCN/events{/privacy}",
      "received_events_url": "https://api.github.com/users/AndyYoungCN/received_events",
      "type": "User",
      "site_admin": false
    },
    "html_url": "https://github.com/AndyYoungCN/githook",
    "description": "利用webhook完成自动部署。兼容码云、Coding、GitHub、Gogs",
   
    # more...
}
```
其他的具体数据可以到各个官网查看：[码云](http://git.mydoc.io/?t=154711#text_154711)、[Coding](https://open.coding.net/webhooks/)、[GitHub](https://developer.github.com/webhooks/)、[Gogs](https://github.com/gogs/gogs/blob/master/README_ZH.md)


## Use（使用步骤）

1. git clone git@github.com:AndyYoungCN/githook.git  放在目标仓库同个服务器上，最好同个目录；
2. 配置目录权限
    ```
    ## 如果需要日志, 修改日志权限
    mkdir logs
    chmod 777 -R logs
    ```
3. 修改目录权限
    
    ```
    chown -R www-data /var/www/githook # 这里请改成你创建的hook目录
    chown -R www-data /var/www/Project # 这里请改成你的项目目录
    ```
4. 设置目标仓库webhook
![webhook](https://img-blog.csdnimg.cn/20190426163010700.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L2Fnb25pZTIwMTIxOA==,size_16,color_FFFFFF,t_70)

 设置URL  
```URL:http://<domain>/githook/pull.php?[log_level=false][&path=xxx]```
* `log_level` 是否日志等级；默认0：关闭日志；15 打开所有日志；打开日志需要给`logs`目录写的权限
* `path` 仓库位置；默认 '../{repositoryname}',`repositoryname`:仓库名；


> 目标仓库必须要设置用户名密码或者SSH(让`git pull`命令可以直接执行 )  
> 最终将会执行`cd {$path} && git pull` 命令


### 注意事项

如果配置都没有问题，但是就是不会自动拉取，那应该是用户的权限配置问题，可以先查看运行php代码的具体用户是什么，然后为该用户开启权限。

```$xslt
system("whoami"); // 查看是哪个用户执行该命令
```


## License

[MIT](https://github.com/AndyYoungCN/githook/blob/master/LICENSE)

Copyright (c) 2019-present andyoung